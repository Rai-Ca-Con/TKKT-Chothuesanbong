<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $startHour = 6;
        $endHour = 24;

        for ($hour = $startHour; $hour < $endHour; $hour += 2) {
            $start = str_pad($hour, 2, '0', STR_PAD_LEFT) . ':00:00';
            $end = str_pad($hour + 2, 2, '0', STR_PAD_LEFT) . ':00:00';

            DB::table('time_slots')->insert([
                'id' => (string) Str::uuid(),
                'start_time' => $start,
                'end_time' => $end,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
