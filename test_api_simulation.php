<?php
// Simular exactamente lo que hace el API endpoint
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use App\Modules\ReportesYDashboard\Services\DashboardService;

$service = app(DashboardService::class);

// Obtener gestiones primero
use Illuminate\Support\Facades\DB;

$gestiones = DB::table('gestion_academica')
    ->select('id_gestion', 'anio', 'semestre')
    ->get();

echo "=== GESTIONES ===\n";
echo json_encode([
    'success' => true,
    'datos' => $gestiones
], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n\n";

// Ahora probar el servicio
$id_gestion = 1;
echo "=== ESTADISTICAS CARGA HORARIA (id_gestion=$id_gestion) ===\n";
$resultado = $service->estadisticasCargaHorariaPorMateria($id_gestion);

$response = [
    'success' => true,
    'datos' => $resultado
];

echo json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
