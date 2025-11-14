<?php
/**
 * Script de prueba para debuggear la consulta de carga horaria
 * Ejecutar: php test_carga_horaria.php
 */

// Configurar el autoloader de Laravel
require __DIR__ . '/vendor/autoload.php';

// Inicializar Laravel
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== TEST CARGA HORARIA POR MATERIA ===\n\n";

// 1. Obtener todas las gestiones
echo "1. GESTIONES DISPONIBLES:\n";
$gestiones = DB::table('gestion_academica')
    ->select('id_gestion', 'anio', 'semestre')
    ->orderBy('anio', 'desc')
    ->get();

if ($gestiones->isEmpty()) {
    echo "   ❌ No hay gestiones en la BD\n\n";
    die();
}

foreach ($gestiones as $g) {
    echo "   - id_gestion: {$g->id_gestion}, año: {$g->anio}, semestre: {$g->semestre}\n";
}

// Usar la primera gestión para pruebas
$id_gestion = $gestiones[0]->id_gestion;
echo "\n✅ Usando id_gestion = {$id_gestion} para pruebas\n\n";

// 2. Contar carga_horaria para esta gestión
echo "2. CARGA HORARIA (count):\n";
$count = DB::table('carga_horaria')
    ->where('id_gestion', $id_gestion)
    ->count();
echo "   Total registros: $count\n\n";

if ($count === 0) {
    echo "   ⚠️ No hay registros de carga_horaria para esta gestión\n\n";
}

// 3. Ver raw de carga_horaria
echo "3. REGISTROS DE CARGA_HORARIA (raw):\n";
$raw = DB::table('carga_horaria')
    ->where('id_gestion', $id_gestion)
    ->limit(5)
    ->get();

foreach ($raw as $r) {
    echo "   - id_carga: {$r->id_carga}, id_grupo: {$r->id_grupo}, horas: {$r->horas_asignadas}\n";
}

// 4. Ver GRUPOS vinculados
echo "\n4. GRUPOS VINCULADOS:\n";
$grupos = DB::table('carga_horaria')
    ->join('grupo', 'carga_horaria.id_grupo', '=', 'grupo.id_grupo')
    ->where('carga_horaria.id_gestion', $id_gestion)
    ->select('grupo.id_grupo', 'grupo.id_materia', 'carga_horaria.horas_asignadas')
    ->distinct()
    ->limit(5)
    ->get();

if ($grupos->isEmpty()) {
    echo "   ❌ No se encontraron grupos\n";
} else {
    foreach ($grupos as $g) {
        echo "   - id_grupo: {$g->id_grupo}, id_materia: {$g->id_materia}, horas: {$g->horas_asignadas}\n";
    }
}

// 5. Ver MATERIAS vinculadas
echo "\n5. MATERIAS VINCULADAS:\n";
$materias = DB::table('carga_horaria')
    ->join('grupo', 'carga_horaria.id_grupo', '=', 'grupo.id_grupo')
    ->join('materia', 'grupo.id_materia', '=', 'materia.id_materia')
    ->where('carga_horaria.id_gestion', $id_gestion)
    ->select('materia.id_materia', 'materia.nombre')
    ->distinct()
    ->limit(5)
    ->get();

if ($materias->isEmpty()) {
    echo "   ❌ No se encontraron materias\n";
} else {
    foreach ($materias as $m) {
        echo "   - id_materia: {$m->id_materia}, nombre: {$m->nombre}\n";
    }
}

// 6. CONSULTA FINAL (como en el servicio)
echo "\n6. CONSULTA FINAL (GROUP BY):\n";
$datos = DB::table('carga_horaria')
    ->join('grupo', 'carga_horaria.id_grupo', '=', 'grupo.id_grupo')
    ->join('materia', 'grupo.id_materia', '=', 'materia.id_materia')
    ->where('carga_horaria.id_gestion', $id_gestion)
    ->select(
        'materia.id_materia',
        'materia.nombre as Materia',
        DB::raw('COALESCE(SUM(carga_horaria.horas_asignadas), 0) as Total_Horas')
    )
    ->groupBy('materia.id_materia', 'materia.nombre')
    ->orderBy('materia.nombre', 'asc')
    ->get();

echo "   Total materias con horas: " . count($datos) . "\n";

if (count($datos) === 0) {
    echo "   ❌ La consulta retorna array vacío\n\n";
} else {
    foreach ($datos as $item) {
        echo "   - {$item->Materia}: {$item->Total_Horas}h\n";
    }
}

// 7. Ver SQL generado
echo "\n7. SQL GENERADO:\n";
$sql = DB::table('carga_horaria')
    ->join('grupo', 'carga_horaria.id_grupo', '=', 'grupo.id_grupo')
    ->join('materia', 'grupo.id_materia', '=', 'materia.id_materia')
    ->where('carga_horaria.id_gestion', $id_gestion)
    ->select(
        'materia.id_materia',
        'materia.nombre as Materia',
        DB::raw('COALESCE(SUM(carga_horaria.horas_asignadas), 0) as Total_Horas')
    )
    ->groupBy('materia.id_materia', 'materia.nombre')
    ->orderBy('materia.nombre', 'asc')
    ->toSql();

echo "   $sql\n";

echo "\n=== FIN TEST ===\n";
