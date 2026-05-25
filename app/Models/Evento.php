<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evento extends Model
{
    protected $fillable = [
        'tipo_evento_id', 'staff_id', 'slug', 'nombre_evento', 'synopsis',
        'author', 'duration', 'poster_color', 'venue', 'city', 'price_from',
        'fecha_evento', 'fecha_inicio_ventas', 'fecha_fin_ventas',
        'capacidad_total', 'publicado', 'activo',
        'fecha_cancelacion', 'motivo_cancelacion',
    ];

    protected $casts = [
        'synopsis'           => 'array',
        'fecha_evento'       => 'datetime',
        'fecha_inicio_ventas'=> 'datetime',
        'fecha_fin_ventas'   => 'datetime',
        'fecha_cancelacion'  => 'datetime',
        'publicado'          => 'boolean',
        'activo'             => 'boolean',
        'price_from'         => 'decimal:2',
    ];

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoEvento::class, 'tipo_evento_id');
    }

    public function staff(): BelongsTo
    {
        return $this->belongsTo(Staff::class, 'staff_id');
    }

    public function imagenes(): HasMany
    {
        return $this->hasMany(Imagen::class, 'evento_id');
    }

    public function eventoAsientos(): HasMany
    {
        return $this->hasMany(EventoAsiento::class, 'evento_id');
    }

    public function favoritos(): HasMany
    {
        return $this->hasMany(Favorito::class, 'evento_id');
    }
}
