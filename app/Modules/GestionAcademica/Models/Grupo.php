<?php

namespace App\Modules\GestionAcademica\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Grupo extends Model
{
    use HasFactory;

    protected $table = 'grupo';
    protected $primaryKey = 'id_grupo';
    public $timestamps = false;

    protected $fillable = [
        'codigo',
        'estado',
        'id_materia',
    ];

    protected $casts = [
        'estado' => 'boolean',
    ];

    public function materia()
    {
        return $this->belongsTo(Materia::class, 'id_materia', 'id_materia');
    }

    public function cargas()
    {
        return $this->hasMany(CargaHoraria::class, 'id_grupo', 'id_grupo');
    }
}

