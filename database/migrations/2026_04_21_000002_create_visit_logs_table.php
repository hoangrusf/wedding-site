<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('visit_logs', function (Blueprint $table) {
            $table->id();
            // Tên từ query ?invite=...
            $table->string('invite_name')->nullable();
            // 1 = Nhà trai, 2 = Nhà gái
            $table->unsignedTinyInteger('type')->default(1);
            $table->string('ip_address', 45)->nullable();
            $table->string('user_agent')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('visit_logs');
    }
};
