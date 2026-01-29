<?php

namespace App\Filament\Resources\Students\Pages;

use App\Filament\Resources\Students\StudentResource;
use App\Models\ClassRoom;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ListRecords;
use Filament\Schemas\Components\Tabs\Tab;
use Filament\Support\Enums\IconPosition;
use Illuminate\Database\Eloquent\Builder;

class ListStudents extends ListRecords
{
    protected static string $resource = StudentResource::class;

    public function getTabs(): array
    {
        $tabs = [
            'all' => Tab::make('Semua')
                ->badge(\App\Models\Student::count()),
        ];

        foreach (ClassRoom::orderBy('name')->get() as $class) {
            $tabs[$class->id] = Tab::make('Kelas ' . $class->name)
                ->icon('heroicon-m-user-circle')
                ->iconPosition(IconPosition::Before)
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

    protected function getHeaderActions(): array
    {
        return [
            CreateAction::make(),
        ];
    }
}
