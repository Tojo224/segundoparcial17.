<?php

namespace App\Modules\ReportesYDashboard\Services;

use App\Modules\AdministracionUsuariosSeguridad\Services\BitacoraService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ReportesService
{
    public function __construct(
        protected BitacoraService $bitacora
    ) {}

    /**
     * Obtener gestiones académicas disponibles
     */
    public function obtenerGestiones()
    {
        return DB::table('gestion_academica')
            ->select('id_gestion', 'anio', 'semestre')
            ->orderByDesc('anio')
            ->orderByDesc('semestre')
            ->get();
    }

    /**
     * CU17 - Reporte de Horarios
     * Muestra la programación semanal de clases por docente, materia y grupo
     */
    public function generarReporteHorarios($id_gestion)
    {
        try {
            $datos = DB::table('horario')
                ->join('carga_horaria', 'horario.id_carga', '=', 'carga_horaria.id_carga')
                ->join('docente', 'carga_horaria.id_docente', '=', 'docente.id_docente')
                ->join('usuario', 'docente.id_usuario', '=', 'usuario.id_usuario')
                ->join('grupo', 'carga_horaria.id_grupo', '=', 'grupo.id_grupo')
                ->join('materia', 'grupo.id_materia', '=', 'materia.id_materia')
                ->join('aula', 'horario.id_aula', '=', 'aula.id_aula')
                ->join('gestion_academica', 'carga_horaria.id_gestion', '=', 'gestion_academica.id_gestion')
                ->where('carga_horaria.id_gestion', $id_gestion)
                ->select(
                    'usuario.nombre as Docente',
                    'materia.nombre as Materia',
                    'grupo.codigo as Grupo',
                    'horario.dia as Dia',
                    'horario.hora_i as Hora_Inicio',
                    'horario.hora_f as Hora_Fin',
                    'aula.nro_aula as Aula',
                    'aula.modulo as Modulo',
                    'gestion_academica.anio as Anio',
                    'gestion_academica.semestre as Semestre'
                )
                ->orderBy('horario.dia')
                ->orderBy('horario.hora_i')
                ->get()
                ->toArray();

            $info = $this->obtenerInfoGestion($id_gestion);

            return [
                'success' => true,
                'datos' => $datos,
                'total' => count($datos),
                'info' => $info
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'mensaje' => 'Error al generar reporte de horarios: ' . $e->getMessage()
            ];
        }
    }


    /**
     * CU17 - Reporte de Asistencia Docente
     * Consolida las asistencias registradas por los docentes
     */
    public function generarReporteAsistencia($id_gestion)
    {
        try {
            $datos = DB::table('asistencia')
                ->join('horario', 'asistencia.id_horario', '=', 'horario.id_horario')
                ->join('carga_horaria', 'horario.id_carga', '=', 'carga_horaria.id_carga')
                ->join('docente', 'carga_horaria.id_docente', '=', 'docente.id_docente')
                ->join('usuario', 'docente.id_usuario', '=', 'usuario.id_usuario')
                ->join('grupo', 'carga_horaria.id_grupo', '=', 'grupo.id_grupo')
                ->join('materia', 'grupo.id_materia', '=', 'materia.id_materia')
                ->join('gestion_academica', 'carga_horaria.id_gestion', '=', 'gestion_academica.id_gestion')
                ->where('carga_horaria.id_gestion', $id_gestion)
                ->select(
                    'asistencia.fecha_registro as Fecha_Registro',
                    'usuario.nombre as Docente',
                    'materia.nombre as Materia',
                    'grupo.codigo as Grupo',
                    'horario.dia as Dia',
                    'horario.hora_i as Hora_Inicio',
                    'horario.hora_f as Hora_Fin',
                    'asistencia.tipo as Tipo',
                    'gestion_academica.anio as Anio',
                    'gestion_academica.semestre as Semestre'
                )
                ->orderBy('asistencia.fecha_registro', 'desc')
                ->orderBy('usuario.nombre')
                ->get()
                ->toArray();

            $estadisticas = [
                'total_registros' => count($datos),
                'presentes' => collect($datos)->where('Tipo', 'Presente')->count(),
                'ausentes' => collect($datos)->where('Tipo', 'Ausente')->count(),
                'retrasos' => collect($datos)->where('Tipo', 'Retraso')->count(),
                'justificadas' => collect($datos)->where('Tipo', 'Justificada')->count(),
            ];

            $info = $this->obtenerInfoGestion($id_gestion);

            return [
                'success' => true,
                'datos' => $datos,
                'estadisticas' => $estadisticas,
                'total' => $estadisticas['total_registros'],
                'info' => $info
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'mensaje' => 'Error al generar reporte de asistencia: ' . $e->getMessage()
            ];
        }
    }


    /**
     * CU17 - Reporte de Disponibilidad de Aulas
     * Muestra el estado de ocupación de cada aula
     */
    public function generarReporteDisponibilidadAulas($id_gestion)
    {
        try {
            $info = $this->obtenerInfoGestion($id_gestion);

            // Obtener TODAS las aulas
            $todasAulas = DB::table('aula')->get();

            // Aulas ocupadas
            $aulasOcupadas = DB::table('horario')
                ->join('carga_horaria', 'horario.id_carga', '=', 'carga_horaria.id_carga')
                ->join('grupo', 'carga_horaria.id_grupo', '=', 'grupo.id_grupo')
                ->join('docente', 'carga_horaria.id_docente', '=', 'docente.id_docente')
                ->join('usuario', 'docente.id_usuario', '=', 'usuario.id_usuario')
                ->join('materia', 'grupo.id_materia', '=', 'materia.id_materia')
                ->where('carga_horaria.id_gestion', $id_gestion)
                ->distinct()
                ->pluck('horario.id_aula')
                ->toArray();

            // Horarios ocupados con detalles
            $datosOcupadas = DB::table('horario')
                ->join('carga_horaria', 'horario.id_carga', '=', 'carga_horaria.id_carga')
                ->join('grupo', 'carga_horaria.id_grupo', '=', 'grupo.id_grupo')
                ->join('docente', 'carga_horaria.id_docente', '=', 'docente.id_docente')
                ->join('usuario', 'docente.id_usuario', '=', 'usuario.id_usuario')
                ->join('materia', 'grupo.id_materia', '=', 'materia.id_materia')
                ->join('aula', 'horario.id_aula', '=', 'aula.id_aula')
                ->join('gestion_academica', 'carga_horaria.id_gestion', '=', 'gestion_academica.id_gestion')
                ->where('carga_horaria.id_gestion', $id_gestion)
                ->select(
                    'aula.nro_aula as Aula',
                    'aula.modulo as Modulo',
                    'horario.dia as Dia',
                    'horario.hora_i as Hora_Inicio',
                    'horario.hora_f as Hora_Fin',
                    'usuario.nombre as Docente',
                    'materia.nombre as Materia',
                    'grupo.codigo as Grupo',
                    'gestion_academica.anio as Anio',
                    'gestion_academica.semestre as Semestre'
                )
                ->orderBy('aula.nro_aula')
                ->orderBy('horario.dia')
                ->get()
                ->toArray();

            // Aulas disponibles
            $datosDisponibles = [];
            foreach ($todasAulas as $aula) {
                if (!in_array($aula->id_aula, $aulasOcupadas)) {
                    $datosDisponibles[] = (object)[
                        'Aula' => $aula->nro_aula,
                        'Modulo' => $aula->modulo,
                        'Intervalos_Disponibles' => 'Toda la semana',
                        'Anio' => $info['anio'],
                        'Semestre' => $info['semestre']
                    ];
                }
            }

            return [
                'success' => true,
                'ocupadas' => $datosOcupadas,
                'disponibles' => $datosDisponibles,
                'total_ocupadas' => count($datosOcupadas),
                'total_disponibles' => count($datosDisponibles),
                'info' => $info
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'mensaje' => 'Error al generar reporte de disponibilidad de aulas: ' . $e->getMessage()
            ];
        }
    }


    /**
     * CU17 - Reporte de Carga Horaria Docente
     * Muestra la carga horaria asignada a cada docente
     */
    public function generarReporteCargaHorariaDocente($id_gestion)
    {
        try {
            $datos = DB::table('carga_horaria')
                ->join('docente', 'carga_horaria.id_docente', '=', 'docente.id_docente')
                ->join('usuario', 'docente.id_usuario', '=', 'usuario.id_usuario')
                ->join('grupo', 'carga_horaria.id_grupo', '=', 'grupo.id_grupo')
                ->join('materia', 'grupo.id_materia', '=', 'materia.id_materia')
                ->join('gestion_academica', 'carga_horaria.id_gestion', '=', 'gestion_academica.id_gestion')
                ->where('carga_horaria.id_gestion', $id_gestion)
                ->select(
                    'usuario.nombre as Docente',
                    'materia.nombre as Materia',
                    'grupo.codigo as Grupo',
                    'carga_horaria.horas_asignadas as Horas_Asignadas',
                    'gestion_academica.anio as Anio',
                    'gestion_academica.semestre as Semestre'
                )
                ->orderBy('usuario.nombre')
                ->orderBy('materia.nombre')
                ->get()
                ->toArray();

            $info = $this->obtenerInfoGestion($id_gestion);

            return [
                'success' => true,
                'datos' => $datos,
                'total' => count($datos),
                'info' => $info
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'mensaje' => 'Error al generar reporte de carga horaria: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Obtener información de la gestión académica
     */
    private function obtenerInfoGestion($id_gestion)
    {
        $gestion = DB::table('gestion_academica')
            ->where('id_gestion', $id_gestion)
            ->first();

        if ($gestion) {
            return [
                'anio' => $gestion->anio,
                'semestre' => $gestion->semestre,
                'fecha_generacion' => now()->format('d/m/Y H:i:s')
            ];
        }

        return [
            'anio' => 'N/A',
            'semestre' => 'N/A',
            'fecha_generacion' => now()->format('d/m/Y H:i:s')
        ];
    }
}
