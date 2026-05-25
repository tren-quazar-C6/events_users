<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class EstadoTicket extends Model
{
    protected $fillable = ['nombre_estado'];

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
