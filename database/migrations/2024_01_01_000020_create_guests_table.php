<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();

            // Mã khách dùng trên URL: ?invite=hoang
            $table->string('guest_code')->unique();

            // Tên hiển thị trên thiệp: "Hoàng", "Gia Đình Anh Tuấn"
            $table->string('display_name');

            // Phân loại: 1 = Nhà trai, 2 = Nhà gái, 3 = Bạn chung
            $table->unsignedTinyInteger('guest_type')->default(1);

            $table->timestamps();

            $table->index('guest_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
