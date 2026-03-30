<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rsvps', function (Blueprint $table) {
            $table->id();

            // Khóa ngoại — nullable cho khách vãng lai
            $table->foreignId('guest_id')
                  ->nullable()
                  ->constrained('guests')
                  ->nullOnDelete();

            // Thông tin xác nhận
            $table->string('guest_name');
            $table->string('phone_number')->nullable();
            $table->boolean('is_attending')->default(true);
            $table->unsignedSmallInteger('companion_count')->default(0);
            $table->text('wishes_message')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rsvps');
    }
};
