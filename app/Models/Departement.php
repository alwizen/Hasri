<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Departement extends Model
{
    protected $fillable = [
        'name',
        'start_time',
        'end_time',
        'tolerance_late_minutes'
    ];
}
