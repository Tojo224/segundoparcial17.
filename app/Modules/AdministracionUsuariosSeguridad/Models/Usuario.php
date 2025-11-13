<?php

namespace App\Modules\AdministracionUsuariosSeguridad\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Usuario extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    protected $table = 'usuario';
    protected $primaryKey = 'id_usuario';
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'ci',
        'nombre',
        'telefono',
        'direccion',
        'correo',
        'sexo',
        'estado_civil',
        'estado',
        'contraseña',
        'id_rol',
        'cambiar_contra',
    ];
    public function rol()
    {
        return $this->belongsTo(Rol::class, 'id_rol');
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'contraseña',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'contraseña' => 'hashed',
            'cambiar_contra' => 'boolean',
        ];
    }

    public function getAuthPassword()
    {
        return $this->attributes['contraseña'] ?? null;
    }
}
