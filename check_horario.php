<?php

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    // AULA
    $columns = DB::select("
        SELECT column_name, data_type, character_maximum_length
        FROM information_schema.columns 
        WHERE table_schema = 'public' 
        AND table_name = 'aula'
        ORDER BY ordinal_position
    ");
    
    echo "=== Estructura de la tabla AULA ===\n\n";
    foreach ($columns as $column) {
        $length = $column->character_maximum_length ? "({$column->character_maximum_length})" : '';
        echo "- {$column->column_name} : {$column->data_type}{$length}\n";
    }
    
    // HORARIO
    $columns = DB::select("
        SELECT column_name, data_type, character_maximum_length
        FROM information_schema.columns 
        WHERE table_schema = 'public' 
        AND table_name = 'horario'
        ORDER BY ordinal_position
    ");
    
    echo "\n=== Estructura de la tabla HORARIO ===\n\n";
    foreach ($columns as $column) {
        $length = $column->character_maximum_length ? "({$column->character_maximum_length})" : '';
        echo "- {$column->column_name} : {$column->data_type}{$length}\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
