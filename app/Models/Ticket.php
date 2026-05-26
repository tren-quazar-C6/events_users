<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Ticket extends Model
{
    protected $fillable = ['venta_id', 'estado_ticket_id', 'evento_asiento_id'];

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            do {
                $code = 'BTC-' . strtoupper(Str::random(6));
            } while (self::where('codigo_unico', $code)->exists());

            $ticket->codigo_unico = $code;
            $ticket->qr_token     = (string) Str::uuid();
        });
    }

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }

    public function estadoTicket(): BelongsTo
    {
        return $this->belongsTo(EstadoTicket::class, 'estado_ticket_id');
    }

    public function eventoAsiento(): BelongsTo
    {
        return $this->belongsTo(EventoAsiento::class, 'evento_asiento_id');
    }

    public function scans(): HasMany
    {
        return $this->hasMany(Scan::class, 'ticket_id');
    }
}
