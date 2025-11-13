<?php

namespace App\Modules\GestionAcademica\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CargaHoraria extends Model
{
    use HasFactory;

    protected $table = 'carga_horaria';
    protected $primaryKey = 'id_carga';
    public $timestamps = false;

    protected $fillable = [
        'horas_asignadas',
        'id_docente',
        'id_grupo',
        'id_gestion',
    ];

    public function docente()
    {
        return $this->belongsTo(Docente::class, 'id_docente', 'id_docente');
    }

    public function grupo()
    {
        return $this->belongsTo(Grupo::class, 'id_grupo', 'id_grupo');
    }
}

