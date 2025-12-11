<?php

namespace App\Models;

use Deprecated;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Teacher extends Model
{
    protected $table = 'teachers';
    protected $guarded = ['id'];

    public function departement(): BelongsTo
    {
        return $this->belongsTo(Departement::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }
}
