<?php

namespace App\Modules\AulasHorarios\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Modules\GestionAcademica\Models\CargaHoraria;

class Horario extends Model
{
    use HasFactory;

    protected $table = 'horario';
    protected $primaryKey = 'id_horario';
    public $timestamps = false;

    protected $fillable = [
        'dia',
        'hora_i',
        'hora_f',
        'id_carga',
        'id_aula',
    ];

    // Relación con carga horaria (docente + grupo)
    public function carga()
    {
        return $this->belongsTo(CargaHoraria::class, 'id_carga', 'id_carga');
    }

    // Relación con aula
    public function aula()
    {
        return $this->belongsTo(Aula::class, 'id_aula', 'id_aula');
    }

    // Accessor para obtener el docente
    public function getDocenteAttribute()
    {
        return $this->carga?->docente;
    }

    // Accessor para obtener el grupo
    public function getGrupoAttribute()
    {
        return $this->carga?->grupo;
    }
}
