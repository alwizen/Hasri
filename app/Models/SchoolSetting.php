<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SchoolSetting extends Model
{
    protected $fillable = [
        'school_name',
        'school_address',
        'phone',
        'email',
        'principal_name',
        'principal_signature',
        'logo'
    ];
}
