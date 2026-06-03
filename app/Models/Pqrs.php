<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pqrs extends Model
{
    public $timestamps = false;

    protected $table = 'PQRS';

    protected $primaryKey = 'id_pqrs';

    protected $fillable = [
        'id_usuario', 'tipo', 'asunto', 'estado', 'asignado_staff',
    ];

    protected $casts = [
        'fecha_creacion' => 'datetime',
        'fecha_ultima_respuesta' => 'datetime',
    ];

    public function mensajes(): HasMany
    {
        return $this->hasMany(PqrsMensaje::class, 'id_pqrs', 'id_pqrs');
    }
}
