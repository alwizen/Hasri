<?php

namespace App\Filament\Widgets;

use App\Models\AttendanceStudent;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AttendanceStudentTable extends TableWidget
{
    protected static ?int $sort = 2;

    protected int | string | array $columnSpan = 'full';

    protected static ?string $heading = 'Daftar Kehadiran Siswa Hari Ini';

    public function table(Table $table): Table
    {
        return $table
            ->poll('10s')
            ->defaultSort('updated_at', 'desc')
            ->query(function (): Builder {
                return AttendanceStudent::query()
                    ->whereDate('attendance_date', Carbon::today());
            })
            ->columns([
                TextColumn::make('student.full_name')
                    ->label('Nama Siswa'),
                TextColumn::make('student.classRoom.name')
                    ->badge()
                    ->label('Kelas'),
                TextColumn::make('attendance_date')
                    ->date()
                    ->label('Tanggal'),
                TextColumn::make('check_in_at')
                    ->dateTime()
                    ->label('Jam Masuk'),
                TextColumn::make('check_out_at')
                    ->dateTime()
                    ->label('Jam Pulang'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'masuk' => 'success',
                        'izin'  => 'warning',
                        'absen' => 'danger',
                        'terlambat' => 'warning',
                        default => 'secondary',
                    }),
            ])
            ->filters([
                SelectFilter::make('student.class_room_id')
                    ->label('Kelas')
                    ->relationship('student.classRoom', 'name'),
                // SelectFilter::make('student_id')
                //     ->label('Siswa')
                //     ->preload()
                //     ->multiple()
                //     ->searchable()
                //     ->relationship('student', 'full_name'),

                // SelectFilter::make('status')
                //     ->options([
                //         'masuk' => 'Masuk',
                //         'izin'  => 'Izin',
                //         'absen' => 'Absen',
                //         'terlambat' => 'Terlambat',
                //     ]),

            ]);
    }
}
