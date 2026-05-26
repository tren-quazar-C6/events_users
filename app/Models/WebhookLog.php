<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebhookLog extends Model
{
    protected $table = 'webhook_logs';

    protected $fillable = ['proveedor', 'evento', 'payload_json', 'procesado', 'error'];

    protected $casts = [
        'payload_json' => 'array',
        'procesado'    => 'boolean',
    ];
}
