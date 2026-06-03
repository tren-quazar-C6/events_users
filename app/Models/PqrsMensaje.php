<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PqrsMensaje extends Model
{
    public $timestamps = false;

    protected $table = 'PQRS_MENSAJE';

    protected $primaryKey = 'id_mensaje';

    protected $fillable = [
        'id_pqrs', 'remitente', 'id_remitente', 'mensaje',
    ];

    protected $casts = [
        'fecha' => 'datetime',
    ];

    public function pqrs(): BelongsTo
    {
        return $this->belongsTo(Pqrs::class, 'id_pqrs', 'id_pqrs');
    }
}
