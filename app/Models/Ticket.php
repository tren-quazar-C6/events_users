<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Ticket extends Model
{
    protected $table = 'TICKETS';
    protected $primaryKey = 'id_ticket';
    public $timestamps = false;

    protected $fillable = ['id_venta', 'id_estado_ticket', 'id_evento_asiento', 'precio_pagado'];

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            do {
                $code = 'BTC-' . strtoupper(Str::random(6));
            } while (self::where('codigo_unico', $code)->exists());

            $ticket->codigo_unico    = $code;
            $ticket->qr_token        = Str::uuid()->toString();
            $ticket->fecha_generacion = now();
            $ticket->fecha_impresion  = now();
        });
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'id_venta', 'id_venta');
    }

    public function estadoTicket(): BelongsTo
    {
        return $this->belongsTo(EstadoTicket::class, 'id_estado_ticket', 'id_estado_ticket');
    }

    public function eventoAsiento(): BelongsTo
    {
        return $this->belongsTo(EventoAsiento::class, 'id_evento_asiento', 'id_evento_asiento');
    }
}
