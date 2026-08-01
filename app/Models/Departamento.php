<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Departamento extends Model
{
    protected $table = 'departamentos';

    protected $fillable = ['nombre'];

    public function ciudades(): HasMany
    {
        return $this->hasMany(Ciudad::class);
    }
}
