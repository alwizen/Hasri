<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Teacher;
use App\Models\Departement;

class TeacherSeeder extends Seeder
{
    public function run(): void
    {
        $firstNames = [
            'Ahmad',
            'Budi',
            'Citra',
            'Dewi',
            'Eko',
            'Fajar',
            'Gita',
            'Hendra',
            'Indah',
            'Joko',
            'Lina',
            'Maya',
            'Nugroho',
            'Putri',
            'Rizki',
            'Sari',
            'Taufik',
            'Wulan',
            'Yusuf',
            'Zahra'
        ];

        $lastNames = [
            'Saputra',
            'Pratama',
            'Wijaya',
            'Santoso',
            'Lestari',
            'Kurniawan',
            'Hidayat',
            'Maulana',
            'Firmansyah',
            'Ramadhan'
        ];

        $departements = Departement::all();

        if ($departements->isEmpty()) {
            $this->command->warn('Tidak ada departement. Jalankan DepartementSeeder dulu.');
            return;
        }

        $nipCounter = 198001010001;
        $rfidCounter = 100000001;

        foreach ($departements as $dept) {

            // jumlah guru per departement
            $totalTeachers = match ($dept->name) {
                'Kepala Sekolah' => 1,
                'Tata Usaha' => 5,
                default => 10, // Guru
            };

            for ($i = 1; $i <= $totalTeachers; $i++) {

                $fullName = $firstNames[array_rand($firstNames)] . ' ' .
                    $lastNames[array_rand($lastNames)];

                Teacher::create([
                    'nip' => (string) $nipCounter++,
                    'name' => $fullName,
                    'rfid_uid' => (string) $rfidCounter++,
                    'departement_id' => $dept->id,
                    'telp' => '08' . rand(1111111111, 9999999999),
                    'address' => 'Jl. Guru No. ' . rand(1, 200),
                    'photo' => null, // nanti bisa isi path foto
                ]);
            }
        }

        $this->command->info('TeacherSeeder berhasil membuat data guru.');
    }
}
