<?php

namespace App\Filament\Resources\SchoolSettings\Pages;

use App\Filament\Resources\SchoolSettings\SchoolSettingResource;
use App\Models\SchoolSetting;
use Filament\Actions\CreateAction;
use Filament\Resources\Pages\ManageRecords;

class ManageSchoolSettings extends ManageRecords
{
    protected static string $resource = SchoolSettingResource::class;

    public function mount(): void
    {
        $record = SchoolSetting::first();

        if ($record) {
            // Kalau sudah ada data, langsung edit
            $this->mountAction('edit', [
                'record' => $record->getKey(),
            ]);
        }
    }

    protected function getHeaderActions(): array
    {
        // Tombol Create hanya muncul kalau belum ada record
        if (SchoolSetting::count() === 0) {
            return [
                CreateAction::make()
                    ->label('Buat Pengaturan Sekolah'),
            ];
        }

        return [];
    }

    protected function canCreate(): bool
    {
        // Cegah create kalau sudah ada data
        return SchoolSetting::count() === 0;
    }
}
