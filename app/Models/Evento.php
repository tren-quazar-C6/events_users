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
        'capacidad_total', 'publicado', 'activo', 'ruta_url',
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

    // Extraer precio desde la descripción
    public function getPriceFromDescriptionAttribute(): int
    {
        // Buscar patrón: "Precio desde: $XXX.XXX COP" o "$180.000 COP"
        if (preg_match('/\$\s*([\d\.]+)\s*COP/', $this->descripcion ?? '', $matches)) {
            $price = str_replace('.', '', $matches[1]);
            return (int) $price;
        }

        return 0;
    }

    // Getters para información del evento
    public function getVenueNameAttribute(): string
    {
        return 'Teatro Quasar';
    }

    public function getCityNameAttribute(): string
    {
        return 'Medellín';
    }

    public function getFormattedDateAttribute(): string
    {
        return $this->fecha_evento?->translatedFormat('j \\d\\e F \\d\\e Y') ?? 'Por confirmar';
    }

    public function getFormattedTimeAttribute(): string
    {
        return $this->fecha_evento?->format('H:i') . 'h' ?? 'Por confirmar';
    }

    public function getFormattedPriceAttribute(): string
    {
        $price = $this->price_from_description;
        return $price > 0 ? number_format($price, 0, ',', '.') : 'Por confirmar';
    }
}
