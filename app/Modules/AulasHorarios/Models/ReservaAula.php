<?php

namespace App\Modules\AulasHorarios\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\AdministracionUsuariosSeguridad\Models\Usuario;

class ReservaAula extends Model
{
    protected $table = 'reserva';
    protected $primaryKey = 'id_reserva';
    public $timestamps = false;

    protected $fillable = [
        'fecha',
        'dia',
        'hora_i',
        'hora_f',
        'motivo',
        'id_aula',
        'id_usuario'
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    // Relaciones
    public function aula(): BelongsTo
    {
        return $this->belongsTo(Aula::class, 'id_aula', 'id_aula');
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'id_usuario', 'id_usuario');
    }

    // Scopes útiles
    public function scopeFecha($query, $fecha)
    {
        return $query->whereDate('fecha', $fecha);
    }

    public function scopeProximas($query)
    {
        return $query->where('fecha', '>=', now()->toDateString())
                     ->orderBy('fecha', 'asc')
                     ->orderBy('hora_i', 'asc');
    }

    public function scopePorAula($query, $idAula)
    {
        return $query->where('id_aula', $idAula);
    }
}
