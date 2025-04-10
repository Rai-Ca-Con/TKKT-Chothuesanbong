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
        Schema::create('comments', function (Blueprint $table) {
            $table->char('id', 36)->primary(); // UUID làm khóa chính
            $table->char('user_id', 36); // Khóa ngoại tới bảng users (UUID)
            $table->char('field_id', 36); // Khóa ngoại tới bảng fields (UUID)
            $table->uuid('parent_id')->nullable();
            $table->text('content'); // Nội dung comment
            $table->text('image_url')->nullable();;         // Đường dẫn hình ảnh
            $table->integer('status'); // Trạng thái comment
            $table->timestamps(); // Created_at và updated_at
            $table->softDeletes(); // Trường để xoá mềm (soft delete)
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('comments');
    }
};
