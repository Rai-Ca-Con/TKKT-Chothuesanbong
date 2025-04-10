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
        Schema::create('receipts', function (Blueprint $table) {
            $table->char('id', 36)->primary(); // UUID receipt
            $table->char('user_id', 36); // UUID người dùng
            $table->char('booking_id', 36); // UUID đặt sân
            $table->dateTime('date'); // Ngày tạo hóa đơn
            $table->double('total_price', 10, 2);
            $table->enum('status', ['pending', 'paid', 'cancelled', 'expired'])->default('pending'); // Trạng thái hóa đơn
            $table->text('payment_url')->nullable();
            $table->dateTime('expired_at')->nullable();
            $table->timestamps(); // created_at & updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('receipts');
    }
};
