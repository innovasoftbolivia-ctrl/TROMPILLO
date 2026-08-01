<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Factura extends Model
{
    protected $table = 'facturas';

    protected $fillable = [
        'numero_factura', 'venta_id', 'persona_id', 'nit', 'razon_social',
        'fecha_emision', 'subtotal', 'descuento', 'impuesto_iva', 'total', 'estado',
    ];

    protected $casts = [
        'fecha_emision' => 'datetime',
        'subtotal'      => 'decimal:2',
        'descuento'     => 'decimal:2',
        'impuesto_iva'  => 'decimal:2',
        'total'         => 'decimal:2',
    ];

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class);
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }
}
