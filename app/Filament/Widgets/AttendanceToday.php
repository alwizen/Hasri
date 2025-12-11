<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use App\Models\Teacher;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Carbon;

class AttendanceToday extends StatsOverviewWidget
{

    protected ?string $pollingInterval = '10s';

    protected function getStats(): array
    {
        $today = Carbon::today();

        // Total guru
        $totalTeachers = Teacher::count();

        // Jumlah hadir (status = 'masuk')
        $presentToday = Attendance::whereDate('date', $today)
            ->where('status', Attendance::STATUS_MASUK)
            ->count();

        // Tidak masuk (izin + absen)
        $notPresentToday = Attendance::whereDate('date', $today)
            ->whereIn('status', [
                Attendance::STATUS_IZIN,
                Attendance::STATUS_ABSEN,
            ])
            ->count();

        return [
            Stat::make('Total Guru', $totalTeachers)
                ->description('Jumlah seluruh guru'),

            Stat::make('Hari Ini Masuk', $presentToday)
                ->description('Guru yang hadir hari ini')
                ->color('success'),

            Stat::make('Hari Ini Tidak Masuk', $notPresentToday)
                ->description('Izin & Absen')
                ->color('danger'),
        ];
    }
}
