<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EventoAsiento extends Model
{
    protected $table = 'EVENTO_ASIENTO';
    protected $primaryKey = 'id_evento_asiento';
    public $timestamps = false;

    protected $fillable = ['id_asiento', 'id_evento', 'estado'];

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class, 'id_evento', 'id_evento');
    }

    public function asiento(): BelongsTo
    {
        return $this->belongsTo(Asiento::class, 'id_asiento', 'id_asiento')->with('zona');
    }

    public function ticket(): HasOne
    {
        return $this->hasOne(Ticket::class, 'id_evento_asiento', 'id_evento_asiento');
    }

    public function isDisponible(): bool
    {
        return $this->estado === 'DISPONIBLE';
    }
}
