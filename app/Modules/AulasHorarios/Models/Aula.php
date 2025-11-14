<?php

namespace App\Modules\AulasHorarios\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Aula extends Model
{
    use HasFactory;

    protected $table = 'aula';
    protected $primaryKey = 'id_aula';
    public $timestamps = false;

    protected $fillable = [
        'nro_aula',
        'modulo',
    ];

    public function horarios()
    {
        return $this->hasMany(Horario::class, 'id_aula', 'id_aula');
    }
}
