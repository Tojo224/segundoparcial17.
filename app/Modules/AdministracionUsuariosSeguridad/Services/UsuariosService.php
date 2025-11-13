<?php

namespace App\Modules\AdministracionUsuariosSeguridad\Services;

use App\Modules\AdministracionUsuariosSeguridad\Models\Usuario;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Servicio para operaciones relacionadas con usuarios.
 * Métodos CRUD básicos y utilidades.
 */
class UsuariosService
{
    /**
     * Obtener todos los usuarios (con opcional filtrado simple).
     * @param array $filters
     * @return Collection
     */
    public function all(array $filters = []): Collection
    {
        $query = Usuario::query();

        // Por defecto, solo usuarios activos (estado = true)
        if (!array_key_exists('estado', $filters)) {
            $query->where('estado', true);
        }

        if (!empty($filters['nombre'])) {
            $query->where('nombre', 'like', '%' . $filters['nombre'] . '%');
        }

        if (!empty($filters['correo'])) {
            $query->where('correo', $filters['correo']);
        }

        return $query->get();
    }

    /**
     * Obtener usuario por id.
     */
    public function find(mixed $id): ?Usuario
    {
        return Usuario::find($id);
    }

    /**
     * Paginar usuarios
     * @return LengthAwarePaginator
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Usuario::query();

        // Por defecto, solo usuarios activos (estado = true)
        if (!array_key_exists('estado', $filters)) {
            $query->where('estado', true);
        }

        if (!empty($filters['nombre'])) {
            $query->where('nombre', 'like', '%' . $filters['nombre'] . '%');
        }

        if (!empty($filters['correo'])) {
            $query->where('correo', $filters['correo']);
        }

        return $query->paginate($perPage);
    }

    /**
     * Crear un usuario. Si se pasa 'password' o 'contraseña' se aplicará hash.
     * @param array $data
     * @return Usuario
     */
    public function create(array $data): Usuario
    {
        // Normalizar claves de contraseña
        if (isset($data['password']) && !isset($data['contraseña'])) {
            $data['contraseña'] = $data['password'];
        }

        if (isset($data['contraseña'])) {
            $data['contraseña'] = Hash::make($data['contraseña']);
        }

        return Usuario::create($data);
    }

    /**
     * Actualizar usuario por id
     * @param mixed $id
     * @param array $data
     * @return Usuario|null
     */
    public function update(mixed $id, array $data): ?Usuario
    {
        $usuario = $this->find($id);
        if (!$usuario) {
            return null;
        }

        // Hash de contraseña si se envía
        if (isset($data['password']) && !isset($data['contraseña'])) {
            $data['contraseña'] = $data['password'];
        }

        if (isset($data['contraseña'])) {
            if (!empty($data['contraseña'])) {
                $data['contraseña'] = Hash::make($data['contraseña']);
            } else {
                unset($data['contraseña']);
            }
        }

        $usuario->fill($data);
        $usuario->save();

        return $usuario->fresh();
    }

    /**
     * Eliminar usuario por id
     */
    public function delete(mixed $id): bool
    {
        $usuario = $this->find($id);
        if (!$usuario) {
            return false;
        }

        // Borrado lógico: marcar estado=false
        $usuario->estado = false;
        return (bool) $usuario->save();
    }

    /**
     * Asignar rol (si existe relación rol()).
     */
    public function assignRole(Usuario $usuario, $rolId): Usuario
    {
        if (method_exists($usuario, 'rol')) {
            $usuario->id_rol = $rolId;
            $usuario->save();
        }

        return $usuario->fresh();
    }
}
