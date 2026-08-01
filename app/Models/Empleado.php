<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Empleado extends Model
{
    protected $table = 'empleados';

    protected $fillable = [
        'persona_id',
        'nombres', 'apellidos', 'tipo_documento', 'numero_documento', 'cargo',
        'telefono', 'email', 'fecha_nacimiento', 'fecha_contratacion', 'salario', 'activo',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'fecha_contratacion' => 'date',
        'activo' => 'boolean',
    ];

    public function piloto(): HasOne
    {
        return $this->hasOne(Piloto::class);
    }

    public function persona(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombres} {$this->apellidos}";
    }
}
