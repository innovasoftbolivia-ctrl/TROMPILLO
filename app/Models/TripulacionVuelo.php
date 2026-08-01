<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TripulacionVuelo extends Model
{
    protected $table = 'tripulacion_vuelo';

    protected $fillable = ['vuelo_id', 'empleado_id', 'rol'];

    public function vuelo(): BelongsTo
    {
        return $this->belongsTo(Vuelo::class);
    }

    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class);
    }
}
