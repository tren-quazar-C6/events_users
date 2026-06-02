<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Asiento extends Model
{
    protected $table = 'ASIENTOS';
    protected $primaryKey = 'id_asiento';
    public $timestamps = false;

    protected $fillable = ['id_zona', 'fila', 'numero', 'activo'];

    public function zona(): BelongsTo
    {
        return $this->belongsTo(Zona::class, 'id_zona', 'id_zona');
    }

    public function eventoAsientos(): HasMany
    {
        return $this->hasMany(EventoAsiento::class, 'id_asiento', 'id_asiento');
    }
}
