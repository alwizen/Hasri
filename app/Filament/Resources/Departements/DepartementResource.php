<?php

namespace App\Filament\Resources\Departements;

use App\Filament\Resources\Departements\Pages\ManageDepartements;
use App\Models\Departement;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\TimePicker;
use Filament\Resources\Resource;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use UnitEnum;

class DepartementResource extends Resource
{
    protected static ?string $model = Departement::class;

    protected static ?string $navigationLabel = 'Jabatan';

    protected static ?string $label = 'Jabatan';

    public static function getNavigationBadge(): ?string
    {
        return static::getModel()::count();
    }

    protected static string | UnitEnum | null $navigationGroup = 'Master Data';


    protected static string|BackedEnum|null $navigationIcon = Heroicon::OutlinedCube;

    public static function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TimePicker::make('start_time')
                    ->native(false),
                TimePicker::make('end_time')
                    ->native(false),
                TextInput::make('tolerance_late_minutes')
                    ->numeric(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->label('Nama Jabatan'),
                TextColumn::make('start_time')
                    ->time('H:i')
                    ->label('Jam Berangkat')
                    ->suffix(' Wib')
                    ->sortable(),
                TextColumn::make('end_time')
                    ->time('H:i')
                    ->suffix(' Wib')
                    ->label('Jam Pulang')
                    ->sortable(),
                TextColumn::make('tolerance_late_minutes')
                    ->numeric()
                    ->label('Toeransi Keterlambatan')
                    ->suffix(' Menit')
                    ->sortable(),
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
            'index' => ManageDepartements::route('/'),
        ];
    }
}
