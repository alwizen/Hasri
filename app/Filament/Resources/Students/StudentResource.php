<?php

namespace App\Filament\Resources\Students;

use App\Filament\Clusters\Student\StudentCluster;
use App\Filament\Resources\Students\Pages\ManageStudents;
use App\Models\ClassRoom;
use App\Models\Student;
use BackedEnum;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Infolists\Components\TextEntry;
use Filament\Navigation\NavigationItem;
use Filament\Resources\Resource;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Schemas\Schema;
use Filament\Support\Icons\Heroicon;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Actions\ExportBulkAction;
use UnitEnum;

class StudentResource extends Resource
{
    // protected static ?string $cluster = StudentCluster::class;

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
                    ->numeric()
                    ->unique(ignoreRecord: true)
                    ->label('Kartu Absensi'),
                TextInput::make('full_name')
                    ->required()
                    ->label('Nama Lengkap'),
                Select::make('class_room_id')
                    ->required()
                    ->label('Kelas')
                    ->columnSpanFull()
                    ->relationship('classRoom', 'name'),
                Textarea::make('address')
                    ->columnSpanFull()
                    ->label('Alamat'),
                TextInput::make('phone')
                    ->label('Nomor Telepon')
                    ->numeric(),
                Select::make('status')
                    ->required()
                    ->options([
                        'active' => 'Active',
                        'inactive' => 'Inactive',
                    ])
                    ->default('active'),
            ]);
    }

    public static function infolist(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextEntry::make('nis')
                    ->label('Kartu Absensi'),
                TextEntry::make('full_name')
                    ->label('Nama Lengkap'),
                TextEntry::make('classRoom.name')
                    ->numeric()
                    ->label('Kelas'),
                TextEntry::make('address')
                    ->placeholder('-')
                    ->columnSpanFull(),
                TextEntry::make('phone')
                    ->placeholder('-'),
                TextEntry::make('status'),
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
                TextColumn::make('nis')
                    ->searchable()
                    ->label('Kartu Absensi')
                    ->copyable(),
                TextColumn::make('full_name')
                    ->searchable()
                    ->label('Nama Lengkap'),
                TextColumn::make('classRoom.name')
                    ->badge()
                    ->label('Kelas'),
                TextColumn::make('phone')
                    ->searchable()
                    ->label('Nomor Telepon'),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => $state === 'active' ? 'success' : 'danger'),
                TextColumn::make('address')
                    ->limit(50, end: ' (more)')
                    ->placeholder('-')
                    ->label('Alamat'),
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
            'index' => ManageStudents::route('/'),
        ];
    }
}
