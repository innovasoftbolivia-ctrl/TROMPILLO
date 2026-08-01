<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Reserva extends Model
{
    protected $table = 'reservas';

    protected $fillable = [
        'codigo', 'vuelo_id', 'usuario_id', 'pasajero_id', 'estado', 'total',
        'fecha_reserva', 'notas',
    ];

    protected $casts = [
        'fecha_reserva' => 'datetime',
        'total' => 'decimal:2',
    ];

    public function vuelo(): BelongsTo
    {
        return $this->belongsTo(Vuelo::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function titular(): BelongsTo
    {
        return $this->belongsTo(Pasajero::class, 'pasajero_id');
    }

    public function boletos(): HasMany
    {
        return $this->hasMany(Boleto::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function venta(): \Illuminate\Database\Eloquent\Relations\HasOne
    {
        return $this->hasOne(Venta::class);
    }
}
