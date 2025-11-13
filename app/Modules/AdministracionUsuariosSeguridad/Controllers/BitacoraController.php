<?php

namespace App\Modules\AdministracionUsuariosSeguridad\Controllers;

use App\Modules\AdministracionUsuariosSeguridad\Services\BitacoraService;
use Illuminate\Http\JsonResponse;
use App\Http\Controllers\Controller;

class BitacoraController extends Controller
{
    public function __construct(
        protected BitacoraService $bitacoraService
    ) {}

    /**
     * Listar registros de bitácora
     */
    public function index(): JsonResponse
    {
        $bitacoras = $this->bitacoraService->listar();
        return response()->json([
            'success' => true,
            'data' => $bitacoras
        ]);
    }

    /**
     * Ver detalle de una entrada de bitácora
     */
    public function show($id): JsonResponse
    {
        $bitacora = $this->bitacoraService->obtenerPorId($id);

        if (!$bitacora) {
            return response()->json([
                'success' => false,
                'message' => 'Registro no encontrado'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => $bitacora
        ]);
    }
    public function vistaBitacora()
    {
        $bitacoras = $this->bitacoraService->listar(30);
        return view('bitacora', compact('bitacoras'));
    }

}
