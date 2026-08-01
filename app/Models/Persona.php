<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Persona extends Model
{
    protected $table = 'personas';

    protected $fillable = [
        'tipo_persona', 'tipo_documento', 'numero_documento', 'telefono', 'email', 'direccion', 'pais_id',
    ];

    public function natural(): HasOne
    {
        return $this->hasOne(PersonaNatural::class);
    }

    public function juridica(): HasOne
    {
        return $this->hasOne(PersonaJuridica::class);
    }

    public function pais(): BelongsTo
    {
        return $this->belongsTo(Pais::class);
    }

    /**
     * Nombre a mostrar según el tipo de persona.
     */
    public function getNombreCompletoAttribute(): string
    {
        if ($this->tipo_persona === 'juridica') {
            return $this->juridica?->razon_social ?? '—';
        }

        return trim(($this->natural?->nombres ?? '') . ' ' . ($this->natural?->apellidos ?? '')) ?: '—';
    }
}
