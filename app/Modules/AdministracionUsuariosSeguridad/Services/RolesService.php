<?php

namespace App\Modules\AdministracionUsuariosSeguridad\Services;

use App\Modules\AdministracionUsuariosSeguridad\Models\Rol;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class RolesService
{
    public function all(array $filters = []): Collection
    {
        $query = Rol::query();

        if (!empty($filters['nombre'])) {
            $query->where('nombre', 'like', '%' . $filters['nombre'] . '%');
        }

        return $query->get();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Rol::query();

        if (!empty($filters['nombre'])) {
            $query->where('nombre', 'like', '%' . $filters['nombre'] . '%');
        }

        return $query->paginate($perPage);
    }

    public function find(mixed $id): ?Rol
    {
        return Rol::find($id);
    }

    public function create(array $data): Rol
    {
        return Rol::create($data);
    }

    public function update(mixed $id, array $data): ?Rol
    {
        $rol = $this->find($id);
        if (!$rol) return null;

        $rol->fill($data);
        $rol->save();

        return $rol->fresh();
    }

    public function delete(mixed $id): bool
    {
        $rol = $this->find($id);
        if (!$rol) return false;

        return (bool) $rol->delete();
    }
}
