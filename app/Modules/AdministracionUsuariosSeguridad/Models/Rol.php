<?php

namespace App\Modules\AdministracionUsuariosSeguridad\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rol extends Model
{
    use HasFactory;

    protected $table = 'roles';
    protected $primaryKey = 'id_rol';
    public $timestamps = false;

    protected $fillable = [
        'id_rol',
        'nombre',
        'descripcion',
    ];
}
