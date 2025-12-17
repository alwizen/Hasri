<?php

namespace App\Filament\Resources\Attendances;

use App\Filament\Resources\Attendances\Pages\ManageAttendances;
use App\Models\Attendance;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Forms\Components\Toggle;
use Filament\Infolists\Components\IconEntry;
use Filament\Infolists\Components\Section as InfolistSection;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use pxlrbt\FilamentExcel\Actions\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class AttendanceResource extends Resource
{
    protected static ?string $model = Attendance::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedClipboardDocument;

    protected static ?string $navigationLabel = 'Rekap Absensi';

    protected static ?string $modelLabel = 'Absensi';

    protected static ?string $pluralModelLabel = 'Absensi';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                Select::make('teacher_id')
                    ->label('Nama Lengkap')
                    ->relationship('teacher', 'name')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->columnSpanFull(),

                DatePicker::make('date')
                    ->label('Tanggal')
                    ->required()
                    ->default(now())
                    ->native(false),

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

                TimePicker::make('check_in')
                    ->label('Jam Masuk')
                    ->placeholder('-')
                    ->seconds(true)
                    ->native(false)
                    ->nullable(),

                TimePicker::make('check_out')
                    ->label('Jam Keluar')
                    ->placeholder('-')
                    ->seconds(true)
                    ->native(false)
                    ->nullable(),

                Toggle::make('is_late')
                    ->label('Terlambat?')
                    ->default(false)
                    ->inline(false),

                TextInput::make('late_minutes')
                    ->label('Menit Terlambat')
                    ->numeric()
                    ->suffix('menit')
                    ->nullable()
                    ->helperText('Diisi otomatis oleh sistem saat check-in.')
                    ->disabled(),

                Toggle::make('is_early_leave')
                    ->label('Pulang Lebih Awal?')
                    ->default(false)
                    ->inline(false),

                // TextInput::make('early_leave_minutes')
                //     ->label('Menit Lebih Awal')
                //     ->numeric()
                //     ->suffix('menit')
                //     ->nullable()
                //     ->helperText('Diisi otomatis oleh sistem saat check-out.')
                //     ->disabled(),

                TextInput::make('permission_note')
                    ->label('Keterangan Izin')
                    ->nullable()
                    ->columnSpanFull()
                    ->visible(fn($get) => $get('status') === 'izin'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('teacher.name')
                    ->label('Nama Lengkap'),
                TextEntry::make('date')
                    ->label('Tanggal')
                    ->date('d F Y'),
                TextEntry::make('status')
                    ->label('Status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'masuk' => 'success',
                        'izin' => 'warning',
                        'absen' => 'danger',
                        default => 'gray',
                    }),

                TextEntry::make('check_in')
                    ->label('Jam Masuk')
                    ->time('H:i:s')
                    ->placeholder('-')
                    ->icon('heroicon-o-clock'),
                TextEntry::make('check_out')
                    ->label('Jam Keluar')
                    ->time('H:i:s')
                    ->placeholder('-')
                    ->icon('heroicon-o-clock'),

                IconEntry::make('is_late')
                    ->label('Terlambat')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),

                TextEntry::make('late_minutes')
                    ->label('Menit Terlambat')
                    ->suffix(' menit')
                    ->placeholder('-')
                    ->color('danger'),

                IconEntry::make('is_early_leave')
                    ->label('Pulang Lebih Awal')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('warning')
                    ->falseColor('success'),
                // TextEntry::make('early_leave_minutes')
                //     ->label('Menit Lebih Awal')
                //     ->suffix(' menit')
                //     ->placeholder('-')
                //     ->color('warning'),

                TextEntry::make('permission_note')
                    ->label('Keterangan Izin')
                    ->placeholder('-')
                    ->columnSpanFull(),

                TextEntry::make('created_at')
                    ->label('Dibuat pada')
                    ->dateTime('d F Y H:i:s'),
                TextEntry::make('updated_at')
                    ->label('Diubah pada')
                    ->dateTime('d F Y H:i:s'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('teacher.name')
                    ->label('Nama Lengkap')
                    ->searchable()
                    ->sortable()
                    ->weight('medium'),

                TextColumn::make('date')
                    ->label('Tanggal')
                    ->date('d M Y')
                    ->sortable(),

                TextColumn::make('check_in')
                    ->label('Jam Masuk')
                    ->time('H:i:s')
                    ->sortable()
                    ->placeholder('-')
                    ->icon('heroicon-o-arrow-right-on-rectangle')
                    ->iconColor('success'),

                IconColumn::make('is_late')
                    ->label('Telat')
                    ->boolean()
                    ->trueIcon('heroicon-o-x-circle')
                    ->falseIcon('heroicon-o-check-circle')
                    ->trueColor('danger')
                    ->falseColor('success'),

                TextColumn::make('check_out')
                    ->label('Jam Pulang')
                    ->time('H:i:s')
                    ->sortable()
                    ->placeholder('-')
                    ->icon('heroicon-o-arrow-left-on-rectangle')
                    ->iconColor('danger'),

                // TextColumn::make('late_minutes')
                //     ->label('Menit Telat')
                //     ->numeric()
                //     ->sortable()
                //     ->placeholder('-')
                //     ->suffix(' mnt')
                //     ->color('danger')
                //     ->weight('medium'),

                // IconColumn::make('is_early_leave')
                //     ->label('Pulang Awal')
                //     ->boolean()
                //     ->trueIcon('heroicon-o-exclamation-circle')
                //     ->falseIcon('heroicon-o-check-circle')
                //     ->trueColor('warning')
                //     ->falseColor('success')
                //     ->toggleable(),

                // TextColumn::make('early_leave_minutes')
                //     ->label('Menit Lebih Awal')
                //     ->numeric()
                //     ->sortable()
                //     ->placeholder('-')
                //     ->suffix(' mnt')
                //     ->color('warning')
                //     ->weight('medium')
                //     ->toggleable(),

                TextColumn::make('status')
                    ->label('Status')
                    ->searchable()
                    ->sortable()
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'masuk' => 'success',
                        'izin' => 'warning',
                        'absen' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('permission_note')
                    ->label('Keterangan Izin')
                    ->limit(30)
                    ->toggleable(isToggledHiddenByDefault: true)
                    ->tooltip(fn($state) => $state),

                TextColumn::make('created_at')
                    ->label('Dibuat pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),

                TextColumn::make('updated_at')
                    ->label('Diubah pada')
                    ->dateTime('d M Y H:i')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('date', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->label('Status')
                    ->options([
                        'masuk' => 'Masuk',
                        'izin'  => 'Izin',
                        'absen' => 'Absen',
                    ])
                    ->native(false),

                SelectFilter::make('teacher')
                    ->label('Pilih Guru')
                    ->relationship('teacher', 'name')
                    ->searchable()
                    ->preload()
                    ->native(false),

                \Filament\Tables\Filters\Filter::make('date_range')
                    ->form([
                        DatePicker::make('date_from')
                            ->label('Dari Tanggal')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->placeholder('Pilih tanggal mulai'),
                        DatePicker::make('date_until')
                            ->label('Sampai Tanggal')
                            ->native(false)
                            ->displayFormat('d/m/Y')
                            ->placeholder('Pilih tanggal akhir'),
                    ])
                    ->query(function ($query, array $data) {
                        return $query
                            ->when(
                                $data['date_from'] ?? null,
                                fn($q, $date) => $q->where('date', '>=', $date)
                            )
                            ->when(
                                $data['date_until'] ?? null,
                                fn($q, $date) => $q->where('date', '<=', $date)
                            );
                    })
                    ->indicateUsing(function (array $data): array {
                        $indicators = [];

                        if ($data['date_from'] ?? null) {
                            $indicators[] = 'Dari: ' . \Carbon\Carbon::parse($data['date_from'])->format('d M Y');
                        }

                        if ($data['date_until'] ?? null) {
                            $indicators[] = 'Sampai: ' . \Carbon\Carbon::parse($data['date_until'])->format('d M Y');
                        }

                        return $indicators;
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
                    // ->withFilename(fn() => 'rekap-absensi-' . now()->format('Y-m-d-His')),
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageAttendances::route('/'),
        ];
    }
}
