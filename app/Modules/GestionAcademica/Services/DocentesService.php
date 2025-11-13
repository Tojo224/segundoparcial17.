<?php

namespace App\Modules\GestionAcademica\Services;

use App\Modules\GestionAcademica\Models\Docente;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class DocentesService
{
    public function all(array $filters = []): Collection
    {
        $q = Docente::query()->with('usuario');
        if (!empty($filters['cod_docente'])) {
            $q->where('cod_docente', 'like', '%'.$filters['cod_docente'].'%');
        }
        if (!empty($filters['carrera'])) {
            $q->where('carrera', 'like', '%'.$filters['carrera'].'%');
        }
        return $q->get();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $q = Docente::query()->with('usuario');
        if (!empty($filters['cod_docente'])) {
            $q->where('cod_docente', 'like', '%'.$filters['cod_docente'].'%');
        }
        if (!empty($filters['carrera'])) {
            $q->where('carrera', 'like', '%'.$filters['carrera'].'%');
        }
        return $q->paginate($perPage);
    }

    public function find(mixed $id): ?Docente
    {
        return Docente::with('usuario')->find($id);
    }

    public function create(array $data): Docente
    {
        return Docente::create($data);
    }

    public function update(mixed $id, array $data): ?Docente
    {
        $doc = Docente::find($id);
        if (!$doc) return null;
        $doc->fill($data);
        $doc->save();
        return $doc->fresh(['usuario']);
    }

    public function delete(mixed $id): bool
    {
        $doc = Docente::find($id);
        if (!$doc) return false;
        return (bool) $doc->delete();
    }
}

