<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Models\ClassRoom;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\IconPosition;
use Illuminate\Database\Eloquent\Builder;

class ManageStudents extends ManageRecords
{
    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('Semua')
                ->badge(\App\Models\Student::count()),
        ];

        foreach (ClassRoom::orderBy('name')->get() as $class) {
            $tabs[$class->id] = Tab::make($class->name)
                ->icon('heroicon-m-bookmark')
                ->iconPosition(IconPosition::After)
                ->badge(
                    \App\Models\Student::where('class_room_id', $class->id)->count()
                )
                ->modifyQueryUsing(
                    fn(Builder $query) =>
                    $query->where('class_room_id', $class->id)
                );
        }

        return $tabs;
    }


    protected static string $resource = StudentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
