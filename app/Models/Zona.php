<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Zona extends Model
{
    protected $table = 'ZONAS';
    protected $primaryKey = 'id_zona';
    public $timestamps = false;

    protected $fillable = ['nombre_zona', 'color_hex', 'activo'];

    public function asientos(): HasMany
    {
        return $this->hasMany(Asiento::class, 'id_zona', 'id_zona');
    }
}
