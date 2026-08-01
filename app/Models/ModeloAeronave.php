<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ModeloAeronave extends Model
{
    protected $table = 'modelos_aeronave';

    protected $fillable = [
        'fabricante', 'modelo', 'capacidad_pasajeros', 'capacidad_carga_kg',
        'peso_vacio_kg', 'peso_maximo_despegue_kg', 'autonomia_km', 'velocidad_crucero_kmh',
    ];

    public function aeronaves(): HasMany
    {
        return $this->hasMany(Aeronave::class);
    }
}
