<?php

namespace App\Models;

use App\Models\AttendanceStudent;
use App\Models\ClassRoom;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    protected $fillable = [
        'nis',
        'full_name',
        'class_room_id',
        'address',
        'phone',
        'status',
    ];

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_room_id');
    }

    public function attendanceRecords()
    {
        return $this->hasMany(AttendanceStudent::class, 'student_id');
    }

    // public function attendanceStudent()
    // {
    //     return $this->hasMany(AttendanceStudent::class, 'student_id');
    // }
}
