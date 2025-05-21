<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FieldTimeSlotSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $fields = DB::table('fields')->get(); // Lấy tất cả sân
        $timeSlots = DB::table('time_slots')->get(); // Lấy toàn bộ khung giờ
        $now = Carbon::now();

        $data = [];

        foreach ($fields as $field) {
            foreach ($timeSlots as $timeSlot) {
                // Lấy giờ bắt đầu (dưới dạng số nguyên)
                $hour = (int) Carbon::parse($timeSlot->start_time)->format('H');

                // Xác định hệ số theo khoảng thời gian
                if ($hour >= 6 && $hour < 12) {
                    $coefficient = 1.0;
                } elseif ($hour >= 12 && $hour < 18) {
                    $coefficient = 1.2;
                } elseif ($hour >= 18 && $hour < 24) {
                    $coefficient = 1.5;
                } else {
                    // Nếu nằm ngoài 6h - 24h thì không cho active hoặc bỏ qua
                    continue;
                }

                $customPrice = $field->price * $coefficient;

                $data[] = [
                    'id'             => (string) Str::uuid(),
                    'field_id'       => $field->id,
                    'time_slot_id'   => $timeSlot->id,
                    'status'         => 'active',
                    'custom_price'   => $customPrice,
                    'created_at'     => $now,
                    'updated_at'     => $now,
                ];
            }
        }

        foreach (array_chunk($data, 500) as $chunk) {
            DB::table('field_time_slot')->insert($chunk);
        }
    }
}
