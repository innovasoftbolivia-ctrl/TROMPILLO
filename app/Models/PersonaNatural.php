<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonaNatural extends Model
{
    protected $table = 'personas_naturales';

    protected $fillable = ['persona_id', 'nombres', 'apellidos', 'fecha_nacimiento', 'sexo'];

    protected $casts = ['fecha_nacimiento' => 'date'];

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }
}
