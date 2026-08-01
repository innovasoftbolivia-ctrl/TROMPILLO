<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pasajero extends Model
{
    protected $table = 'pasajeros';

    protected $fillable = [
        'persona_id',
        'nombres', 'apellidos', 'tipo_documento', 'numero_documento', 'fecha_nacimiento',
        'nacionalidad', 'pais_id', 'telefono', 'email', 'peso_kg', 'contacto_emergencia',
        'telefono_emergencia',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'peso_kg' => 'decimal:2',
    ];

    public function pais(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Pais::class);
    }

    public function persona(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    public function boletos(): HasMany
    {
        return $this->hasMany(Boleto::class);
    }

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombres} {$this->apellidos}";
    }
}
