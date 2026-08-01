<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Boleto extends Model
{
    protected $table = 'boletos';

    protected $fillable = [
        'numero_boleto', 'reserva_id', 'pasajero_id', 'vuelo_id', 'asiento',
        'precio', 'equipaje_kg', 'checkin', 'estado',
    ];

    protected $casts = [
        'checkin' => 'boolean',
        'precio' => 'decimal:2',
        'equipaje_kg' => 'decimal:2',
    ];

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class);
    }

    public function pasajero(): BelongsTo
    {
        return $this->belongsTo(Pasajero::class);
    }

    public function vuelo(): BelongsTo
    {
        return $this->belongsTo(Vuelo::class);
    }
}
