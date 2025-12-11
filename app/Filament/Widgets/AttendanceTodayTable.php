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

    protected static ?string $heading = 'Daftar Kehadiran Guru Hari Ini';

    public function table(Table $table): Table
    {
        return $table
            ->poll('10s')
            ->query(function (): Builder {
                return Attendance::query()
                    ->whereDate('date', Carbon::today());
            })
            ->paginated(false)
            ->columns([
                TextColumn::make('teacher.name')
                    ->label('Nama Lengkap')
                    ->weight('medium'),

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
                    ->sortable()
                    ->placeholder('-')
                    ->icon('heroicon-o-arrow-left-on-rectangle')
                    ->iconColor('danger'),

                IconColumn::make('is_late')
                    ->label('Telat')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'masuk' => 'success',
                        'izin' => 'warning',
                        'absen' => 'danger',
                        default => 'gray',
                    }),

                // TextColumn::make('permission_note')
                //     ->label('Keterangan Izin')
                //     ->limit(30)
                //     // ->toggleable(isToggledHiddenByDefault: true)
                //     ->tooltip(fn($state) => $state),
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
