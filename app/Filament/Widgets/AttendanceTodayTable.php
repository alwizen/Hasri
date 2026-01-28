<?php

namespace App\Filament\Widgets;

use App\Models\Attendance;
use Carbon\Carbon;
use Filament\Actions\BulkActionGroup;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class AttendanceTodayTable extends TableWidget
{
    protected int | string | array $columnSpan = 'full';

    protected static ?int $sort = 1;

    protected static ?string $heading = 'Daftar Kehadiran Guru Hari Ini';

    public function table(Table $table): Table
    {
        return $table
            ->poll('10s')
            ->defaultSort('updated_at', 'desc')
            ->query(function (): Builder {
                return Attendance::query()
                    ->whereDate('date', Carbon::today());
            })
            ->paginated(false)
            ->columns([
                TextColumn::make('teacher.name')
                    ->label('Nama Lengkap')
                    ->weight('medium'),

                TextColumn::make('teacher.departement.name')
                    ->label('Departemen')
                    ->badge()
                    ->color('info'),

                TextColumn::make('teacher.departement.start_time')
                    ->label('Jadwal Masuk')
                    ->time('H:i')
                    ->iconColor('primary')
                    ->suffix(' Wib'),

                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y'),

                TextColumn::make('check_in')
                    ->label('Jam Masuk')
                    ->time('H:i:s')
                    ->placeholder('-')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->iconColor('success'),

                TextColumn::make('check_out')
                    ->label('Jam Pulang')
                    ->time('H:i:s')

                    ->placeholder('-')
                    ->icon('heroicon-o-arrow-left-on-rectangle')
                    ->iconColor('danger'),

                TextColumn::make('is_late')
                    ->label('Keterlambatan')
                    ->formatStateUsing(function ($record): string {
                        if ($record->is_late && $record->late_minutes) {
                            return "Terlambat ({$record->late_minutes} menit)";
                        }
                        return $record->is_late ? 'Terlambat' : 'Tepat Waktu';
                    })
                    ->badge()
                    ->color(fn($record): string => $record->is_late ? 'danger' : 'success'),

                TextColumn::make('status')
                    ->label('Status')


                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'masuk' => 'success',
                        'izin' => 'warning',
                        'absen' => 'danger',
                        default => 'gray',
                    }),

            ])
            ->filters([
                //
            ])
            ->headerActions([
                //
            ])
            ->recordActions([
                //
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    //
                ]),
            ]);
    }
}
