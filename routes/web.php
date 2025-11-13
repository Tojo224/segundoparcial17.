<?php

use Illuminate\Support\Facades\Route;
use App\Modules\AdministracionUsuariosSeguridad\Controllers\Auth\LoginController;
use App\Modules\AdministracionUsuariosSeguridad\Controllers\UsuariosController;
use App\Modules\AdministracionUsuariosSeguridad\Controllers\BitacoraController;
use App\Modules\GestionAcademica\Controllers\DocentesController;
use App\Modules\GestionAcademica\Controllers\MateriasController;
use App\Modules\GestionAcademica\Controllers\GruposController;

Route::get('/', function () { return redirect('/login'); });

// Auth routes
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login']);
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// Cambio de contraseña al primer login
Route::middleware('auth')->group(function () {
    Route::get('/change-password', [LoginController::class, 'showChangePasswordForm'])->name('change-password.show');
    Route::post('/change-password', [LoginController::class, 'storeNewPassword'])->name('change-password.store');
});

// Dashboards por rol
Route::middleware('auth')->group(function () {
    Route::get('/admin', fn () => view('administracion_usuarios_seguridad.dashboard'))->name('admin.dashboard');
    Route::get('/decano', fn () => view('administracion_usuarios_seguridad.dashboard'))->name('decano.dashboard');
    Route::get('/vicedecano', fn () => view('administracion_usuarios_seguridad.dashboard'))->name('vicedecano.dashboard');
    Route::get('/director', fn () => view('administracion_usuarios_seguridad.dashboard'))->name('director.dashboard');
    Route::get('/docente', fn () => view('administracion_usuarios_seguridad.dashboard'))->name('docente.dashboard');
    Route::get('/home', fn () => view('administracion_usuarios_seguridad.dashboard'))->name('home');

    // Vista dinámica de gestión de usuarios
    Route::get('/usuarios', [UsuariosController::class, 'vistaUsuarios'])->name('usuarios.vista');
    Route::post('/usuarios', [UsuariosController::class, 'store'])->name('usuarios.store');
    Route::put('/usuarios/{id}', [UsuariosController::class, 'update'])->name('usuarios.update');
    Route::delete('/usuarios/{id}', [UsuariosController::class, 'destroy'])->name('usuarios.destroy');

    Route::get('/bitacora', [BitacoraController::class, 'vistaBitacora'])->name('bitacora.vista');
    
    Route::get('/docentes', [DocentesController::class, 'vistaDocentes'])->name('docentes.vista');
    Route::post('/docentes', [DocentesController::class, 'storeWeb'])->name('docentes.store');
    
    Route::get('/materias', [MateriasController::class, 'vistaMaterias'])->name('materias.vista');
    Route::post('/materias', [MateriasController::class, 'storeWeb'])->name('materias.store');
    Route::put('/materias/{id}', [MateriasController::class, 'updateWeb'])->name('materias.update');
    Route::delete('/materias/{id}', [MateriasController::class, 'destroyWeb'])->name('materias.destroy');

    Route::get('/grupos', [GruposController::class, 'vistaGrupos'])->name('grupos.vista');
    Route::get('/grupos/{id}/edit', [GruposController::class, 'edit'])->name('grupos.edit');
    Route::post('/grupos', [GruposController::class, 'storeWeb'])->name('grupos.store');
    Route::put('/grupos/{id}', [GruposController::class, 'updateWeb'])->name('grupos.update'); 
    Route::delete('/grupos/{id}', [GruposController::class, 'destroyWeb'])->name('grupos.destroy');
});

// Cargar rutas API (roles, usuarios, bitácora)
Route::prefix('api')->group(function () {
    require base_path('routes/api.php');
});
