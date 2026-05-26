<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Staff extends Model
{
    protected $table = 'staffs';

    protected $fillable = ['rol_staff_id', 'nombre', 'email', 'password_hash', 'activo'];

    protected $hidden = ['password_hash'];

    public function rol(): BelongsTo
    {
        return $this->belongsTo(RolStaff::class, 'rol_staff_id');
    }

    public function ventas(): HasMany
    {
        return $this->hasMany(Venta::class, 'staff_id');
    }

    public function scans(): HasMany
    {
        return $this->hasMany(Scan::class, 'staff_id');
    }
}
