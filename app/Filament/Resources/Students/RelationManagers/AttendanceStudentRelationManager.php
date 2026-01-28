<?php

namespace App\Filament\Resources\Students\RelationManagers;

use Barryvdh\DomPDF\Facade\Pdf;
use Dom\Text;
use Filament\Actions\AssociateAction;
use Filament\Actions\BulkAction;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\CreateAction;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\DissociateAction;
use Filament\Actions\DissociateBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\TextInput;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Schemas\Schema;
use pxlrbt\FilamentExcel\Columns\Column;
// use Filament\Tables\Columns\Column;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class AttendanceStudentRelationManager extends RelationManager
{
    protected static string $relationship = 'attendanceRecords';

    protected static ?string $title = '';

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('attendance_date')
            ->columns([
                TextColumn::make('attendance_date')
                    ->date()
                    ->label('Tanggal'),
                TextColumn::make('check_in_at')
                    ->label('Masuk')
                    ->time('H:i'),
                TextColumn::make('check_out_at')
                    ->label('Pulang')
                    ->time('H:i'),
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
                Filter::make('attendance_date')
                    ->form([
                        DatePicker::make('from')
                            ->label('Dari Tanggal'),

                        DatePicker::make('until')
                            ->label('Sampai Tanggal'),
                    ])
                    ->query(function (Builder $query, array $data) {
                        return $query
                            ->when(
                                $data['from'] ?? null,
                                fn(Builder $query, $date) =>
                                $query->whereDate('attendance_date', '>=', $date)
                            )
                            ->when(
                                $data['until'] ?? null,
                                fn(Builder $query, $date) =>
                                $query->whereDate('attendance_date', '<=', $date)
                            );
                    }),
            ])

            ->headerActions([
                // CreateAction::make(),
                // AssociateAction::make(),
            ])
            ->recordActions([
                // EditAction::make(),
                // DissociateAction::make(),
                // DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    BulkAction::make('printPdf')
                        ->label('Cetak PDF')
                        ->icon('heroicon-o-printer')
                        ->color('primary')
                        ->action(function ($records) {
                            $student = $this->getOwnerRecord();

                            $records = $records->sortBy('attendance_date');

                            $pdf = Pdf::loadView('pdf.attendance-student', [
                                'student' => $student,
                                'records' => $records,
                            ]);

                            return response()->streamDownload(
                                fn() => print($pdf->output()),
                                'absensi-' . str($student->full_name)->slug('_') . '.pdf'
                            );
                        }),

                    // Excel tetap ada
                    ExportBulkAction::make()
                        ->exports([
                            ExcelExport::make()
                                ->withColumns([
                                    Column::make('Tanggal')
                                        ->getStateUsing(
                                            fn($record) =>
                                            optional($record->attendance_date)?->format('Y-m-d')
                                        ),
                                    Column::make('Masuk')
                                        ->getStateUsing(
                                            fn($record) =>
                                            optional($record->check_in_at)?->format('H:i')
                                        ),
                                    Column::make('Pulang')
                                        ->getStateUsing(
                                            fn($record) =>
                                            optional($record->check_out_at)?->format('H:i')
                                        ),
                                    Column::make('Status')
                                        ->getStateUsing(
                                            fn($record) =>
                                            ucfirst($record->status)
                                        ),
                                ])
                                ->withFilename(function () {
                                    $student = $this->getOwnerRecord();

                                    $name  = str($student->full_name)->slug('_');
                                    $class = str(optional($student->classRoom)->name)->slug('_');

                                    return "absensi-{$name}-{$class}-" . now()->format('Y-m-d');
                                }),
                        ]),
                ]),
            ]);
    }
}
