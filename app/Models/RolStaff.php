<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class RolStaff extends Model
{
    protected $table = 'rol_staffs';
    protected $fillable = ['nombre_rol', 'activo'];

    public function staffs(): HasMany
    {
        return $this->hasMany(Staff::class, 'rol_staff_id');
    }
}
