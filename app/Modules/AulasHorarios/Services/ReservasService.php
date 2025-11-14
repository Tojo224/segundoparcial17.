<?php

namespace App\Modules\AulasHorarios\Services;

use App\Modules\AulasHorarios\Models\{ReservaAula, Horario};
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class ReservasService
{
    /**
     * Obtener todas las reservas con filtros
     */
    public function all(array $filters = []): Collection
    {
        $query = ReservaAula::with(['aula', 'usuario']);

        if (!empty($filters['fecha'])) {
            $query->whereDate('fecha', $filters['fecha']);
        }

        if (!empty($filters['id_aula'])) {
            $query->where('id_aula', $filters['id_aula']);
        }

        if (!empty($filters['id_usuario'])) {
            $query->where('id_usuario', $filters['id_usuario']);
        }

        return $query->orderBy('fecha', 'desc')
                     ->orderBy('hora_i', 'asc')
                     ->get();
    }

    /**
     * Paginación de reservas
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = ReservaAula::with(['aula', 'usuario']);

        if (!empty($filters['fecha'])) {
            $query->whereDate('fecha', $filters['fecha']);
        }

        if (!empty($filters['id_aula'])) {
            $query->where('id_aula', $filters['id_aula']);
        }

        return $query->orderBy('fecha', 'desc')
                     ->orderBy('hora_i', 'asc')
                     ->paginate($perPage);
    }

    /**
     * Buscar reserva por ID
     */
    public function find(mixed $id): ?ReservaAula
    {
        return ReservaAula::with(['aula', 'usuario'])->find($id);
    }

    /**
     * Crear nueva reserva
     */
    public function create(array $data): ReservaAula
    {
        // Calcular el día de la semana a partir de la fecha
        $fecha = Carbon::parse($data['fecha']);
        $data['dia'] = ucfirst($fecha->locale('es')->isoFormat('dddd')); // Lunes, Martes, etc.
        
        return ReservaAula::create($data);
    }

    /**
     * Actualizar reserva existente
     */
    public function update(mixed $id, array $data): ?ReservaAula
    {
        $reserva = ReservaAula::find($id);
        if (!$reserva) return null;

        // Recalcular día si se cambió la fecha
        if (isset($data['fecha'])) {
            $fecha = Carbon::parse($data['fecha']);
            $data['dia'] = ucfirst($fecha->locale('es')->isoFormat('dddd'));
        }

        $reserva->fill($data);
        $reserva->save();
        return $reserva->fresh(['aula', 'usuario']);
    }

    /**
     * Eliminar reserva
     */
    public function delete(mixed $id): bool
    {
        $reserva = ReservaAula::find($id);
        if (!$reserva) return false;
        return (bool) $reserva->delete();
    }

    /**
     * Verificar conflictos de reserva
     * Retorna array con conflictos encontrados
     */
    public function verificarConflictos(
        int $idAula,
        string $fecha,
        string $horaInicio,
        string $horaFin,
        ?int $idReservaExcluir = null
    ): array {
        $conflictos = [];

        // 1. Verificar conflictos con otras reservas
        $reservasConflicto = ReservaAula::where('id_aula', $idAula)
            ->where('fecha', $fecha)
            ->when($idReservaExcluir, function ($q) use ($idReservaExcluir) {
                $q->where('id_reserva', '!=', $idReservaExcluir);
            })
            ->where(function ($q) use ($horaInicio, $horaFin) {
                $q->where(function ($query) use ($horaInicio, $horaFin) {
                    // Caso 1: La nueva reserva empieza durante una reserva existente
                    $query->where('hora_i', '<=', $horaInicio)
                          ->where('hora_f', '>', $horaInicio);
                })->orWhere(function ($query) use ($horaInicio, $horaFin) {
                    // Caso 2: La nueva reserva termina durante una reserva existente
                    $query->where('hora_i', '<', $horaFin)
                          ->where('hora_f', '>=', $horaFin);
                })->orWhere(function ($query) use ($horaInicio, $horaFin) {
                    // Caso 3: La nueva reserva engloba completamente una reserva existente
                    $query->where('hora_i', '>=', $horaInicio)
                          ->where('hora_f', '<=', $horaFin);
                });
            })
            ->with('usuario')
            ->get();

        foreach ($reservasConflicto as $reserva) {
            $conflictos[] = "Conflicto con reserva existente: {$reserva->usuario->nombre} reservó de " . 
                           substr($reserva->hora_i, 0, 5) . " a " . substr($reserva->hora_f, 0, 5) . 
                           " para '{$reserva->motivo}'";
        }

        // 2. Verificar conflictos con horarios de clases (tabla horario)
        $diaSemana = Carbon::parse($fecha)->locale('es')->isoFormat('dddd');
        $diaSemana = ucfirst($diaSemana); // Lunes, Martes, etc.

        $horariosConflicto = Horario::where('id_aula', $idAula)
            ->where('dia', $diaSemana)
            ->where(function ($q) use ($horaInicio, $horaFin) {
                $q->where(function ($query) use ($horaInicio, $horaFin) {
                    $query->where('hora_i', '<=', $horaInicio)
                          ->where('hora_f', '>', $horaInicio);
                })->orWhere(function ($query) use ($horaInicio, $horaFin) {
                    $query->where('hora_i', '<', $horaFin)
                          ->where('hora_f', '>=', $horaFin);
                })->orWhere(function ($query) use ($horaInicio, $horaFin) {
                    $query->where('hora_i', '>=', $horaInicio)
                          ->where('hora_f', '<=', $horaFin);
                });
            })
            ->with('carga.grupo.materia', 'carga.docente.usuario')
            ->get();

        foreach ($horariosConflicto as $horario) {
            $materia = $horario->carga->grupo->materia->nombre ?? 'N/A';
            $docente = $horario->carga->docente->usuario->nombre ?? 'N/A';
            $conflictos[] = "Conflicto con clase programada: {$materia} con {$docente} de " . 
                           substr($horario->hora_i, 0, 5) . " a " . substr($horario->hora_f, 0, 5);
        }

        return $conflictos;
    }

    /**
     * Obtener reservas por fecha para calendario
     */
    public function getReservasPorFecha(string $fecha): Collection
    {
        return ReservaAula::with(['aula', 'usuario'])
            ->where('fecha', $fecha)
            ->orderBy('hora_i')
            ->get();
    }

    /**
     * Obtener reservas del usuario
     */
    public function getReservasUsuario(int $idUsuario, bool $soloProximas = false): Collection
    {
        $query = ReservaAula::with(['aula'])
            ->where('id_usuario', $idUsuario);

        if ($soloProximas) {
            $query->where('fecha', '>=', now()->toDateString());
        }

        return $query->orderBy('fecha', 'desc')
                     ->orderBy('hora_i', 'asc')
                     ->get();
    }

    /**
     * Obtener disponibilidad de aula en una fecha
     */
    public function getDisponibilidadAula(int $idAula, string $fecha): array
    {
        $reservas = ReservaAula::where('id_aula', $idAula)
            ->where('fecha', $fecha)
            ->orderBy('hora_i')
            ->get(['hora_i', 'hora_f', 'motivo']);

        $diaSemana = Carbon::parse($fecha)->locale('es')->isoFormat('dddd');
        $diaSemana = ucfirst($diaSemana);

        $horarios = Horario::where('id_aula', $idAula)
            ->where('dia', $diaSemana)
            ->with('carga.grupo.materia')
            ->orderBy('hora_i')
            ->get(['hora_i', 'hora_f', 'id_carga']);

        return [
            'reservas' => $reservas,
            'clases' => $horarios
        ];
    }
}
