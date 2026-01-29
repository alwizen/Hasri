<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\ClassRoom;
use App\Models\AttendanceStudent;

class AttendancePerClass extends StatsOverviewWidget
{
    protected ?string $heading = 'Absensi Per Kelas';

    // protected ?string $description = 'Menampilkan jumlah siswa yang hadir hari ini per kelas';

    protected ?string $pollingInterval = '10s';

    protected static ?int $sort = 1;

    protected static bool $isLazy = false;

    protected function getStats(): array
    {
        $today = now()->toDateString();
        $stats = [];

        $classes = ClassRoom::with('students')->get();

        foreach ($classes as $class) {
            $presentCount = AttendanceStudent::whereDate('attendance_date', $today)
                ->whereIn('status', [
                    AttendanceStudent::STATUS_MASUK,
                    AttendanceStudent::STATUS_TERLAMBAT,
                ])
                ->whereHas('student', function ($query) use ($class) {
                    $query->where('class_room_id', $class->id);
                })
                ->count();

            $totalStudents = $class->students->count();

            $stats[] = Stat::make(
                $class->name,
                "{$presentCount} / {$totalStudents} Siswa"
            )
                ->description('Masuk hari ini')
                ->color(
                    $presentCount === $totalStudents ? 'success' : 'warning'
                );
        }

        return $stats;
    }
}
