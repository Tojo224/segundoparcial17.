<?php

namespace App\Modules\ReportesYDashboard\Services;

use App\Modules\AdministracionUsuariosSeguridad\Services\BitacoraService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class DashboardService
{
    public function __construct(
        protected BitacoraService $bitacora
    ) {}

    // rango según periodo
    private function rangoFecha($periodo)
    {
        switch ($periodo) {

            case 'mes':
                return [now()->startOfMonth(), now()->endOfMonth()];

            case 'semestre':
                return now()->month <= 6
                    ? [now()->startOfYear(), now()->setMonth(6)->endOfMonth()]
                    : [now()->setMonth(7)->startOfMonth(), now()->endOfYear()];

            case 'gestion':
                $g = DB::table('gestion_academica')->where('estado', true)->first();
                if (!$g) return [now()->startOfYear(), now()->endOfYear()];

                return $g->semestre == 1
                    ? [now()->setMonth(1)->startOfMonth(), now()->setMonth(6)->endOfMonth()]
                    : [now()->setMonth(7)->startOfMonth(), now()->setMonth(12)->endOfMonth()];

            default:
                return [now()->startOfWeek(), now()->endOfWeek()];
        }
    }

    // KPIs
    public function obtenerKPIs($periodo)
    {
        return [
            'ocupacion_aulas' => $this->calcularOcupacionAulas(),
            'asistencia_docentes' => $this->calcularAsistenciaDocentes($periodo),
            'carga_horaria' => $this->calcularCargaHoraria(),
            'docentes_activos' => $this->calcularDocentesActivos(),
        ];
    }

    private function calcularOcupacionAulas()
    {
        $total = DB::table('aula')->count();
        $ocupadas = DB::table('horario')->distinct()->count('id_aula');

        return [
            'valor' => "$ocupadas/$total",
            'porcentaje' => $total ? round(($ocupadas / $total) * 100, 2) : 0
        ];
    }

    private function calcularAsistenciaDocentes($periodo)
    {
        [$inicio, $fin] = $this->rangoFecha($periodo);

        $total = DB::table('asistencia')
            ->whereBetween('fecha_registro', [$inicio, $fin])
            ->count();

        $presentes = DB::table('asistencia')
            ->where('tipo', 'Presente')
            ->whereBetween('fecha_registro', [$inicio, $fin])
            ->count();

        return [
            'valor' => "$presentes/$total",
            'porcentaje' => $total ? round(($presentes / $total) * 100, 2) : 0
        ];
    }

    private function calcularCargaHoraria()
    {
        $docentes = DB::table('docente')->count();
        $totalHoras = DB::table('carga_horaria')->sum('horas_asignadas');

        return [
            'valor' => $docentes ? round($totalHoras / $docentes, 2) : 0
        ];
    }

    private function calcularDocentesActivos()
    {
        $total = DB::table('docente')->count();
        $activos = DB::table('carga_horaria')->distinct()->count('id_docente');

        return [
            'valor' => "$activos/$total",
            'porcentaje' => $total ? round(($activos / $total) * 100, 2) : 0
        ];
    }

    // actividad
    public function obtenerActividad($periodo)
    {
        [$inicio, $fin] = $this->rangoFecha($periodo);

        return DB::table('asistencia')
            ->select(DB::raw('DATE(fecha_registro) as fecha'), DB::raw('count(*) as cantidad'))
            ->whereBetween('fecha_registro', [$inicio, $fin])
            ->groupBy('fecha')
            ->orderBy('fecha')
            ->get();
    }

    // distribución
    public function obtenerDistribucionReportes($periodo)
    {
        [$inicio, $fin] = $this->rangoFecha($periodo);

        $tipos = ['Presente', 'Ausente', 'Retraso', 'Justificada'];

        return collect($tipos)->map(function ($t) use ($inicio, $fin) {
            return [
                'tipo' => $t,
                'cantidad' => DB::table('asistencia')
                    ->where('tipo', $t)
                    ->whereBetween('fecha_registro', [$inicio, $fin])
                    ->count()
            ];
        });
    }

    // carga horaria por materia
    public function estadisticasCargaHorariaPorMateria($id_gestion)
    {
        try {
            // Logging para debugging
            \Log::info("=== CARGA HORARIA POR MATERIA ===");
            \Log::info("id_gestion recibido: " . $id_gestion);
            
            // Verificar que exista la gestión
            $gestion = DB::table('gestion_academica')
                ->where('id_gestion', $id_gestion)
                ->first();
            
            if (!$gestion) {
                \Log::warning("Gestión no encontrada: id_gestion = " . $id_gestion);
                return [];
            }
            
            \Log::info("Gestión encontrada: " . json_encode($gestion));
            
            // Contar carga_horaria para esta gestión
            $count = DB::table('carga_horaria')
                ->where('id_gestion', $id_gestion)
                ->count();
            
            \Log::info("Registros de carga_horaria encontrados: " . $count);
            
            // FIX: En PostgreSQL, el alias no funciona bien en el select
            // Se debe castear el resultado después
            $datos = DB::table('carga_horaria')
                ->join('grupo', 'carga_horaria.id_grupo', '=', 'grupo.id_grupo')
                ->join('materia', 'grupo.id_materia', '=', 'materia.id_materia')
                ->where('carga_horaria.id_gestion', $id_gestion)
                ->select(
                    'materia.id_materia',
                    'materia.nombre',
                    DB::raw('COALESCE(SUM(carga_horaria.horas_asignadas), 0)::int as total_horas')
                )
                ->groupBy('materia.id_materia', 'materia.nombre')
                ->orderBy('materia.nombre', 'asc')
                ->get();

            \Log::info("Datos obtenidos (raw): " . json_encode($datos));
            
            // Convertir explícitamente los tipos de datos con los nombres correctos
            $resultado = $datos->map(function ($item) {
                return [
                    'Materia' => (string) $item->nombre,
                    'Total_Horas' => (int) $item->total_horas
                ];
            })->values()->toArray();
            
            \Log::info("Resultado final: " . json_encode($resultado));
            
            return $resultado;
            
        } catch (\Exception $e) {
            // Log del error para debugging
            \Log::error('Error en estadisticasCargaHorariaPorMateria: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());
            return [];
        }
    }

    // bitácora
    public function registrarDescarga($tipo, $formato)
    {
        try {
            return $this->bitacora->registrar(
                "Descargó reporte $tipo ($formato)",
                Auth::id()
            );
        } catch (\Exception) {
            return false;
        }
    }

    // resumen
    public function obtenerResumen()
    {
        return [
            'fecha' => now()->format('d/m/Y H:i'),
            'gestion_actual' => DB::table('gestion_academica')
                ->where('estado', true)
                ->first()
        ];
    }
}
