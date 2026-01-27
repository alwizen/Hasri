<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\AttendanceStudent;
use Carbon\Carbon;
use Illuminate\Http\Request;

class StudentAttendanceController extends Controller
{
    public function index()
    {
        return view('studentTap');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nis' => 'required|string',
        ]);

        $student = Student::with('classRoom')
            ->where('nis', $request->nis)
            ->first();

        if (! $student) {
            return back()->with('error', 'NIS tidak ditemukan.');
        }

        $today = Carbon::today();
        $now = Carbon::now();

        $attendance = AttendanceStudent::where('student_id', $student->id)
            ->whereDate('attendance_date', $today)
            ->first();

        // =========================
        // CHECK IN
        // =========================
        if (! $attendance) {

            $status = AttendanceStudent::STATUS_MASUK;

            // Cek keterlambatan berdasarkan kelas
            if ($student->classRoom) {
                $class = $student->classRoom;

                $startTime = Carbon::createFromTimeString($class->start_time);
                $lateLimit = $startTime->copy()
                    ->addMinutes($class->tolerance_late_minutes);

                if ($now->greaterThan($lateLimit)) {
                    $status = AttendanceStudent::STATUS_TERLAMBAT;
                }
            }

            AttendanceStudent::create([
                'student_id' => $student->id,
                'attendance_date' => $today,
                'check_in_at' => $now,
                'status' => $status,
            ]);

            return back()->with(
                'success',
                "Check-in berhasil: {$student->full_name} (Status: {$status})"
            );
        }

        // =========================
        // CHECK OUT
        // =========================
        if (! $attendance->check_out_at) {
            $attendance->update([
                'check_out_at' => $now,
            ]);

            return back()->with(
                'success',
                "Check-out berhasil: {$student->full_name}"
            );
        }

        // =========================
        // SUDAH LENGKAP
        // =========================
        return back()->with(
            'info',
            "Siswa sudah check-in dan check-out hari ini: {$student->full_name}"
        );
    }
}
