<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ClassRoom;

class ClassRoomSeeder extends Seeder
{
    public function run(): void
    {
        $classRooms = [
            [
                'name' => 'X A',
                'start_time' => '07:00:00',
                'end_time' => '14:00:00',
                'tolerance_late_minutes' => 10,
            ],
            [
                'name' => 'X B',
                'start_time' => '07:00:00',
                'end_time' => '14:00:00',
                'tolerance_late_minutes' => 10,
            ],
            [
                'name' => 'XI A',
                'start_time' => '07:15:00',
                'end_time' => '14:30:00',
                'tolerance_late_minutes' => 10,
            ],
            [
                'name' => 'XI B',
                'start_time' => '07:15:00',
                'end_time' => '14:30:00',
                'tolerance_late_minutes' => 10,
            ],
            [
                'name' => 'XII A',
                'start_time' => '07:30:00',
                'end_time' => '15:00:00',
                'tolerance_late_minutes' => 5,
            ],
            [
                'name' => 'XII B',
                'start_time' => '07:30:00',
                'end_time' => '15:00:00',
                'tolerance_late_minutes' => 5,
            ],
        ];

        foreach ($classRooms as $classRoom) {
            ClassRoom::create($classRoom);
        }
    }
}
