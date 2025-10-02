<?php

namespace Database\Seeders;

use App\Models\Holiday;
use Illuminate\Database\Seeder;

class HolidaySeeder extends Seeder
{
    public function run(): void
    {
        $defaults = [
            // Contoh libur nasional (sesuaikan tiap tahun sesuai kebutuhan)
            ['date' => '2025-01-01', 'name' => 'Tahun Baru Masehi'],
            ['date' => '2025-03-31', 'name' => 'Nyepi'],
            ['date' => '2025-04-18', 'name' => 'Wafat Isa Almasih'],
            ['date' => '2025-05-01', 'name' => 'Hari Buruh'],
            ['date' => '2025-05-29', 'name' => 'Kenaikan Isa Almasih'],
            ['date' => '2025-06-01', 'name' => 'Hari Lahir Pancasila'],
            ['date' => '2025-08-17', 'name' => 'Hari Kemerdekaan RI'],
        ];

        foreach ($defaults as $h) {
            Holiday::firstOrCreate(['date' => $h['date']], ['name' => $h['name']]);
        }
    }
}

