<?php

namespace App\Modules\AulasHorarios\Services;

use App\Modules\AulasHorarios\Models\Aula;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

class AulasService
{
    public function all(array $filters = []): Collection
    {
        $q = Aula::query();
        
        if (!empty($filters['nro_aula'])) {
            $q->where('nro_aula', 'like', '%' . $filters['nro_aula'] . '%');
        }
        
        if (!empty($filters['modulo'])) {
            $q->where('modulo', 'like', '%' . $filters['modulo'] . '%');
        }
        
        return $q->get();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $q = Aula::query();
        
        if (!empty($filters['nro_aula'])) {
            $q->where('nro_aula', 'like', '%' . $filters['nro_aula'] . '%');
        }
        
        if (!empty($filters['modulo'])) {
            $q->where('modulo', 'like', '%' . $filters['modulo'] . '%');
        }
        
        return $q->paginate($perPage);
    }

    public function find(mixed $id): ?Aula
    {
        return Aula::find($id);
    }

    public function create(array $data): Aula
    {
        return Aula::create($data);
    }

    public function update(mixed $id, array $data): ?Aula
    {
        $aula = Aula::find($id);
        if (!$aula) return null;
        $aula->fill($data);
        $aula->save();
        return $aula->fresh();
    }

    public function delete(mixed $id): bool
    {
        $aula = Aula::find($id);
        if (!$aula) return false;
        return (bool) $aula->delete();
    }
}
