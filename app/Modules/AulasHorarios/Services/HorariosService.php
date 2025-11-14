<?php

namespace App\Modules\AulasHorarios\Services;

use App\Modules\AulasHorarios\Models\Horario;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class HorariosService
{
    public function all(array $filters = []): Collection
    {
        $q = Horario::query()->with(['carga.docente.usuario', 'carga.grupo.materia', 'aula']);
        
        if (!empty($filters['dia'])) {
            $q->where('dia', $filters['dia']);
        }
        
        if (!empty($filters['id_carga'])) {
            $q->where('id_carga', $filters['id_carga']);
        }
        
        if (!empty($filters['id_aula'])) {
            $q->where('id_aula', $filters['id_aula']);
        }
        
        return $q->orderBy('dia')->orderBy('hora_i')->get();
    }

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $q = Horario::query()->with(['carga.docente.usuario', 'carga.grupo.materia', 'aula']);
        
        if (!empty($filters['dia'])) {
            $q->where('dia', $filters['dia']);
        }
        
        if (!empty($filters['id_carga'])) {
            $q->where('id_carga', $filters['id_carga']);
        }
        
        if (!empty($filters['id_aula'])) {
            $q->where('id_aula', $filters['id_aula']);
        }
        
        return $q->orderBy('dia')->orderBy('hora_i')->paginate($perPage);
    }

    public function find(mixed $id): ?Horario
    {
        return Horario::with(['carga.docente.usuario', 'carga.grupo.materia', 'aula'])->find($id);
    }

    public function create(array $data): Horario
    {
        return Horario::create($data);
    }

    public function update(mixed $id, array $data): ?Horario
    {
        $horario = Horario::find($id);
        if (!$horario) return null;
        $horario->fill($data);
        $horario->save();
        return $horario->fresh(['carga.docente.usuario', 'carga.grupo.materia', 'aula']);
    }

    public function delete(mixed $id): bool
    {
        $horario = Horario::find($id);
        if (!$horario) return false;
        return (bool) $horario->delete();
    }

    /**
     * Verificar conflictos de horario
     * Retorna array de conflictos encontrados
     */
    public function verificarConflictos(string $dia, string $horaInicio, string $horaFin, ?int $idAula = null, ?int $idCarga = null, ?int $idHorarioExcluir = null): array
    {
        $conflictos = [];

        // 1. Conflicto de aula (misma aula, mismo día y horario)
        if ($idAula) {
            $conflictoAula = Horario::where('id_aula', $idAula)
                ->where('dia', $dia)
                ->where(function ($q) use ($horaInicio, $horaFin) {
                    $q->whereBetween('hora_i', [$horaInicio, $horaFin])
                      ->orWhereBetween('hora_f', [$horaInicio, $horaFin])
                      ->orWhere(function ($q2) use ($horaInicio, $horaFin) {
                          $q2->where('hora_i', '<=', $horaInicio)
                             ->where('hora_f', '>=', $horaFin);
                      });
                });

            if ($idHorarioExcluir) {
                $conflictoAula->where('id_horario', '!=', $idHorarioExcluir);
            }

            $conflictoAula = $conflictoAula->with(['carga.grupo.materia'])->first();

            if ($conflictoAula) {
                $conflictos[] = [
                    'tipo' => 'aula',
                    'mensaje' => "El aula ya está ocupada en este horario por " . 
                                 ($conflictoAula->carga->grupo->materia->nombre ?? 'otra clase'),
                    'horario' => $conflictoAula
                ];
            }
        }

        // 2. Conflicto de docente (mismo docente, mismo día y horario)
        if ($idCarga) {
            $carga = \App\Modules\GestionAcademica\Models\CargaHoraria::find($idCarga);
            if ($carga) {
                $conflictoDocente = Horario::whereHas('carga', function ($q) use ($carga) {
                    $q->where('id_docente', $carga->id_docente);
                })
                ->where('dia', $dia)
                ->where(function ($q) use ($horaInicio, $horaFin) {
                    $q->whereBetween('hora_i', [$horaInicio, $horaFin])
                      ->orWhereBetween('hora_f', [$horaInicio, $horaFin])
                      ->orWhere(function ($q2) use ($horaInicio, $horaFin) {
                          $q2->where('hora_i', '<=', $horaInicio)
                             ->where('hora_f', '>=', $horaFin);
                      });
                });

                if ($idHorarioExcluir) {
                    $conflictoDocente->where('id_horario', '!=', $idHorarioExcluir);
                }

                $conflictoDocente = $conflictoDocente->with(['carga.grupo.materia', 'aula'])->first();

                if ($conflictoDocente) {
                    $conflictos[] = [
                        'tipo' => 'docente',
                        'mensaje' => "El docente ya tiene una clase en este horario: " . 
                                     ($conflictoDocente->carga->grupo->materia->nombre ?? 'otra materia') . 
                                     " en Aula " . ($conflictoDocente->aula->nro_aula ?? 'desconocida'),
                        'horario' => $conflictoDocente
                    ];
                }
            }
        }

        return $conflictos;
    }

    /**
     * Obtener horarios en formato calendario semanal
     */
    public function getHorariosSemanal(array $filters = []): array
    {
        $horarios = $this->all($filters);
        
        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
        $calendario = [];

        foreach ($dias as $dia) {
            $calendario[$dia] = $horarios->where('dia', $dia)->sortBy('hora_i')->values();
        }

        return $calendario;
    }
}
