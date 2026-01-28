<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('nis')
                    ->required(),
                TextInput::make('full_name')
                    ->required(),
                TextInput::make('class_room_id')
                    ->required()
                    ->numeric(),
                Textarea::make('address')
                    ->default(null)
                    ->columnSpanFull(),
                TextInput::make('phone')
                    ->tel()
                    ->default(null),
                Select::make('status')
                    ->options(['active' => 'Active', 'inactive' => 'Inactive'])
                    ->default('active')
                    ->required(),
            ]);
    }
}
