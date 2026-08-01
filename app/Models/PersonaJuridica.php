<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersonaJuridica extends Model
{
    protected $table = 'personas_juridicas';

    protected $fillable = ['persona_id', 'razon_social', 'nit', 'representante_legal'];

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class);
    }
}
