<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Mantenimiento extends Model
{
    protected $table = 'mantenimientos';

    protected $fillable = [
        'aeronave_id', 'tecnico_id', 'tipo', 'descripcion', 'fecha_inicio',
        'fecha_fin', 'horas_vuelo_aeronave', 'costo', 'estado',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
    ];

    public function aeronave(): BelongsTo
    {
        return $this->belongsTo(Aeronave::class);
    }

    public function tecnico(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'tecnico_id');
    }
}
