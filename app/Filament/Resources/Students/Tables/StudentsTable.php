<?php

namespace App\Filament\Resources\Students\Tables;

use App\Filament\Resources\Students\RelationManagers\AttendanceStudentRelationManager;
use Filament\Actions\BulkActionGroup;
use Filament\Actions\DeleteAction;
use Filament\Actions\DeleteBulkAction;
use Filament\Actions\EditAction;
use Filament\Actions\ViewAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Guava\FilamentModalRelationManagers\Actions\RelationManagerAction;
use pxlrbt\FilamentExcel\Actions\ExportBulkAction;

class StudentsTable
{
    public static function configure(Table $table): Table
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
                RelationManagerAction::make('lesson-relation-manager')
                    ->label('Absensi')
                    ->slideOver()
                    ->icon('heroicon-o-clock')
                    ->relationManager(AttendanceStudentRelationManager::make()),
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
}
