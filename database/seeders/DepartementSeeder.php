<?php

namespace Database\Seeders;

use App\Models\Departement;
use Illuminate\Database\Seeder;

class DepartementSeeder extends Seeder
{
    public function run(): void
    {
        $departements = [
            [
                'name' => 'Kepala Sekolah',
                'start_time' => '07:00:00',
                'end_time' => '14:00:00',
                'tolerance_late_minutes' => 30,
            ],
            [
                'name' => 'Guru',
                'start_time' => '07:15:00',
                'end_time' => '14:30:00',
                'tolerance_late_minutes' => 30,
            ],
            [
                'name' => 'Tata Usaha',
                'start_time' => '07:30:00',
                'end_time' => '15:00:00',
                'tolerance_late_minutes' => 30,
            ],

        ];

        foreach ($departements as $dept) {
            Departement::create($dept);
        }
    }
}
