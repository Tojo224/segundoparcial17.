<?php
// Simple test
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

$id_gestion = 1;

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

echo "=== RESULTADO ===\n";
echo "Count: " . count($datos) . "\n";
echo json_encode($datos, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) . "\n";
