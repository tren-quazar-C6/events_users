<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Venta extends Model
{
    protected $fillable = [
        'user_id', 'staff_id', 'tipo_venta', 'subtotal', 'cargo_servicio',
        'total', 'moneda', 'estado_pago', 'metodo_pago', 'referencia_interna',
        'referencia_wompi', 'transaccion_wompi_id', 'json_respuesta', 'fecha_pago',
    ];

    protected $casts = [
        'json_respuesta' => 'array',
        'fecha_pago'     => 'datetime',
        'subtotal'       => 'decimal:2',
        'cargo_servicio' => 'decimal:2',
        'total'          => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Venta $venta) {
            do {
                $ref = 'ORD-' . strtoupper(Str::random(6));
            } while (self::where('referencia_interna', $ref)->exists());

            $venta->referencia_interna = $ref;
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'venta_id');
    }

    public function transacciones(): HasMany
    {
        return $this->hasMany(TransaccionPago::class, 'venta_id');
    }
}
