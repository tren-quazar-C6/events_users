<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Ticket extends Model
{
    protected $fillable = [
        'unique_code',
        'purchase_id',
        'user_id',
        'event_id',
        'event_title',
        'event_date',
        'event_time',
        'venue',
        'city',
        'seat_row',
        'seat_number',
        'seat_section',
        'price',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (Ticket $ticket) {
            do {
                $code = 'BTC-' . strtoupper(Str::random(6));
            } while (self::where('unique_code', $code)->exists());

            $ticket->unique_code = $code;
        });
    }

    public function purchase(): BelongsTo
    {
        return $this->belongsTo(Purchase::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
