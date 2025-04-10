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
        Schema::create('fields', function (Blueprint $table) {
            // Tạo UUID làm khóa chính
            $table->uuid('id')->primary();

            // Các trường thông tin khác
            $table->string('name');
            $table->decimal('longitude', 20, 15)->nullable();
            $table->decimal('latitude', 20, 15)->nullable();
            $table->string('address');
            $table->uuid('category_id');
            $table->uuid('state_id');
            $table->double('price', 10, 2);
            $table->text('description')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('fields');
    }
};
