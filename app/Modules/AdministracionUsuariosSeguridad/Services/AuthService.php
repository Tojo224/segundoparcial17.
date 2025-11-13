<?php

namespace App\Modules\AdministracionUsuariosSeguridad\Services;

use Illuminate\Support\Facades\Auth;
use App\Modules\AdministracionUsuariosSeguridad\Models\Usuario;

class AuthService
{
    public function __construct(
        protected BitacoraService $bitacora
    ) {}

    /**
     * Intentar iniciar sesión con correo + password, solo usuarios activos.
     */
    public function attempt(string $correo, string $password, bool $remember = false): bool
    {
        $ok = Auth::attempt([
            'correo' => $correo,
            'estado' => true,
            'password' => $password,
        ], $remember);

        if ($ok) {
            /** @var Usuario $user */
            $user = Auth::user();
            if ($user) {
                $this->bitacora->registrar('login', (int) $user->id_usuario);
            }
        }

        return $ok;
    }

    /**
     * Cerrar sesión e invalidar la sesión.
     */
    public function logout(): void
    {
        /** @var Usuario|null $user */
        $user = Auth::user();
        if ($user) {
            $this->bitacora->registrar('logout', (int) $user->id_usuario);
        }

        Auth::logout();
    }

    /**
     * Devolver ruta de redirección según rol.
     */
    public function redirectPathFor(Usuario $user): string
    {
        $roleName = optional($user->rol)->nombre;

        return match ($roleName) {
            'Administrador' => '/admin',
            'Decano' => '/decano',
            'Vicedecano' => '/vicedecano',
            'Director' => '/director',
            'Docente' => '/docente',
            default => '/home',
        };
    }
}

