<?php

namespace Database\Seeders;

use App\Models\Calendar;
use Illuminate\Database\Seeder;

class CalendarSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Calendar::insert([
            [
                'name' => '24 Hours',
            ],
            [
                'name' => 'RGB Shifts',
            ],
        ]);
    }
}
