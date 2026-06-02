<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Venta extends Model
{
    protected $table = 'VENTAS';
    protected $primaryKey = 'id_venta';
    public $timestamps = false;

    protected $fillable = [
        'id_usuario', 'id_staff', 'tipo_venta', 'total', 'moneda',
        'estado_pago', 'metodo_pago', 'referencia_interna',
        'fecha_pago', 'fecha_venta',
    ];

    protected $casts = [
        'fecha_pago'  => 'datetime',
        'fecha_venta' => 'datetime',
        'total'       => 'decimal:2',
    ];

    protected static function booted(): void
    {
        static::creating(function (Venta $venta) {
            if (blank($venta->referencia_interna)) {
                do {
                    $ref = 'ORD-' . strtoupper(Str::random(6));
                } while (self::where('referencia_interna', $ref)->exists());

                $venta->referencia_interna = $ref;
            }
        });
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'id_venta', 'id_venta');
    }
}
