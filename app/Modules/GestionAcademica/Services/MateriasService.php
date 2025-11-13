<?php

namespace App\Modules\GestionAcademica\Services;

use App\Modules\GestionAcademica\Models\Materia;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class MateriasService
{
    public function all(array $filters = []): Collection
    {
        $q = Materia::query();
        if (!empty($filters['sigla'])) {
            $q->where('sigla', 'like', '%'.$filters['sigla'].'%');
        }
        if (!empty($filters['nombre'])) {
            $q->where('nombre', 'like', '%'.$filters['nombre'].'%');
        }
        return $q->get();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $q = Materia::query();
        if (!empty($filters['sigla'])) {
            $q->where('sigla', 'like', '%'.$filters['sigla'].'%');
        }
        if (!empty($filters['nombre'])) {
            $q->where('nombre', 'like', '%'.$filters['nombre'].'%');
        }
        return $q->paginate($perPage);
    }

    public function find(mixed $id): ?Materia
    {
        return Materia::find($id);
    }

    public function create(array $data): Materia
    {
        return Materia::create($data);
    }

    public function update(mixed $id, array $data): ?Materia
    {
        $m = Materia::find($id);
        if (!$m) return null;
        $m->fill($data);
        $m->save();
        return $m->fresh();
    }

    public function delete(mixed $id): bool
    {
        $m = Materia::find($id);
        if (!$m) return false;
        return (bool) $m->delete();
    }
}

