<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Student;
use App\Models\ClassRoom;
use Illuminate\Support\Str;

class StudentSeeder extends Seeder
{
    public function run(): void
    {
        $firstNames = [
            'Ahmad',
            'Budi',
            'Citra',
            'Dewi',
            'Eka',
            'Fajar',
            'Gita',
            'Hendra',
            'Indah',
            'Joko',
            'Kiki',
            'Lina',
            'Maya',
            'Nanda',
            'Putra',
            'Rina',
            'Sari',
            'Taufik',
            'Wulan',
            'Yusuf'
        ];

        $lastNames = [
            'Saputra',
            'Pratama',
            'Wijaya',
            'Santoso',
            'Lestari',
            'Permata',
            'Ramadhan',
            'Maulana',
            'Hidayat',
            'Kurniawan'
        ];

        $classRooms = ClassRoom::all();

        if ($classRooms->isEmpty()) {
            $this->command->warn('Tidak ada classroom. Jalankan ClassRoomSeeder dulu.');
            return;
        }

        $nisCounter = 2024001;

        foreach ($classRooms as $classRoom) {
            // jumlah siswa per kelas
            for ($i = 1; $i <= 15; $i++) {

                $fullName = $firstNames[array_rand($firstNames)] . ' ' .
                    $lastNames[array_rand($lastNames)];

                Student::create([
                    'nis' => (string) $nisCounter++,
                    'full_name' => $fullName,
                    'class_room_id' => $classRoom->id,
                    'address' => 'Jl. Pendidikan No. ' . rand(1, 200),
                    'phone' => '08' . rand(1111111111, 9999999999),
                    'status' => 'active',
                ]);
            }
        }

        $this->command->info('StudentSeeder berhasil membuat data siswa.');
    }
}
