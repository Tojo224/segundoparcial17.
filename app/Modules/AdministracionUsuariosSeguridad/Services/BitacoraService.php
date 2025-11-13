<?php

namespace App\Modules\AdministracionUsuariosSeguridad\Services;

use App\Modules\AdministracionUsuariosSeguridad\Models\Bitacora;
use Illuminate\Support\Facades\Request;
use Carbon\Carbon;

class BitacoraService
{
    /**
     * Registrar acción en bitácora
     */
    public function registrar(string $accion, int $idUsuario): Bitacora
    {
        return Bitacora::create([
            'accion' => $accion,
            'fecha' => Carbon::now()->toDateString(),
            'hora' => Carbon::now()->toTimeString(),
            'ip' => Request::ip(),
            'id_usuario' => $idUsuario,
        ]);
    }

    /**
     * Listar todas las acciones (con paginación opcional)
     */
    public function listar(int $perPage = 15)
    {
        return Bitacora::with('usuario')
            ->orderByDesc('fecha')
            ->orderByDesc('hora')
            ->paginate($perPage);
    }

    /**
     * Obtener detalle de una acción
     */
    public function obtenerPorId(int $id): ?Bitacora
    {
        return Bitacora::with('usuario')->find($id);
    }
}
