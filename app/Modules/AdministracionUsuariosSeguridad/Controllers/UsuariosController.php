<?php

namespace App\Modules\AdministracionUsuariosSeguridad\Controllers;

use App\Modules\AdministracionUsuariosSeguridad\Services\UsuariosService;
use App\Modules\AdministracionUsuariosSeguridad\Services\BitacoraService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

class UsuariosController extends Controller
{
    public function __construct(
        protected UsuariosService $usuariosService,
        protected BitacoraService $bitacoraService
    ) {}

    /**
     * Listar usuarios con filtros opcionales
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $filters = $request->only(['nombre', 'correo']);

        $usuarios = $this->usuariosService->paginate($perPage, $filters);

        return response()->json([
            'success' => true,
            'data' => $usuarios
        ]);
    }

    /**
     * Ver detalle de usuario
     */
    public function show($id): JsonResponse
    {
        $usuario = $this->usuariosService->find($id);

        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $usuario
        ]);
    }

    /**
     * Crear usuario nuevo
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'CI' => 'required|unique:usuario,CI',
            'nombre' => 'required|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'correo' => 'required|email|unique:usuario,correo',
            'sexo' => 'required|in:M,F',
            'estado_civil' => 'required|string|max:20',
            'estado' => 'required|boolean',
            'contraseña' => 'required|min:6',
            'id_rol' => 'required|exists:roles,id_rol'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $usuario = $this->usuariosService->create($request->all());

        // Registrar acción en bitácora
        $usuarioActual = Auth::user();
        $this->bitacoraService->registrar(
            "{$usuarioActual->nombre} creó un nuevo usuario: {$usuario->nombre}",
            $usuarioActual->id_usuario
        );

        return response()->json([
            'success' => true,
            'message' => 'Usuario creado exitosamente',
            'data' => $usuario
        ], 201);
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, $id): JsonResponse
    {
        $usuario = $this->usuariosService->find($id);
        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'CI' => 'unique:usuario,CI,' . $id . ',id_usuario',
            'nombre' => 'string|max:255',
            'telefono' => 'nullable|string|max:20',
            'direccion' => 'nullable|string|max:255',
            'correo' => 'email|unique:usuario,correo,' . $id . ',id_usuario',
            'sexo' => 'in:M,F',
            'estado_civil' => 'string|max:20',
            'estado' => 'boolean',
            'contraseña' => 'nullable|min:6',
            'id_rol' => 'exists:roles,id_rol'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $usuarioActualizado = $this->usuariosService->update($id, $request->all());

        // Registrar acción en bitácora
        $usuarioActual = Auth::user();
        $this->bitacoraService->registrar(
            "{$usuarioActual->nombre} actualizó el usuario: {$usuarioActualizado->nombre}",
            $usuarioActual->id_usuario
        );

        return response()->json([
            'success' => true,
            'message' => 'Usuario actualizado exitosamente',
            'data' => $usuarioActualizado
        ]);
    }

    /**
     * Eliminar usuario
     */
    public function destroy($id): JsonResponse
    {
        $usuario = $this->usuariosService->find($id);
        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $this->usuariosService->delete($id);

        // Registrar acción en bitácora
        $usuarioActual = Auth::user();
        $this->bitacoraService->registrar(
            "{$usuarioActual->nombre} desactivó el usuario: {$usuario->nombre}",
            $usuarioActual->id_usuario
        );

        return response()->json([
            'success' => true,
            'message' => 'Usuario desactivado exitosamente'
        ]);
    }

    /**
     * Cambiar rol de usuario
     */
    public function assignRole(Request $request, $id): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id_rol' => 'required|exists:roles,id_rol'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $usuario = $this->usuariosService->find($id);
        if (!$usuario) {
            return response()->json([
                'success' => false,
                'message' => 'Usuario no encontrado'
            ], 404);
        }

        $usuario = $this->usuariosService->assignRole($usuario, $request->id_rol);

        // Registrar acción en bitácora
        $usuarioActual = Auth::user();
        $this->bitacoraService->registrar(
            "{$usuarioActual->nombre} cambió el rol del usuario: {$usuario->nombre}",
            $usuarioActual->id_usuario
        );

        return response()->json([
            'success' => true,
            'message' => 'Rol asignado exitosamente',
            'data' => $usuario
        ]);
    }

    /**
     * Vista Laravel para Gestión de Usuarios
     */
    public function vistaUsuarios()
    {
        $usuarios = $this->usuariosService->all();
        return view('usuarios', compact('usuarios'));
    }
}
