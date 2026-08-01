<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Vuelo extends Model
{
    protected $table = 'vuelos';

    protected $fillable = [
        'numero_vuelo', 'ruta_id', 'origen_id', 'destino_id', 'aeronave_id',
        'piloto_id', 'copiloto_id', 'tipo', 'salida_programada', 'llegada_programada',
        'salida_real', 'llegada_real', 'asientos_disponibles', 'precio', 'estado',
        'observaciones',
    ];

    protected $casts = [
        'salida_programada' => 'datetime',
        'llegada_programada' => 'datetime',
        'salida_real' => 'datetime',
        'llegada_real' => 'datetime',
    ];

    public function ruta(): BelongsTo
    {
        return $this->belongsTo(Ruta::class);
    }

    public function origen(): BelongsTo
    {
        return $this->belongsTo(Aeropuerto::class, 'origen_id');
    }

    public function destino(): BelongsTo
    {
        return $this->belongsTo(Aeropuerto::class, 'destino_id');
    }

    public function aeronave(): BelongsTo
    {
        return $this->belongsTo(Aeronave::class);
    }

    public function piloto(): BelongsTo
    {
        return $this->belongsTo(Piloto::class, 'piloto_id');
    }

    public function copiloto(): BelongsTo
    {
        return $this->belongsTo(Piloto::class, 'copiloto_id');
    }

    public function tripulacion(): HasMany
    {
        return $this->hasMany(TripulacionVuelo::class);
    }

    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class);
    }

    public function boletos(): HasMany
    {
        return $this->hasMany(Boleto::class);
    }

    public function enviosCarga(): HasMany
    {
        return $this->hasMany(EnvioCarga::class);
    }
}
