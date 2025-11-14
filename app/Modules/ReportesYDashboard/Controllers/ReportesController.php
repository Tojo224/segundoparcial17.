<?php

namespace App\Modules\ReportesYDashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\ReportesYDashboard\Services\ReportesService;
use App\Modules\ReportesYDashboard\Exports\ReportesExport;
use App\Modules\AdministracionUsuariosSeguridad\Services\BitacoraService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;

class ReportesController extends Controller
{
    public function __construct(
        protected ReportesService $reportesService,
        protected BitacoraService $bitacora
    ) {}

    /**
     * Mostrar vista de generación de reportes (CU17)
     */
    public function vistaReportes()
    {
        $gestiones = $this->reportesService->obtenerGestiones();
        return view('reportes_y_dashboard.reportes', compact('gestiones'));
    }

    /**
     * CU17 - Generar reporte de horarios
     */
    public function generarReporteHorarios(Request $request)
    {
        $id_gestion = $request->input('id_gestion');

        if (!$id_gestion) {
            return response()->json([
                'success' => false,
                'mensaje' => 'id_gestion es requerido'
            ], 400);
        }

        $resultado = $this->reportesService->generarReporteHorarios($id_gestion);

        return response()->json($resultado);
    }

    /**
     * CU17 - Generar reporte de asistencia
     */
    public function generarReporteAsistencia(Request $request)
    {
        $id_gestion = $request->input('id_gestion');

        if (!$id_gestion) {
            return response()->json([
                'success' => false,
                'mensaje' => 'id_gestion es requerido'
            ], 400);
        }

        $resultado = $this->reportesService->generarReporteAsistencia($id_gestion);

        return response()->json($resultado);
    }

    /**
     * CU17 - Generar reporte de disponibilidad de aulas
     */
    public function generarReporteDisponibilidadAulas(Request $request)
    {
        $id_gestion = $request->input('id_gestion');

        if (!$id_gestion) {
            return response()->json([
                'success' => false,
                'mensaje' => 'id_gestion es requerido'
            ], 400);
        }

        $resultado = $this->reportesService->generarReporteDisponibilidadAulas($id_gestion);

        return response()->json($resultado);
    }

    /**
     * CU17 - Generar reporte de carga horaria docente
     */
    public function generarReporteCargaHorariaDocente(Request $request)
    {
        $id_gestion = $request->input('id_gestion');

        if (!$id_gestion) {
            return response()->json([
                'success' => false,
                'mensaje' => 'id_gestion es requerido'
            ], 400);
        }

        $resultado = $this->reportesService->generarReporteCargaHorariaDocente($id_gestion);

        return response()->json($resultado);
    }

