<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Venta extends Model
{
    protected $table = 'ventas';

    protected $fillable = [
        'numero', 'persona_id', 'usuario_id', 'reserva_id', 'fecha',
        'subtotal', 'descuento', 'total', 'estado', 'metodo_pago',
    ];

    protected $casts = [
        'fecha'     => 'datetime',
        'subtotal'  => 'decimal:2',
        'descuento' => 'decimal:2',
        'total'     => 'decimal:2',
    ];

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'persona_id');
    }

    public function vendedor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class);
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(VentaDetalle::class);
    }

    public function pagos(): HasMany
    {
        return $this->hasMany(Pago::class);
    }

    public function factura(): HasOne
    {
        return $this->hasOne(Factura::class);
    }
}
