<?php

namespace App\Modules\GestionAcademica\Services;

use App\Modules\GestionAcademica\Models\CargaHoraria;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class CargaHorariaService
{
    public function all(array $filters = []): Collection
    {
        $q = CargaHoraria::query()->with(['docente','grupo.materia']);
        if (!empty($filters['id_docente'])) { $q->where('id_docente', $filters['id_docente']); }
        if (!empty($filters['id_grupo'])) { $q->where('id_grupo', $filters['id_grupo']); }
        if (!empty($filters['id_gestion'])) { $q->where('id_gestion', $filters['id_gestion']); }
        return $q->get();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $q = CargaHoraria::query()->with(['docente','grupo.materia']);
        if (!empty($filters['id_docente'])) { $q->where('id_docente', $filters['id_docente']); }
        if (!empty($filters['id_grupo'])) { $q->where('id_grupo', $filters['id_grupo']); }
        if (!empty($filters['id_gestion'])) { $q->where('id_gestion', $filters['id_gestion']); }
        return $q->paginate($perPage);
    }

    public function find(mixed $id): ?CargaHoraria
    {
        return CargaHoraria::with(['docente','grupo.materia'])->find($id);
    }

    public function create(array $data): CargaHoraria
    {
        return CargaHoraria::create($data);
    }

    public function update(mixed $id, array $data): ?CargaHoraria
    {
        $c = CargaHoraria::find($id);
        if (!$c) return null;
        $c->fill($data);
        $c->save();
        return $c->fresh(['docente','grupo.materia']);
    }

    public function delete(mixed $id): bool
    {
        $c = CargaHoraria::find($id);
        if (!$c) return false;
        return (bool) $c->delete();
    }
}

