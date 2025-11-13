<?php

namespace App\Modules\GestionAcademica\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Docente extends Model
{
    use HasFactory;

    protected $table = 'docente';
    protected $primaryKey = 'id_docente';
    public $timestamps = false;

    protected $fillable = [
        'cod_docente',
        'nit',
        'maestria',
        'carrera',
        'id_usuario',
    ];

    public function usuario()
    {
        return $this->belongsTo(\App\Modules\AdministracionUsuariosSeguridad\Models\Usuario::class, 'id_usuario', 'id_usuario');
    }

    public function cargas()
    {
        return $this->hasMany(CargaHoraria::class, 'id_docente', 'id_docente');
    }
}

