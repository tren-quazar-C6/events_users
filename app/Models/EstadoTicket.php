<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoTicket extends Model
{
    protected $table = 'ESTADO_TICKET';
    protected $primaryKey = 'id_estado_ticket';
    public $timestamps = false;

    protected $fillable = ['nombre_estado'];

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class, 'id_estado_ticket', 'id_estado_ticket');
    }
}
