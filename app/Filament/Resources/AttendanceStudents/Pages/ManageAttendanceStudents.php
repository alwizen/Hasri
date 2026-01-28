<?php

namespace App\Filament\Resources\AttendanceStudents\Pages;

use App\Filament\Resources\AttendanceStudents\AttendanceStudentResource;
use App\Models\ClassRoom;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\IconPosition;
use Illuminate\Database\Eloquent\Builder;

class ManageAttendanceStudents extends ManageRecords
{
    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('Semua')
                ->badge(\App\Models\AttendanceStudent::count()),
        ];

        foreach (ClassRoom::orderBy('name')->get() as $class) {
            $tabs[$class->id] = Tab::make($class->name)
                ->icon('heroicon-m-bookmark')
                ->iconPosition(IconPosition::After)
                ->badge(
                    \App\Models\AttendanceStudent::whereHas('student', function ($query) use ($class) {
                        $query->where('class_room_id', $class->id);
                    })->count()
                )
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->whereHas('student', function ($q) use ($class) {
                        $q->where('class_room_id', $class->id);
                    })
                );
        }

        return $tabs;
    }

    protected static string $resource = AttendanceStudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
