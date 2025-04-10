<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('booking_schedule', function (Blueprint $table) {
            $table->char('id', 36)->primary(); // Dùng char cho ID (UUID)
            $table->char('user_id', 36); // Khóa ngoại tới bảng users (UUID)
            $table->char('field_id', 36); // Khóa ngoại tới bảng fields (UUID)
            $table->dateTime('date_start'); // Thời gian bắt đầu
            $table->dateTime('date_end');   // Thời gian kết thúc
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('booking_schedule');
    }
};
