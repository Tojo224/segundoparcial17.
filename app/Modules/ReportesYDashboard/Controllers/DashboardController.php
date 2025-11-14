<?php

namespace App\Modules\ReportesYDashboard\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\ReportesYDashboard\Services\DashboardService;
use App\Modules\ReportesYDashboard\Services\ReportesService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    public function __construct(
        protected DashboardService $dashboardService,
        protected ReportesService $reportesService
    ) {}

    // vista
    public function vistaDashboard()
    {
        return view('reportes_y_dashboard.dashboardrep');
    }

    // KPIs
    public function obtenerKPIs(Request $request)
    {
        $periodo = $request->query('periodo', 'semana');

        return response()->json([
            'success' => true,
            'datos' => $this->dashboardService->obtenerKPIs($periodo)
        ]);
    }

    // actividad
    public function obtenerActividadUltimos7Dias(Request $request)
    {
        $periodo = $request->query('periodo', 'semana');

        return response()->json([
            'success' => true,
            'datos' => $this->dashboardService->obtenerActividad($periodo)
        ]);
    }

    // distribución
    public function obtenerDistribucionReportes(Request $request)
    {
        $periodo = $request->query('periodo', 'semana');

        return response()->json([
            'success' => true,
            'datos' => $this->dashboardService->obtenerDistribucionReportes($periodo)
        ]);
    }

    // resumen
    public function obtenerResumen()
    {
        return response()->json([
            'success' => true,
            'datos' => $this->dashboardService->obtenerResumen()
        ]);
    }

    // carga horaria por materia
    public function estadisticasCargaHoraria(Request $request)
    {
        $id_gestion = $request->query('id_gestion');

        if (!$id_gestion || !is_numeric($id_gestion)) {
            return response()->json([
                'success' => false,
                'mensaje' => 'id_gestion es requerido'
            ], 400);
        }

        return response()->json([
            'success' => true,
            'datos' => $this->dashboardService->estadisticasCargaHorariaPorMateria($id_gestion)
        ]);
    }
    public function obtenerGestiones()
    {
        $gestiones = DB::table('gestion_academica')
            ->select('id_gestion', 'anio', 'semestre')
            ->orderBy('anio', 'desc')
            ->orderBy('semestre', 'desc')
            ->get();

        return response()->json([
            'success' => true,
            'datos' => $gestiones
        ]);
    }

}
