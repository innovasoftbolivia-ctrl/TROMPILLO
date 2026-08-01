<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Piloto extends Model
{
    protected $table = 'pilotos';

    protected $fillable = [
        'empleado_id', 'licencia_numero', 'tipo_licencia', 'horas_vuelo',
        'vencimiento_licencia', 'vencimiento_medico', 'habilitaciones',
    ];

    protected $casts = [
        'vencimiento_licencia' => 'date',
        'vencimiento_medico' => 'date',
        'horas_vuelo' => 'decimal:1',
    ];

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }

    public function vuelosComoPiloto(): HasMany
    {
        return $this->hasMany(Vuelo::class, 'piloto_id');
    }

    public function vuelosComoCopiloto(): HasMany
    {
        return $this->hasMany(Vuelo::class, 'copiloto_id');
    }
}
