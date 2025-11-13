<?php

namespace App\Modules\GestionAcademica\Services;

use App\Modules\GestionAcademica\Models\Grupo;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class GruposService
{
    public function all(array $filters = []): Collection
    {
        $q = Grupo::query()->with('materia');
        if (!empty($filters['codigo'])) {
            $q->where('codigo', 'like', '%'.$filters['codigo'].'%');
        }
        if (array_key_exists('estado', $filters)) {
            $q->where('estado', (bool) $filters['estado']);
        }
        if (!empty($filters['id_materia'])) {
            $q->where('id_materia', $filters['id_materia']);
        }
        return $q->get();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $q = Grupo::query()->with('materia');
        if (!empty($filters['codigo'])) {
            $q->where('codigo', 'like', '%'.$filters['codigo'].'%');
        }
        if (array_key_exists('estado', $filters)) {
            $q->where('estado', (bool) $filters['estado']);
        }
        if (!empty($filters['id_materia'])) {
            $q->where('id_materia', $filters['id_materia']);
        }
        // Buscar por nombre o sigla de materia
        if (!empty($filters['buscar_materia'])) {
            $q->whereHas('materia', function ($query) use ($filters) {
                $query->where('nombre', 'like', '%'.$filters['buscar_materia'].'%')
                      ->orWhere('sigla', 'like', '%'.$filters['buscar_materia'].'%');
            });
        }
        return $q->paginate($perPage);
    }

    public function find(mixed $id): ?Grupo
    {
        return Grupo::with('materia')->find($id);
    }

    public function create(array $data): Grupo
    {
        return Grupo::create($data);
    }

    public function update(mixed $id, array $data): ?Grupo
    {
        $g = Grupo::find($id);
        if (!$g) return null;
        $g->fill($data);
        $g->save();
        return $g->fresh(['materia']);
    }

    public function delete(mixed $id): bool
    {
        $g = Grupo::find($id);
        if (!$g) return false;
        return (bool) $g->delete();
    }
}

