<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AttendanceStudent extends Model
{
    public const STATUS_MASUK = 'masuk';
    public const STATUS_IZIN  = 'izin';
    public const STATUS_ABSEN = 'absen';
    public const STATUS_TERLAMBAT = 'terlambat';

    protected $fillable = [
        'student_id',
        'attendance_date',
        'check_in_at',
        'check_out_at',
        'status'
    ];

    protected $casts = [
        'attendance_date' => 'date',
        'check_in_at' => 'datetime',
        'check_out_at' => 'datetime',
    ];

    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }
    public function isPresent(): bool
    {
        return $this->status === self::STATUS_MASUK;
    }

    public function isPermission(): bool
    {
        return $this->status === self::STATUS_IZIN;
    }

    public function isAbsent(): bool
    {
        return $this->status === self::STATUS_ABSEN;
    }
}
