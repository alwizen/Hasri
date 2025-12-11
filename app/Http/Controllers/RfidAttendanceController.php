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
        
        // \Log::info('Attendance Check', [
        //     'teacher_id' => $teacher->id,
        //     'today' => $today,
        //     'found' => $attendance ? 'yes' : 'no',
        //     'check_in' => $attendance?->check_in,
        //     'check_out' => $attendance?->check_out,
        // ]);

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

            return redirect()->route('rfid.attendance.form')
                ->with('success', "{$teacher->name} berhasil check-in pada {$now->format('H:i:s')}");
        }

        // Jika sudah ada check-in tapi belum check-out -> lakukan check-out
        if (!is_null($attendance->check_in) && is_null($attendance->check_out)) {
            $attendance->check_out = $now->toTimeString();
            
            // Hitung apakah pulang lebih awal dari end_time department
            $this->calculateEarlyLeaveStatus($attendance, $teacher, $now);
            
            $attendance->save();

            $message = "{$teacher->name} berhasil check-out pada {$now->format('H:i:s')}";
            
            // Tambahkan peringatan jika pulang lebih awal
            if ($attendance->is_early_leave) {
                $message .= " (Pulang lebih awal {$attendance->early_leave_minutes} menit)";
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
    private function calculateLateStatus(Attendance $attendance, Teacher $teacher, Carbon $now)
    {
        $startTime = $teacher->departement->start_time ?? null;
        $tolerance = (int) ($teacher->departement->tolerance_late_minutes ?? 0);

        if ($startTime) {
            try {
                $start = Carbon::parse($startTime)->setDate($now->year, $now->month, $now->day);
                $diffMinutes = $now->diffInMinutes($start, false);

                // diffInMinutes dengan false returns negatif jika $now < $start
                $minutesLateFromStart = $diffMinutes > 0 ? $diffMinutes : 0;
                $minutesLateBeyondTolerance = max(0, $minutesLateFromStart - $tolerance);
                $isLate = $minutesLateFromStart > $tolerance;

                $attendance->is_late = $isLate;
                $attendance->late_minutes = $minutesLateBeyondTolerance > 0 ? $minutesLateBeyondTolerance : null;
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
}