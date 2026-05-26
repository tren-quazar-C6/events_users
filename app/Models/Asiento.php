<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asiento extends Model
{
    protected $fillable = ['zona_id', 'fila', 'numero', 'codigo_asiento', 'pos_x', 'pos_y', 'activo'];

    public function zona(): BelongsTo
    {
        return $this->belongsTo(Zona::class, 'zona_id');
    }

    public function eventoAsientos(): HasMany
    {
        return $this->hasMany(EventoAsiento::class, 'asiento_id');
    }
}
