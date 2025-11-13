<?php

namespace App\Modules\AdministracionUsuariosSeguridad\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use App\Modules\AdministracionUsuariosSeguridad\Services\AuthService;
use App\Modules\AdministracionUsuariosSeguridad\Services\BitacoraService;
use App\Modules\AdministracionUsuariosSeguridad\Models\Usuario;

class LoginController extends Controller
{
    public function __construct(
        protected AuthService $auth
    ) {}

    /**
     * Mostrar formulario de inicio de sesión
     */
    public function showLoginForm()
    {
        return view('administracion_usuarios_seguridad.auth.login');
    }

    /**
     * Iniciar sesión de usuario
     */
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'correo' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        if (!$this->auth->attempt(
            $request->string('correo')->toString(),
            $request->string('password')->toString(),
            $request->boolean('remember')
        )) {
            return back()->withErrors(['correo' => 'Credenciales inválidas o usuario inactivo.'])->withInput();
        }

        $request->session()->regenerate();

        $user = Auth::user();
        
        // Verificar si debe cambiar contraseña (primer login)
        if ($user->cambiar_contra) {
            return redirect()->route('change-password.show');
        }

        $target = $this->auth->redirectPathFor($user);

        return redirect()->intended($target);
    }

    /**
     * Cerrar sesión del usuario y registrar en bitácora
     */
    public function logout(Request $request)
    {
        $bitacora = app(BitacoraService::class);
        $usuario = Auth::user();

        if ($usuario) {
            // Registrar acción de cierre de sesión
            $bitacora->registrar("{$usuario->nombre} cerró sesión en el sistema", $usuario->id_usuario);
        }

        // Cerrar sesión y limpiar sesión
        $this->auth->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Mostrar formulario para cambiar contraseña en primer login
     */
    public function showChangePasswordForm()
    {
        $user = Auth::user();
        
        // Si no debe cambiar contraseña, redirigir al dashboard
        if (!$user || !$user->cambiar_contra) {
            return redirect()->route('home');
        }

        return view('administracion_usuarios_seguridad.auth.change-password');
    }

    /**
     * Procesar cambio de contraseña
     */
    public function storeNewPassword(Request $request)
    {
        $user = Auth::user();

        // Validar que el usuario deba cambiar contraseña
        if (!$user || !$user->cambiar_contra) {
            return redirect()->route('home');
        }

        // Validar datos
        $validator = Validator::make($request->all(), [
            'password' => 'required|string|min:8|regex:/[a-zA-Z]/|regex:/[0-9]/|confirmed',
            'password_confirmation' => 'required|string|min:8',
        ], [
            'password.required' => 'La contraseña es obligatoria.',
            'password.min' => 'La contraseña debe tener mínimo 8 caracteres.',
            'password.regex' => 'La contraseña debe contener letras y números.',
            'password.confirmed' => 'Las contraseñas no coinciden.',
            'password_confirmation.required' => 'Debe confirmar la contraseña.',
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        try {
            // Actualizar contraseña y marcar que ya cambió
            $user->update([
                'contraseña' => bcrypt($request->string('password')->toString()),
                'cambiar_contra' => false,
            ]);

            // Registrar en bitácora
            $bitacora = app(BitacoraService::class);
            $bitacora->registrar(
                "{$user->nombre} cambió su contraseña al primer ingreso",
                $user->id_usuario
            );

            // Logout y redirigir al login
            $this->auth->logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return redirect('/login')->with('success', 'Contraseña cambiad exitosamente. Por favor, inicie sesión nuevamente.');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Error al cambiar la contraseña: ' . $e->getMessage()]);
        }
    }
}