    /**
     * CU18 - Exportar a Excel y registrar en bitácora
     */
    public function exportarExcel(Request $request)
    {
        try {
            $tipo = $request->input('tipo_reporte');
            $id_gestion = $request->input('id_gestion');

            if (!$tipo || !$id_gestion) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'tipo_reporte e id_gestion son requeridos'
                ], 400);
            }

            // Generar datos del reporte
            $resultado = match ($tipo) {
                'horarios' => $this->reportesService->generarReporteHorarios($id_gestion),
                'asistencia' => $this->reportesService->generarReporteAsistencia($id_gestion),
                'aulas' => $this->reportesService->generarReporteDisponibilidadAulas($id_gestion),
                'carga_horaria' => $this->reportesService->generarReporteCargaHorariaDocente($id_gestion),
                default => ['success' => false, 'datos' => []]
            };

            if (!$resultado['success']) {
                return response()->json([
                    'success' => false,
                    'mensaje' => $resultado['mensaje'] ?? 'Error al generar reporte'
                ], 400);
            }

            // Obtener datos según el tipo de reporte
            $datos = [];
            $encabezados = [];

            if ($tipo === 'aulas') {
                // Para aulas, combinamos ocupadas y disponibles
                $datos = array_merge($resultado['ocupadas'], $resultado['disponibles']);
                $encabezados = ['Aula', 'Modulo', 'Dia', 'Hora_Inicio', 'Hora_Fin', 'Docente', 'Materia', 'Grupo', 'Anio', 'Semestre'];
            } else {
                $datos = $resultado['datos'] ?? [];
                if (!empty($datos)) {
                    $primer = (array) $datos[0];
                    $encabezados = array_keys($primer);
                }
            }

            if (empty($datos)) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No hay datos para exportar'
                ], 400);
            }

            // Convertir objetos a arrays
            $datosArray = collect($datos)->map(function ($item) {
                return (array) $item;
            })->toArray();

            // Registrar en bitácora
            $this->bitacora->registrar(
                "Exportó reporte de {$tipo} a Excel",
                Auth::id()
            );

            // Generar Excel
            $nombreArchivo = "Reporte_{$tipo}_" . now()->format('dmYHis') . ".xlsx";
            return Excel::download(
                new ReportesExport($datosArray, $encabezados, "Reporte: {$tipo}"),
                $nombreArchivo
            );

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al exportar Excel: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * CU18 - Exportar a PDF y registrar en bitácora
     */
    public function exportarPDF(Request $request)
    {
        try {
            $tipo = $request->input('tipo_reporte');
            $id_gestion = $request->input('id_gestion');

            if (!$tipo || !$id_gestion) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'tipo_reporte e id_gestion son requeridos'
                ], 400);
            }

            // Generar datos del reporte
            $resultado = match ($tipo) {
                'horarios' => $this->reportesService->generarReporteHorarios($id_gestion),
                'asistencia' => $this->reportesService->generarReporteAsistencia($id_gestion),
                'aulas' => $this->reportesService->generarReporteDisponibilidadAulas($id_gestion),
                'carga_horaria' => $this->reportesService->generarReporteCargaHorariaDocente($id_gestion),
                default => ['success' => false, 'datos' => []]
            };

            if (!$resultado['success']) {
                return response()->json([
                    'success' => false,
                    'mensaje' => $resultado['mensaje'] ?? 'Error al generar reporte'
                ], 400);
            }

            // Obtener datos según el tipo de reporte
            $datos = [];
            if ($tipo === 'aulas') {
                $datos = array_merge($resultado['ocupadas'], $resultado['disponibles']);
            } else {
                $datos = $resultado['datos'] ?? [];
            }

            if (empty($datos)) {
                return response()->json([
                    'success' => false,
                    'mensaje' => 'No hay datos para exportar'
                ], 400);
            }

            // Preparar HTML para PDF
            $info = $resultado['info'];
            $html = $this->generarHTMLReporte($tipo, $datos, $info);

            // Registrar en bitácora
            $this->bitacora->registrar(
                "Exportó reporte de {$tipo} a PDF",
                Auth::id()
            );

            // Generar PDF
            $nombreArchivo = "Reporte_{$tipo}_" . now()->format('dmYHis') . ".pdf";
            $pdf = Pdf::loadHTML($html)
                ->setPaper('a4', 'landscape')
                ->setOption('margin-bottom', 0)
                ->setOption('margin-top', 5)
                ->setOption('margin-left', 5)
                ->setOption('margin-right', 5);

            return $pdf->download($nombreArchivo);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'mensaje' => 'Error al exportar PDF: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Generar HTML para PDF con información del reporte
     */
    private function generarHTMLReporte($tipo, $datos, $info)
    {
        $titulo = match ($tipo) {
            'horarios' => 'Reporte de Horarios',
            'asistencia' => 'Reporte de Asistencia Docente',
            'aulas' => 'Reporte de Disponibilidad de Aulas',
            'carga_horaria' => 'Reporte de Carga Horaria Docente',
            default => 'Reporte'
        };

        $html = "<style>
            body { font-family: Arial, sans-serif; font-size: 9px; margin: 0; padding: 10px; }
            h1 { text-align: center; font-size: 16px; margin: 10px 0 5px 0; color: #1F2937; }
            .header-info { text-align: center; margin: 10px 0; color: #666; font-size: 9px; border-bottom: 1px solid #ddd; padding-bottom: 10px; }
            table { width: 100%; border-collapse: collapse; margin-top: 10px; }
            th { background-color: #1F2937; color: white; padding: 8px; text-align: left; border: 1px solid #ddd; font-weight: bold; font-size: 8px; }
            td { padding: 6px; border: 1px solid #ddd; font-size: 8px; }
            tr:nth-child(even) { background-color: #f9fafb; }
            .footer { text-align: center; margin-top: 20px; font-size: 8px; color: #999; border-top: 1px solid #ddd; padding-top: 10px; }
        </style>";

        $html .= "<h1>{$titulo}</h1>";
        $html .= "<div class='header-info'>
            <p><strong>Año:</strong> {$info['anio']} | <strong>Semestre:</strong> {$info['semestre']}</p>
            <p><strong>Fecha de Generación:</strong> {$info['fecha_generacion']}</p>
        </div>";

        $html .= "<table>";

        // Encabezados
        if (!empty($datos)) {
            $primer = (array) $datos[0];
            $html .= "<tr>";
            foreach (array_keys($primer) as $columna) {
                $html .= "<th>" . str_replace('_', ' ', $columna) . "</th>";
            }
            $html .= "</tr>";

            // Datos
            foreach ($datos as $fila) {
                $html .= "<tr>";
                foreach ((array) $fila as $valor) {
                    $html .= "<td>" . htmlspecialchars($valor ?? '') . "</td>";
                }
                $html .= "</tr>";
            }
        }

        $html .= "</table>";
        $html .= "<div class='footer'>
            <p>Reporte generado automáticamente por el Sistema de Reportes Académicos</p>
        </div>";

        return $html;
    }
}
