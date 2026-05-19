<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Purchase extends Model
{
    protected $fillable = [
        'reference',
        'user_id',
        'event_id',
        'event_title',
        'event_date',
        'event_time',
        'venue',
        'city',
        'subtotal',
        'service_fee',
        'total',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (Purchase $purchase) {
            do {
                $ref = 'ORD-' . strtoupper(Str::random(6));
            } while (self::where('reference', $ref)->exists());

            $purchase->reference = $ref;
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function tickets(): HasMany
    {
        return $this->hasMany(Ticket::class);
    }
}
