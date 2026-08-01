<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aeronave extends Model
{
    protected $table = 'aeronaves';

    protected $fillable = [
        'modelo_aeronave_id',
        'matricula', 'modelo', 'fabricante', 'ano_fabricacion', 'capacidad_pasajeros',
        'capacidad_carga_kg', 'peso_vacio_kg', 'peso_maximo_despegue_kg', 'autonomia_km',
        'velocidad_crucero_kmh', 'horas_vuelo_totales', 'fecha_ultima_revision',
        'fecha_proxima_revision', 'estado',
    ];

    protected $casts = [
        'fecha_ultima_revision' => 'date',
        'fecha_proxima_revision' => 'date',
        'horas_vuelo_totales' => 'decimal:1',
    ];

    public function modeloAeronave(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(ModeloAeronave::class);
    }

    public function vuelos(): HasMany
    {
        return $this->hasMany(Vuelo::class);
    }

    public function mantenimientos(): HasMany
    {
        return $this->hasMany(Mantenimiento::class);
    }
}
