<?php

namespace App\Filament\Resources\AttendanceStudents\Pages;

use App\Filament\Resources\AttendanceStudents\AttendanceStudentResource;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageAttendanceStudents extends ManageRecords
{
    protected static string $resource = AttendanceStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
