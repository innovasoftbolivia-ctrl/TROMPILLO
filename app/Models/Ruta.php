<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ruta extends Model
{
    protected $table = 'rutas';

    protected $fillable = [
        'origen_id', 'destino_id', 'distancia_km', 'duracion_estimada_min',
        'precio_base', 'activa',
    ];

    protected $casts = [
        'activa' => 'boolean',
    ];

    public function origen(): BelongsTo
    {
        return $this->belongsTo(Aeropuerto::class, 'origen_id');
    }

    public function destino(): BelongsTo
    {
        return $this->belongsTo(Aeropuerto::class, 'destino_id');
    }

    public function vuelos(): HasMany
    {
        return $this->hasMany(Vuelo::class);
    }
}
