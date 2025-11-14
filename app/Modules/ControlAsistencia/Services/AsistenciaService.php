<?php

namespace App\Modules\ControlAsistencia\Services;

use App\Modules\ControlAsistencia\Models\Asistencia;
use App\Modules\AulasHorarios\Models\Horario;
use App\Modules\GestionAcademica\Models\CargaHoraria;
use Illuminate\Support\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Carbon\Carbon;

class AsistenciaService
{
    /**
     * Obtener todas las asistencias con filtros
     */
    public function all(array $filters = []): Collection
    {
        $query = Asistencia::with(['horario.carga.docente.usuario', 'horario.carga.grupo.materia', 'horario.aula']);

        if (!empty($filters['fecha_registro'])) {
            $query->whereDate('fecha_registro', $filters['fecha_registro']);
        }

        if (!empty($filters['fecha_desde'])) {
            $query->where('fecha_registro', '>=', $filters['fecha_desde']);
        }

        if (!empty($filters['fecha_hasta'])) {
            $query->where('fecha_registro', '<=', $filters['fecha_hasta']);
        }

        if (!empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (!empty($filters['id_horario'])) {
            $query->where('id_horario', $filters['id_horario']);
        }

        if (!empty($filters['id_docente'])) {
            $query->whereHas('horario.carga', function ($q) use ($filters) {
                $q->where('id_docente', $filters['id_docente']);
            });
        }

        if (!empty($filters['id_grupo'])) {
            $query->whereHas('horario.carga', function ($q) use ($filters) {
                $q->where('id_grupo', $filters['id_grupo']);
            });
        }

        return $query->orderBy('fecha_registro', 'desc')
                     ->orderBy('id_asistencia', 'desc')
                     ->get();
    }

    /**
     * Paginación de asistencias
     */
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        $query = Asistencia::with(['horario.carga.docente.usuario', 'horario.carga.grupo.materia', 'horario.aula']);

        if (!empty($filters['fecha_registro'])) {
            $query->whereDate('fecha_registro', $filters['fecha_registro']);
        }

        if (!empty($filters['tipo'])) {
            $query->where('tipo', $filters['tipo']);
        }

        if (!empty($filters['id_docente'])) {
            $query->whereHas('horario.carga', function ($q) use ($filters) {
                $q->where('id_docente', $filters['id_docente']);
            });
        }

        return $query->orderBy('fecha_registro', 'desc')
                     ->orderBy('id_asistencia', 'desc')
                     ->paginate($perPage);
    }

    /**
     * Encontrar una asistencia por ID
     */
    public function find(int $id): ?Asistencia
    {
        return Asistencia::with(['horario.carga.docente.usuario', 'horario.carga.grupo.materia', 'horario.aula'])
            ->find($id);
    }

    /**
     * Crear nueva asistencia
     */
    public function create(array $data): Asistencia
    {
        return Asistencia::create($data);
    }

    /**
     * Actualizar asistencia
     */
    public function update(int $id, array $data): bool
    {
        $asistencia = Asistencia::find($id);
        if (!$asistencia) {
            return false;
        }

        return $asistencia->update($data);
    }

    /**
     * Eliminar asistencia
     */
    public function delete(int $id): bool
    {
        $asistencia = Asistencia::find($id);
        if (!$asistencia) {
            return false;
        }

        return $asistencia->delete();
    }

    /**
     * Verificar si ya existe una asistencia registrada para un horario en una fecha
     */
    public function existeAsistencia(int $idHorario, string $fecha): bool
    {
        return Asistencia::where('id_horario', $idHorario)
            ->whereDate('fecha_registro', $fecha)
            ->exists();
    }

    /**
     * Obtener horarios programados para una fecha específica
     */
    public function getHorariosPorFecha(string $fecha): Collection
    {
        $diaSemana = Carbon::parse($fecha)->locale('es')->dayName;
        $diaCapitalizado = ucfirst($diaSemana);

        return Horario::with(['carga.docente.usuario', 'carga.grupo.materia', 'aula'])
            ->where('dia', $diaCapitalizado)
            ->orderBy('hora_i', 'asc')
            ->get();
    }

    /**
     * Obtener horarios sin asistencia registrada para una fecha
     */
    public function getHorariosSinAsistencia(string $fecha): Collection
    {
        $horarios = $this->getHorariosPorFecha($fecha);
        $horariosConAsistencia = Asistencia::whereDate('fecha_registro', $fecha)
            ->pluck('id_horario')
            ->toArray();

        return $horarios->filter(function ($horario) use ($horariosConAsistencia) {
            return !in_array($horario->id_horario, $horariosConAsistencia);
        });
    }

    /**
     * Obtener estadísticas de asistencia
     */
    public function getEstadisticas(array $filters = []): array
    {
        $query = Asistencia::query();

        if (!empty($filters['fecha_desde'])) {
            $query->where('fecha_registro', '>=', $filters['fecha_desde']);
        }

        if (!empty($filters['fecha_hasta'])) {
            $query->where('fecha_registro', '<=', $filters['fecha_hasta']);
        }

        $total = $query->count();
        $presentados = (clone $query)->where('tipo', Asistencia::TIPO_PRESENTADO)->count();
        $ausentes = (clone $query)->where('tipo', Asistencia::TIPO_AUSENTE)->count();
        $justificados = (clone $query)->where('tipo', Asistencia::TIPO_JUSTIFICADO)->count();
        $tardanzas = (clone $query)->where('tipo', Asistencia::TIPO_TARDANZA)->count();

        return [
            'total' => $total,
            'presentados' => $presentados,
            'ausentes' => $ausentes,
            'justificados' => $justificados,
            'tardanzas' => $tardanzas,
            'porcentaje_presentados' => $total > 0 ? round(($presentados / $total) * 100, 2) : 0,
            'porcentaje_ausentes' => $total > 0 ? round(($ausentes / $total) * 100, 2) : 0,
        ];
    }
}
