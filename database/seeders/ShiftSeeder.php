<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shift;

class ShiftSeeder extends Seeder
{
    public function run(): void
    {
        Shift::create([
            'name' => 'Shift Pagi',
            'start_time' => '08:00:00',
            'end_time' => '16:00:00',
        ]);

        Shift::create([
            'name' => 'Shift Sore',
            'start_time' => '16:00:00',
            'end_time' => '00:00:00',
        ]);
    }
}
