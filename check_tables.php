<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo "=== Buscando tablas relacionadas ===\n";
$tables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public' AND (tablename LIKE '%grupo%' OR tablename LIKE '%asignacion%') ORDER BY tablename");
foreach($tables as $t) {
    echo "- {$t->tablename}\n";
}

echo "\n=== Todas las tablas ===\n";
$allTables = DB::select("SELECT tablename FROM pg_tables WHERE schemaname = 'public' ORDER BY tablename");
foreach($allTables as $t) {
    echo "- {$t->tablename}\n";
}
