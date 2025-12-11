<?php

namespace App\Filament\Resources\Teachers;

use App\Filament\Resources\Teachers\Pages\ManageTeachers;
use App\Models\Teacher;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class TeacherResource extends Resource
{
    protected static ?string $model = Teacher::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedAcademicCap;

    protected static ?string $navigationLabel = 'Daftar Guru';

    protected static ?string $label = "Daftar Guru";

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nip')
                    ->label('NIP')
                    ->numeric(),
                TextInput::make('name')
                    ->required()
                    ->label('Nama Lengkap'),
                TextInput::make('rfid_uid')
                    ->label('RFID (Kartu Absen)')
                    ->numeric(),
                Select::make('departement_id')
                    ->relationship('departement', 'name')
                    ->required()
                    ->label('Jabatan'),
                TextInput::make('telp')
                    ->label('No Telp'),
                TextInput::make('address')
                    ->label('Alamat'),
                FileUpload::make('photo')
                    ->label('Foto Guru')
                    ->image()
                    ->columnSpanFull(),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nip')
                    ->placeholder('-'),
                TextEntry::make('name'),
                TextEntry::make('rfid_uid')
                    ->placeholder('-'),
                TextEntry::make('departement_id')
                    ->numeric(),
                TextEntry::make('telp')
                    ->placeholder('-'),
                TextEntry::make('address')
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
                ImageColumn::make('photo')
                    ->label('Foto Guru')
                    ->circular()
                    ->default(asset('img/ico.svg'))
                    ->alignCenter(),
                TextColumn::make('nip')
                    ->searchable()
                    ->label('NIP'),
                TextColumn::make('name')
                    ->searchable()
                    ->label('Nama Lengkap'),
                TextColumn::make('rfid_uid')
                    ->searchable()
                    ->label('Kartu Absen'),
                TextColumn::make('departement.name')
                    ->badge()
                    ->label('Jabatan')
                    ->sortable(),
                TextColumn::make('telp')
                    ->searchable()
                    ->label('No. Telp'),
                TextColumn::make('address')
                    ->searchable()
                    ->label('Alamat'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->recordActions([
                ViewAction::make(),
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
            'index' => ManageTeachers::route('/'),
        ];
    }
}
