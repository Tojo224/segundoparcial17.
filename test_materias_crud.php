<?php
/**
 * Test del CRUD de Materias y Bitácora
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make('Illuminate\Contracts\Console\Kernel');
$kernel->bootstrap();

use App\Modules\GestionAcademica\Services\MateriasService;
use App\Modules\AdministracionUsuariosSeguridad\Services\BitacoraService;
use Illuminate\Support\Facades\DB;

echo "=== TEST CRUD DE MATERIAS Y BITÁCORA ===\n\n";

// 1. Test del servicio de Materias
echo "1. TEST MATERIAS SERVICE:\n";
$materiasService = app(MateriasService::class);

// Crear materia de prueba
$datosTest = [
    'sigla' => 'TESTM',
    'nombre' => 'Materia Test ' . uniqid()
];

try {
    $materia = $materiasService->create($datosTest);
    echo "   ✅ Materia creada: ID={$materia->id_materia}, Sigla={$materia->sigla}\n";
    
    // Buscar materia
    $materiaFind = $materiasService->find($materia->id_materia);
    echo "   ✅ Materia encontrada: {$materiaFind->nombre}\n";
    
    // Actualizar materia
    $datosUpdate = ['nombre' => 'Materia Test Actualizada'];
    $materiaUpdate = $materiasService->update($materia->id_materia, $datosUpdate);
    echo "   ✅ Materia actualizada: {$materiaUpdate->nombre}\n";
    
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// 2. Test del servicio de Bitácora
echo "\n2. TEST BITÁCORA SERVICE:\n";

try {
    // Obtener o crear un usuario de prueba
    $usuario = DB::table('usuario')->first();
    
    if ($usuario) {
        $bitacoraService = app(BitacoraService::class);
        
        // Registrar acción en bitácora
        $resultado = $bitacoraService->registrar(
            "Test de registro en bitácora desde script",
            $usuario->id_usuario
        );
        
        if ($resultado) {
            echo "   ✅ Registro en bitácora exitoso\n";
        } else {
            echo "   ❌ Fallo al registrar en bitácora\n";
        }
        
        // Listar registros recientes
        $registros = DB::table('bitacora')
            ->orderBy('id_bitacora', 'desc')
            ->limit(5)
            ->get();
        
        echo "   📋 Últimos 5 registros en bitácora:\n";
        foreach ($registros as $reg) {
            echo "      - ID: {$reg->id_bitacora}, Acción: {$reg->accion}, Usuario: {$reg->id_usuario}\n";
        }
    } else {
        echo "   ⚠️ No hay usuarios en la BD para probar bitácora\n";
    }
    
} catch (\Exception $e) {
    echo "   ❌ Error: " . $e->getMessage() . "\n";
}

// 3. Verificar rutas
echo "\n3. VERIFICACIÓN DE RUTAS:\n";
$routes = [
    'materias.vista' => 'GET /materias',
    'materias.store' => 'POST /materias',
    'materias.update' => 'PUT /materias/{id}',
    'materias.destroy' => 'DELETE /materias/{id}',
];

foreach ($routes as $nombre => $metodo) {
    try {
        $ruta = route($nombre, ['id' => 1]);
        echo "   ✅ Ruta {$nombre} disponible\n";
    } catch (\Exception $e) {
        echo "   ❌ Ruta {$nombre} NO disponible\n";
    }
}

// 4. Listar todas las materias
echo "\n4. MATERIAS EN LA BD:\n";
$todasMaterias = DB::table('materia')->get();
echo "   Total: " . count($todasMaterias) . "\n";
foreach ($todasMaterias as $m) {
    echo "   - {$m->sigla}: {$m->nombre}\n";
}

echo "\n=== FIN TEST ===\n";
