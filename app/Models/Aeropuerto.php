<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Aeropuerto extends Model
{
    protected $table = 'aeropuertos';

    protected $fillable = [
        'codigo_oaci', 'codigo_iata', 'nombre', 'ciudad', 'ciudad_id', 'departamento', 'pais',
        'tipo', 'latitud', 'longitud', 'elevacion_pies', 'longitud_pista_m',
        'superficie_pista', 'activo',
    ];

    protected $casts = [
        'activo' => 'boolean',
        'latitud' => 'decimal:7',
        'longitud' => 'decimal:7',
    ];

    public function ciudadRef(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Ciudad::class, 'ciudad_id');
    }

    public function rutasOrigen(): HasMany
    {
        return $this->hasMany(Ruta::class, 'origen_id');
    }

    public function rutasDestino(): HasMany
    {
        return $this->hasMany(Ruta::class, 'destino_id');
    }
}
