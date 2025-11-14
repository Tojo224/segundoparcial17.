<?php

namespace App\Modules\ControlAsistencia\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Modules\AulasHorarios\Models\Horario;

class Asistencia extends Model
{
    protected $table = 'asistencia';
    protected $primaryKey = 'id_asistencia';
    public $timestamps = false;

    protected $fillable = [
        'fecha_registro',
        'tipo',
        'id_horario'
    ];

    protected $casts = [
        'fecha_registro' => 'date',
    ];

    // Estados posibles para tipo de asistencia
    public const TIPO_PRESENTADO = 'Presentado';
    public const TIPO_AUSENTE = 'Ausente';
    public const TIPO_JUSTIFICADO = 'Justificado';
    public const TIPO_TARDANZA = 'Tardanza';

    public static function getTiposAsistencia(): array
    {
        return [
            self::TIPO_PRESENTADO,
            self::TIPO_AUSENTE,
            self::TIPO_JUSTIFICADO,
            self::TIPO_TARDANZA,
        ];
    }

    // Relaciones
    public function horario(): BelongsTo
    {
        return $this->belongsTo(Horario::class, 'id_horario', 'id_horario');
    }

    // Scopes útiles
    public function scopeFecha($query, $fecha)
    {
        return $query->whereDate('fecha_registro', $fecha);
    }

    public function scopeRangoFecha($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_registro', [$fechaInicio, $fechaFin]);
    }

    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    public function scopePorHorario($query, $idHorario)
    {
        return $query->where('id_horario', $idHorario);
    }

    public function scopeRecientes($query, $limite = 10)
    {
        return $query->orderBy('fecha_registro', 'desc')
                     ->orderBy('id_asistencia', 'desc')
                     ->limit($limite);
    }
}
