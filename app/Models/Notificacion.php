<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Notificacion extends Model
{
    protected $table = 'NOTIFICACIONES';

    protected $fillable = ['id_usuario', 'titulo', 'mensaje', 'leido'];

    protected $casts = ['leido' => 'boolean'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
