<?php

namespace Database\Seeders;

use App\Models\WRTemplate;
use Illuminate\Database\Seeder;

class WRTemplateSeeder extends Seeder
{
    public function run(): void
    {
        WRTemplate::firstOrCreate(
            ['name' => 'default'],
            ['settings' => [
                'row_height_weekday' => 60,
                'row_height_weekend' => 20,
                'columns' => [
                    'day' => 'B', 'time' => 'D', 'detail' => 'E', 'output' => 'G'
                ],
            ]]
        );
    }
}

