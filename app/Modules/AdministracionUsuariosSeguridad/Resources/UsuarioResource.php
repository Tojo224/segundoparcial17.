<?php

namespace App\Modules\AdministracionUsuariosSeguridad\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UsuarioResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray(Request $request): array
    {
        return [
            'id_usuario' => $this->id_usuario,
            'ci' => $this->ci,
            'nombre' => $this->nombre,
            'correo' => $this->correo,
            'estado' => (bool) $this->estado,
            'rol' => $this->whenLoaded('rol', function () {
                return [
                    'id_rol' => $this->rol->id_rol,
                    'nombre' => $this->rol->nombre,
                ];
            }),
        ];
    }
}

