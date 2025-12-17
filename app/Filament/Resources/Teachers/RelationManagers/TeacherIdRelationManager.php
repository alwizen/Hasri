<?php

namespace App\Filament\Resources\Teachers\RelationManagers;

use App\Filament\Resources\Attendances\AttendanceResource;
use Filament\Actions\CreateAction;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Table;

class TeacherIdRelationManager extends RelationManager
{
    protected static string $relationship = 'teacher_id';

    protected static ?string $relatedResource = AttendanceResource::class;

    public function table(Table $table): Table
    {
        return $table
            ->headerActions([
                CreateAction::make(),
            ]);
    }
}
