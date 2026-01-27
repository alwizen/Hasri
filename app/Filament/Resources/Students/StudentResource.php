<?php

namespace App\Filament\Resources\Students;

use App\Filament\Resources\Students\Pages\ManageStudents;
use App\Models\Student;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use UnitEnum;

class StudentResource extends Resource
{
    protected static ?string $navigationLabel = 'Daftar Siswa';

    protected static string | UnitEnum | null $navigationGroup = 'Master Data';

    protected static ?string $label = "Daftar Siswa";

    protected static ?string $model = Student::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedUserGroup;

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nis')
                    ->required()
                    ->label('Kartu Absensi'),
                TextInput::make('full_name')
                    ->required()
                    ->label('Nama Lengkap'),
                Select::make('class_room_id')
                    ->required()
                    ->label('Kelas')
                    ->relationship('classRoom', 'name'),
                Textarea::make('address')
                    ->columnSpanFull()
                    ->label('Alamat'),
                TextInput::make('phone')
                    ->label('Nomor Telepon'),
                Select::make('status')
                    ->required()
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->default('active'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('nis')
                    ->searchable()
                    ->copyable(),
                TextColumn::make('full_name')
                    ->searchable()
                    ->label('Nama Lengkap'),
                TextColumn::make('classRoom.name')
                    ->numeric()
                    ->label('Kelas'),
                TextColumn::make('phone')
                    ->searchable()
                    ->label('Nomor Telepon'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => $state === 'active' ? 'success' : 'danger'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                SelectFilter::make('class_room_id')
                    ->label('Kelas')
                    ->relationship('classRoom', 'name'),
            ])
            ->recordActions([
                EditAction::make(),
                DeleteAction::make(),
            ])
            ->toolbarActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => ManageStudents::route('/'),
        ];
    }
}
