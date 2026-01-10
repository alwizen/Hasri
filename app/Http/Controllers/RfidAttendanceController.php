<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Http\Request;

class RfidAttendanceController extends Controller
{
    public function index()
    {
        return view('rfid-attendance');
    }

    public function store(Request $request)
    {
        $request->validate([
            'rfid_uid' => 'required|string',
        ]);

        $rfid = trim($request->input('rfid_uid'));

        // Cari teacher berdasarkan rfid_uid
        $teacher = Teacher::where('rfid_uid', $rfid)->first();

        if (! $teacher) {
            return redirect()->route('rfid.attendance.form')
                ->with('error', "Teacher dengan RFID UID \"{$rfid}\" tidak ditemukan.");
        }

        $today = Carbon::now()->toDateString();

        // Cek apakah sudah ada attendance hari ini
        $attendance = Attendance::where('teacher_id', $teacher->id)
            ->whereDate('date', $today)
            ->first();

        $now = Carbon::now();

        // Jika belum ada record sama sekali -> buat check-in baru
        if (!$attendance) {
            $attendance = new Attendance([
                'teacher_id' => $teacher->id,
                'date' => $today,
            ]);

            $attendance->check_in = $now->toTimeString();
            $attendance->status = Attendance::STATUS_MASUK;

            // Hitung keterlambatan berdasarkan start_time dari department
            $this->calculateLateStatus($attendance, $teacher, $now);

            $attendance->save();

            $message = "{$teacher->name} berhasil check-in pada {$now->format('H:i:s')}";

            // Tambahkan info keterlambatan jika terlambat
            if ($attendance->is_late && $attendance->late_minutes) {
                $lateTime = $this->formatMinutesToTime($attendance->late_minutes);
                $message .= " (Terlambat {$lateTime})";
            }

            return redirect()->route('rfid.attendance.form')
                ->with('success', $message);
        }

        // Jika sudah ada check-in tapi belum check-out -> lakukan check-out
        if (!is_null($attendance->check_in) && is_null($attendance->check_out)) {
            $attendance->check_out = $now->toTimeString();

            // Hitung apakah pulang lebih awal dari end_time department
            $this->calculateEarlyLeaveStatus($attendance, $teacher, $now);

            $attendance->save();

            $message = "{$teacher->name} berhasil check-out pada {$now->format('H:i:s')}";

            // Tambahkan peringatan jika pulang lebih awal
            if ($attendance->is_early_leave && $attendance->early_leave_minutes) {
                $earlyTime = $this->formatMinutesToTime($attendance->early_leave_minutes);
                $message .= " (Pulang lebih awal {$earlyTime})";
            }

            return redirect()->route('rfid.attendance.form')
                ->with('success', $message);
        }

        // Jika sudah check-in dan check-out -> tolak absen lagi
        if (!is_null($attendance->check_in) && !is_null($attendance->check_out)) {
            return redirect()->route('rfid.attendance.form')
                ->with('warning', "{$teacher->name} sudah melakukan check-in ({$attendance->check_in}) dan check-out ({$attendance->check_out}) hari ini. Tidak bisa absen lagi.");
        }

        // Fallback jika ada kondisi tidak terduga
        return redirect()->route('rfid.attendance.form')
            ->with('error', "Terjadi kesalahan pada data absensi {$teacher->name}. Silakan hubungi admin.");
    }

    /**
     * Hitung status keterlambatan saat check-in
     */
    // private function calculateLateStatus(Attendance $attendance, Teacher $teacher, Carbon $now)
    // {
    //     $startTime = $teacher->departement->start_time ?? null;
    //     $tolerance = (int) ($teacher->departement->tolerance_late_minutes ?? 0);

    //     if ($startTime) {
    //         try {
    //             $start = Carbon::parse($startTime)->setDate($now->year, $now->month, $now->day);
    //             $diffMinutes = $now->diffInMinutes($start, false);

    //             // diffInMinutes dengan false returns negatif jika $now < $start
    //             $minutesLateFromStart = $diffMinutes > 0 ? $diffMinutes : 0;
    //             $minutesLateBeyondTolerance = max(0, $minutesLateFromStart - $tolerance);
    //             $isLate = $minutesLateFromStart > $tolerance;

    //             $attendance->is_late = $isLate;
    //             $attendance->late_minutes = $minutesLateBeyondTolerance > 0 ? $minutesLateBeyondTolerance : null;
    //         } catch (\Exception $e) {
    //             $attendance->is_late = false;
    //             $attendance->late_minutes = null;
    //         }
    //     } else {
    //         $attendance->is_late = false;
    //         $attendance->late_minutes = null;
    //     }
    // }

    private function calculateLateStatus(Attendance $attendance, Teacher $teacher, Carbon $now)
    {
        $startTime = $teacher->departement->start_time ?? null;
        $tolerance = (int) ($teacher->departement->tolerance_late_minutes ?? 0);

        if ($startTime) {
            try {
                $start = Carbon::parse($startTime)->setDate($now->year, $now->month, $now->day);

                // Jika check-in SETELAH start_time
                if ($now->gt($start)) {
                    $minutesLate = $start->diffInMinutes($now);

                    // Cek apakah melebihi toleransi
                    if ($minutesLate > $tolerance) {
                        $attendance->is_late = true;
                        $attendance->late_minutes = (int) round($minutesLate - $tolerance);
                    } else {
                        // Terlambat tapi masih dalam toleransi
                        $attendance->is_late = false;
                        $attendance->late_minutes = null;
                    }
                } else {
                    // Check-in tepat waktu atau lebih awal
                    $attendance->is_late = false;
                    $attendance->late_minutes = null;
                }
            } catch (\Exception $e) {
                $attendance->is_late = false;
                $attendance->late_minutes = null;
            }
        } else {
            $attendance->is_late = false;
            $attendance->late_minutes = null;
        }
    }

    /**
     * Hitung status pulang lebih awal saat check-out
     */
    private function calculateEarlyLeaveStatus(Attendance $attendance, Teacher $teacher, Carbon $now)
    {
        $endTime = $teacher->departement->end_time ?? null;

        if ($endTime) {
            try {
                $end = Carbon::parse($endTime)->setDate($now->year, $now->month, $now->day);

                // Jika check-out sebelum end_time
                if ($now->lt($end)) {
                    $minutesEarly = $now->diffInMinutes($end);

                    $attendance->is_early_leave = true;
                    $attendance->early_leave_minutes = $minutesEarly;
                } else {
                    $attendance->is_early_leave = false;
                    $attendance->early_leave_minutes = null;
                }
            } catch (\Exception $e) {
                $attendance->is_early_leave = false;
                $attendance->early_leave_minutes = null;
            }
        } else {
            $attendance->is_early_leave = false;
            $attendance->early_leave_minutes = null;
        }
    }

    /**
     * Format menit menjadi jam dan menit
     * 
     * @param int $minutes
     * @return string
     */
    private function formatMinutesToTime(int $minutes): string
    {
        if ($minutes < 60) {
            return "{$minutes} menit";
        }

        $hours = floor($minutes / 60);
        $remainingMinutes = $minutes % 60;

        if ($remainingMinutes === 0) {
            return "{$hours} jam";
        }

        return "{$hours} jam {$remainingMinutes} menit";
    }
}
