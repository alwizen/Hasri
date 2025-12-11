<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Attendance extends Model
{
    // konstanta status supaya tidak typo di kode lain
    public const STATUS_MASUK = 'masuk';
    public const STATUS_IZIN  = 'izin';
    public const STATUS_ABSEN = 'absen';

    protected $fillable = [
        'teacher_id',
        'date',
        'check_in',
        'check_out',
        'status',
        'permission_note',
        'is_late',
        'late_minutes',
        'is_early_leave',
        'early_leave_minutes',
    ];

    protected $casts = [
        'date' => 'date',
        'is_late' => 'boolean',
        'is_early_leave' => 'boolean',
    ];

    public function teacher(): BelongsTo
    {
        return $this->belongsTo(Teacher::class);
    }

    // helper kecil
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

    public function hasLateIssue(): bool
    {
        return $this->is_late === true;
    }

    public function hasEarlyLeaveIssue(): bool
    {
        return $this->is_early_leave === true;
    }
}