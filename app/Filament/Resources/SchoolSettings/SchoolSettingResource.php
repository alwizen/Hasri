<?php

namespace App\Filament\Resources\SchoolSettings;

use App\Filament\Resources\SchoolSettings\Pages\ManageSchoolSettings;
use App\Models\SchoolSetting;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class SchoolSettingResource extends Resource
{
    protected static ?string $model = SchoolSetting::class;

    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCog6Tooth;

    protected static ?string $navigationLabel = 'Pengaturan';

    protected static ?int $navigationSort = 2;

    protected static ?string $label = 'Pengaturan';

    protected static string | UnitEnum | null $navigationGroup = 'Lainnya';

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('school_name')
                    ->required()
                    ->label('Nama Sekolah'),
                Textarea::make('school_address')
                    ->required()
                    ->label('Alamat Sekolah'),
                TextInput::make('phone')
                    ->label('Telp')
                    ->default(null),
                TextInput::make('email')
                    ->label('Email')
                    ->email(),
                TextInput::make('principal_name')
                    ->required()
                    ->label('Nama Kepala Sekolah'),
                FileUpload::make('logo')
                    ->directory('img')
                    ->label('Logo Sekolah'),
                FileUpload::make('principal_signature')
                    ->directory('img')
                    ->label('Tanda Tangan Kepala Sekolah'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('school_name'),
                TextEntry::make('school_address')
                    ->columnSpanFull(),
                TextEntry::make('phone')
                    ->placeholder('-'),
                TextEntry::make('email')
                    ->label('Email address')
                    ->placeholder('-'),
                TextEntry::make('principal_name'),
                TextEntry::make('principal_signature')
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
                TextColumn::make('school_name')
                    ->label('Nama Sekolah'),
                TextColumn::make('phone')
                    ->label('Telp'),
                TextColumn::make('email')
                    ->label('Email'),
                TextColumn::make('principal_name')
                    ->label('Nama Kepala Sekolah'),
                ImageColumn::make('principal_signature')
                    ->label('Tanda Tangan Kepala Sekolah'),
                ImageColumn::make('logo')
                    ->label('Logo Sekolah'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
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
            'index' => ManageSchoolSettings::route('/'),
        ];
    }
}
