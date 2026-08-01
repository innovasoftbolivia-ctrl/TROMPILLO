<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EnvioCarga extends Model
{
    protected $table = 'envios_carga';

    protected $fillable = [
        'guia', 'vuelo_id', 'remitente', 'remitente_documento', 'destinatario',
        'destinatario_documento', 'descripcion', 'peso_kg', 'valor_declarado',
        'costo_envio', 'estado',
    ];

    protected $casts = [
        'peso_kg' => 'decimal:2',
        'valor_declarado' => 'decimal:2',
        'costo_envio' => 'decimal:2',
    ];

    public function vuelo(): BelongsTo
    {
        return $this->belongsTo(Vuelo::class);
    }
}
