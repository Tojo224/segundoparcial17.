<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RolesSeeder extends Seeder
{
    public function run()
    {
        // Verificar si ya existen roles
        $rolesCount = DB::table('roles')->count();
        
        if ($rolesCount === 0) {
            DB::table('roles')->insert([
                [
                    'nombre' => 'Administrador',
                    'descripcion' => 'Mayoría de funcionalidades de administración del sistema'
                ],
                [
                    'nombre' => 'Docente',
                    'descripcion' => 'Registro de asistencia y funciones docentes'
                ],
                [
                    'nombre' => 'Auxiliar',
                    'descripcion' => 'Visibilidad de aulas durante clases'
                ],
                [
                    'nombre' => 'Autoridad',
                    'descripcion' => 'Funciones de docente + gestión de docentes'
                ],
                [
                    'nombre' => 'Coordinador',
                    'descripcion' => 'Gestión académica y de docentes'
                ]
            ]);

            echo "Roles creados exitosamente.\n";
        } else {
            echo "Ya existen roles en la base de datos.\n";
        }
    }
}
