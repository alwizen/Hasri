<?php

namespace App\Filament\Resources\AttendanceStudents;

use App\Filament\Resources\AttendanceStudents\Pages\ManageAttendanceStudents;
use App\Models\AttendanceStudent;
use App\Models\ClassRoom;
use App\Models\Student;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Tables\Filters\Filter;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\ExportBulkAction;
use UnitEnum;

class AttendanceStudentResource extends Resource
{
    protected static ?string $model = AttendanceStudent::class;

    protected static ?string $navigationLabel = 'Rekap Absensi Siswa';

    protected static string | UnitEnum | null $navigationGroup = 'Rekapitulasi';

    protected static ?string $modelLabel = 'Absensi Siswa';

    protected static ?string $pluralModelLabel = 'Absensi Siswa';

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocumentCheck;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('student_id')
                    ->required()
                    ->relationship('student', 'full_name'),
                DatePicker::make('attendance_date')
                    ->required(),
                DateTimePicker::make('check_in_at')
                    ->required(),
                Select::make('status')
                    ->label('Status')
                    ->options([
                        'masuk' => 'Masuk',
                        'izin'  => 'Izin',
                        'absen' => 'Absen',
                    ])
                    ->default('masuk')
                    ->required()
                    ->native(false),
                DateTimePicker::make('check_out_at'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('student.full_name'),
                TextEntry::make('attendance_date')
                    ->date(),
                TextEntry::make('check_in_at')
                    ->dateTime(),
                TextEntry::make('check_out_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('created_at')
                    ->dateTime()
                    ->placeholder('-'),
                TextEntry::make('updated_at')
                    ->dateTime()
                    ->placeholder('-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('student.nis')
                    ->label('Kartu Absen')
                    ->copyable()
                    ->searchable(),
                TextColumn::make('student.full_name')
                    ->label('Nama Siswa')
                    ->searchable(),
                TextColumn::make('attendance_date')
                    ->date()
                    ->label('Tanggal'),
                TextColumn::make('check_in_at')
                    ->dateTime()
                    ->label('Masuk'),
                TextColumn::make('check_out_at')
                    ->dateTime()
                    ->label('Keluar'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'masuk' => 'success',
                        'izin'  => 'warning',
                        'absen' => 'danger',
                        'terlambat' => 'warning',
                        default => 'secondary',
                    }),
                TextColumn::make('created_at')
                    ->dateTime()

                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()

                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('student_id')
                    ->label('Siswa')
                    ->preload()
                    ->multiple()
                    ->searchable()
                    ->relationship('student', 'full_name'),

                SelectFilter::make('status')
                    ->options([
                        'masuk' => 'Masuk',
                        'izin'  => 'Izin',
                        'absen' => 'Absen',
                        'terlambat' => 'Terlambat',
                    ]),

                // 🔥 FILTER RANGE TANGGAL
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
                                $data['from'],
                                fn(Builder $query, $date) =>
                                $query->whereDate('attendance_date', '>=', $date)
                            )
                            ->when(
                                $data['until'],
                                fn(Builder $query, $date) =>
                                $query->whereDate('attendance_date', '<=', $date)
                            );
                    }),
            ])
            ->recordActions([
                ViewAction::make(),
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    ExportBulkAction::make(),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAttendanceStudents::route('/'),
        ];
    }
}
