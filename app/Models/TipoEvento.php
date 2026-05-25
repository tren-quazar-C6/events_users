<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoEvento extends Model
{
    protected $fillable = ['nombre_tipo', 'activo'];

    public function eventos(): HasMany
    {
        return $this->hasMany(Evento::class);
    }
}
