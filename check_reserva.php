<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Estructura de la tabla 'reserva' ===\n\n";

try {
    $columns = DB::select("
        SELECT column_name, data_type, character_maximum_length, is_nullable, column_default
        FROM information_schema.columns 
        WHERE table_name = 'reserva' 
        ORDER BY ordinal_position
    ");

    if (empty($columns)) {
        echo "⚠️  La tabla 'reserva' no existe en la base de datos.\n";
    } else {
        foreach ($columns as $col) {
            $type = $col->data_type;
            if ($col->character_maximum_length) {
                $type .= "({$col->character_maximum_length})";
            }
            $nullable = $col->is_nullable === 'YES' ? 'NULL' : 'NOT NULL';
            $default = $col->column_default ? " DEFAULT {$col->column_default}" : '';
            
            echo sprintf("%-25s %-20s %-10s %s\n", 
                $col->column_name, 
                $type, 
                $nullable,
                $default
            );
        }
        
        echo "\n=== Conteo de registros ===\n";
        $count = DB::table('reserva')->count();
        echo "Total de reservas: {$count}\n";
        
        if ($count > 0) {
            echo "\n=== Primeros 3 registros ===\n";
            $registros = DB::table('reserva')->limit(3)->get();
            foreach ($registros as $reg) {
                print_r($reg);
            }
        }
    }
} catch (Exception $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
