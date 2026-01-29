<?php

namespace App\Filament\Resources\Students\Schemas;

use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Schemas\Components\Section;
use Filament\Schemas\Schema;

class StudentForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Section::make()
                    ->columnSpanFull()
                    ->columns(3)
                    ->schema([
                        TextInput::make('nis')
                            ->required()
                            ->label('Kartu Absen')
                            ->unique(ignoreRecord: true),
                        TextInput::make('full_name')
                            ->required()
                            ->label('Nama Lengkap'),
                        Select::make('class_room_id')
                            ->required()
                            ->relationship('classRoom', 'name'),
                    ]),
                Section::make()
                    ->columnSpanFull()
                    ->schema([
                        TextInput::make('phone')
                            ->label('No. Telepon'),
                        Textarea::make('address')
                            ->label('Alamat')
                            ->rows(3),
                        Select::make('status')
                            ->label('Status')
                            ->options([
                                'active' => 'Aktif',
                                'inactive' => 'Tidak Aktif',
                            ])
                            ->default('active')
                            ->required(),
                    ]),
            ]);
    }
}
