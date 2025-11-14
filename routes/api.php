<?php

use Illuminate\Support\Facades\Route;
use App\Modules\AdministracionUsuariosSeguridad\Controllers\{
    RolesController,
    UsuariosController,
    BitacoraController
};
use App\Modules\GestionAcademica\Controllers\{
    DocentesController,
    MateriasController,
    GruposController,
    CargaHorariaController
};

/*
|--------------------------------------------------------------------------
| RUTAS API – Administración de Usuarios y Seguridad
|--------------------------------------------------------------------------
*/

Route::prefix('roles')->group(function () {
    Route::get('/', [RolesController::class, 'index']);          // Listar roles
    Route::get('/{id}', [RolesController::class, 'show']);       // Ver detalle
    Route::post('/', [RolesController::class, 'store']);         // Crear rol
    Route::put('/{id}', [RolesController::class, 'update']);     // Actualizar rol
    Route::delete('/{id}', [RolesController::class, 'destroy']); // Eliminar rol
});

Route::prefix('usuarios')->group(function () {
    Route::get('/', [UsuariosController::class, 'index']);          // Listar usuarios
    Route::get('/{id}', [UsuariosController::class, 'show']);       // Ver detalle
    Route::post('/', [UsuariosController::class, 'store']);         // Crear usuario
    Route::put('/{id}', [UsuariosController::class, 'update']);     // Actualizar usuario
    Route::delete('/{id}', [UsuariosController::class, 'destroy']); // Eliminar usuario

    // Asignar rol a usuario
    Route::post('/{id}/asignar-rol', [UsuariosController::class, 'assignRole']);
});

Route::prefix('bitacora')->group(function () {
    Route::get('/', [BitacoraController::class, 'index']);       // Listar acciones
    Route::get('/{id}', [BitacoraController::class, 'show']);    // Ver detalle
});

/*
|--------------------------------------------------------------------------
| RUTAS API — Gestión Académica
|--------------------------------------------------------------------------
*/
Route::prefix('docentes')->group(function () {
    Route::get('/', [DocentesController::class, 'index']);
    Route::get('/{id}', [DocentesController::class, 'show']);
    Route::post('/', [DocentesController::class, 'store']);
    Route::put('/{id}', [DocentesController::class, 'update']);
    Route::delete('/{id}', [DocentesController::class, 'destroy']);
});

Route::prefix('materias')->group(function () {
    Route::get('/', [MateriasController::class, 'index']);
    Route::get('/{id}', [MateriasController::class, 'show']);
    Route::post('/', [MateriasController::class, 'store']);
    Route::put('/{id}', [MateriasController::class, 'update']);
    Route::delete('/{id}', [MateriasController::class, 'destroy']);
});

Route::prefix('grupos')->group(function () {
    Route::get('/', [GruposController::class, 'index']);
    Route::get('/{id}', [GruposController::class, 'show']);
    Route::post('/', [GruposController::class, 'store']);
    Route::put('/{id}', [GruposController::class, 'update']);
    Route::delete('/{id}', [GruposController::class, 'destroy']);
});

Route::prefix('carga-horaria')->group(function () {
    Route::get('/', [CargaHorariaController::class, 'index']);
    Route::get('/{id}', [CargaHorariaController::class, 'show']);
    Route::post('/', [CargaHorariaController::class, 'store']);
    Route::put('/{id}', [CargaHorariaController::class, 'update']);
    Route::delete('/{id}', [CargaHorariaController::class, 'destroy']);
});

/*
|--------------------------------------------------------------------------
| RUTAS API — Reportes y Dashboard (CU17, CU18, CU19)
|--------------------------------------------------------------------------
*/
use App\Modules\ReportesYDashboard\Controllers\{
    ReportesController,
    DashboardController
};

// Endpoints de Reportes
Route::prefix('reportes')->group(function () {
    Route::post('/horarios', [ReportesController::class, 'generarReporteHorarios']);
    Route::post('/asistencia', [ReportesController::class, 'generarReporteAsistencia']);
    Route::post('/aulas', [ReportesController::class, 'generarReporteDisponibilidadAulas']);
    Route::post('/carga-horaria', [ReportesController::class, 'generarReporteCargaHorariaDocente']);
    Route::post('/pdf', [ReportesController::class, 'exportarPDF']);
    Route::post('/excel', [ReportesController::class, 'exportarExcel']);
});

// Endpoints de Dashboard
Route::prefix('dashboard')->group(function () {
    Route::get('/kpis', [DashboardController::class, 'obtenerKPIs']);
    Route::get('/actividad', [DashboardController::class, 'obtenerActividadUltimos7Dias']); 
    Route::get('/distribucion', [DashboardController::class, 'obtenerDistribucionReportes']);
    Route::get('/estadisticas-carga-horaria', [DashboardController::class, 'estadisticasCargaHoraria']);
    Route::get('/gestiones', [DashboardController::class, 'obtenerGestiones']);


});
