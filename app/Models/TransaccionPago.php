<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransaccionPago extends Model
{
    protected $table = 'transacciones_pagos';

    protected $fillable = [
        'venta_id', 'proveedor_pago', 'transaccion_ext_id', 'estado',
        'metodo_pago', 'monto', 'moneda', 'referencia', 'respuesta_json',
    ];

    protected $casts = [
        'respuesta_json' => 'array',
        'monto'          => 'decimal:2',
    ];

    public function venta(): BelongsTo
    {
        return $this->belongsTo(Venta::class, 'venta_id');
    }
}
