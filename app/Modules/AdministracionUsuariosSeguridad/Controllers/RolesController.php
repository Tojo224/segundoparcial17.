<?php

namespace App\Modules\AdministracionUsuariosSeguridad\Controllers;

use App\Modules\AdministracionUsuariosSeguridad\Services\RolesService;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Validator;

class RolesController extends Controller
{
    public function __construct(
        protected RolesService $rolesService
    ) {}

    /**
     * Listar roles con paginación y filtros
     */
    public function index(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $filters = $request->only(['nombre']);

        $roles = $this->rolesService->paginate($perPage, $filters);

        return response()->json([
            'success' => true,
            'data' => $roles
        ]);
    }

    /**
     * Mostrar un rol específico
     */
    public function show($id): JsonResponse
    {
        $rol = $this->rolesService->find($id);

        if (!$rol) {
            return response()->json([
                'success' => false,
                'message' => 'Rol no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $rol
        ]);
    }

    /**
     * Crear un nuevo rol
     */
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'nombre' => 'required|string|max:50|unique:roles,nombre',
            'descripcion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $rol = $this->rolesService->create($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Rol creado exitosamente',
            'data' => $rol
        ], 201);
    }

    /**
     * Actualizar un rol
     */
    public function update(Request $request, $id): JsonResponse
    {
        $rol = $this->rolesService->find($id);
        if (!$rol) {
            return response()->json([
                'success' => false,
                'message' => 'Rol no encontrado'
            ], 404);
        }

        $validator = Validator::make($request->all(), [
            'nombre' => 'string|max:50|unique:roles,nombre,' . $id . ',id_rol',
            'descripcion' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'errors' => $validator->errors()
            ], 422);
        }

        $rol = $this->rolesService->update($id, $request->all());

        return response()->json([
            'success' => true,
            'message' => 'Rol actualizado exitosamente',
            'data' => $rol
        ]);
    }

    /**
     * Eliminar rol
     */
    public function destroy($id): JsonResponse
    {
        if (!$this->rolesService->delete($id)) {
            return response()->json([
                'success' => false,
                'message' => 'Rol no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'message' => 'Rol eliminado exitosamente'
        ]);
    }
}
