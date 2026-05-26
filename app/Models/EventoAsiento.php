<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class EventoAsiento extends Model
{
    protected $fillable = [
        'evento_id', 'asiento_id', 'precio', 'estado',
        'fecha_reserva', 'reserva_expira',
    ];

    protected $casts = [
        'precio'          => 'decimal:2',
        'fecha_reserva'   => 'datetime',
        'reserva_expira'  => 'datetime',
    ];

    public function evento(): BelongsTo
    {
        return $this->belongsTo(Evento::class, 'evento_id');
    }

    public function asiento(): BelongsTo
    {
        return $this->belongsTo(Asiento::class, 'asiento_id');
    }

    public function ticket(): HasOne
    {
        return $this->hasOne(Ticket::class, 'evento_asiento_id');
    }

    public function isDisponible(): bool
    {
        if ($this->estado === 'RESERVADO' && $this->reserva_expira?->isPast()) {
            $this->update(['estado' => 'DISPONIBLE', 'fecha_reserva' => null, 'reserva_expira' => null]);
            return true;
        }

        return $this->estado === 'DISPONIBLE';
    }
}
