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
        Schema::create('field_time_slot', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('field_id');
            $table->uuid('time_slot_id');

            $table->enum('status', ['active', 'inactive'])->default('active');
            $table->double('custom_price', 10, 2); // Giá tiền thực tế của khung giờ này

            $table->timestamps();

            $table->unique(['field_id', 'time_slot_id']);

        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('field_time_slot');
    }
};
