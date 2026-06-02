<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Evento extends Model
{
    protected $table = 'EVENTOS';
    protected $primaryKey = 'id_evento';
    public $timestamps = false;

    protected $fillable = [
        'id_tipo_evento', 'slug', 'nombre_evento', 'synopsis',
        'author', 'duration', 'poster_color', 'venue', 'city', 'price_from',
        'fecha_evento', 'fecha_inicio_ventas', 'fecha_fin_ventas',
        'capacidad_total', 'publicado', 'activo',
    ];

    protected $casts = [
        'synopsis'            => 'array',
        'fecha_evento'        => 'datetime',
        'fecha_inicio_ventas' => 'datetime',
        'fecha_fin_ventas'    => 'datetime',
        'publicado'           => 'boolean',
        'activo'              => 'boolean',
        'price_from'          => 'decimal:2',
    ];

    public function tipo(): BelongsTo
    {
        return $this->belongsTo(TipoEvento::class, 'id_tipo_evento', 'id_tipo_evento');
    }

    public function imagenes(): HasMany
    {
        return $this->hasMany(Imagen::class, 'evento_id', 'id_evento');
    }

    public function eventoAsientos(): HasMany
    {
        return $this->hasMany(EventoAsiento::class, 'id_evento', 'id_evento');
    }

    public function favoritos(): HasMany
    {
        return $this->hasMany(Favorito::class, 'evento_id', 'id_evento');
    }
}
