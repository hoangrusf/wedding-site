<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wedding_configs', function (Blueprint $table) {
            $table->id();

            // Thông tin cô dâu chú rể
            $table->string('groom_name');
            $table->string('bride_name');
            $table->dateTime('wedding_date');

            // Thông tin gia đình
            $table->string('groom_parents');
            $table->string('bride_parents');

            // Địa điểm tổ chức
            $table->string('event_location');
            $table->string('event_address');
            $table->text('map_iframe_url')->nullable();

            // Tài khoản mừng cưới
            $table->text('bank_account_info')->nullable();

            // Media
            $table->string('hero_image_url')->nullable();
            $table->string('background_music_url')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wedding_configs');
    }
};
