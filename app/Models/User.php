<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'rol',
        'role_id',
        'empleado_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'profile_photo_url',
    ];

    /**
     * Empleado vinculado a esta cuenta de usuario (opcional).
     */
    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    /**
     * Rol RBAC del usuario (tabla roles). El atributo `rol` (enum) se conserva.
     */
    public function role()
    {
        return $this->belongsTo(Rol::class, 'role_id');
    }

    /**
     * ¿El usuario tiene el permiso indicado (por clave) vía su rol?
     */
    public function tienePermiso(string $clave): bool
    {
        return $this->role && $this->role->permisos->contains('clave', $clave);
    }

    /**
     * Reservas registradas por este usuario (agente de ventas).
     */
    public function reservas()
    {
        return $this->hasMany(Reserva::class, 'usuario_id');
    }
}
