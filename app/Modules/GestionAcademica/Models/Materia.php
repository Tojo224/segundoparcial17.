<?php

namespace App\Modules\GestionAcademica\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Materia extends Model
{
    use HasFactory;

    protected $table = 'materia';
    protected $primaryKey = 'id_materia';
    public $timestamps = false;

    protected $fillable = [
        'sigla',
        'nombre',
    ];

    public function grupos()
    {
        return $this->hasMany(Grupo::class, 'id_materia', 'id_materia');
    }
}

