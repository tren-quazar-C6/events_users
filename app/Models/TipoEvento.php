<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoEvento extends Model
{
    protected $table = 'TIPO_EVENTO';
    protected $primaryKey = 'id_tipo_evento';
    public $timestamps = false;

    protected $fillable = ['nombre_tipo', 'activo'];

    public function eventos(): HasMany
    {
        return $this->hasMany(Evento::class, 'id_tipo_evento', 'id_tipo_evento');
    }
}
