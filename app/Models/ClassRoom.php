<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassRoom extends Model
{
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'tolerance_late_minutes',
    ];

    public function students()
    {
        return $this->hasMany(Student::class, 'class_id');
    }
}
