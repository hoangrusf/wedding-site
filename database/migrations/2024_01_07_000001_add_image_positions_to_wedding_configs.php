<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wedding_configs', function (Blueprint $table) {
            $table->string('hero_image_position', 50)->default('center center')->after('hero_image_url');
            $table->string('groom_image_position', 50)->default('center center')->after('groom_image_url');
            $table->string('bride_image_position', 50)->default('center center')->after('bride_image_url');
        });
    }

    public function down(): void
    {
        Schema::table('wedding_configs', function (Blueprint $table) {
            $table->dropColumn(['hero_image_position', 'groom_image_position', 'bride_image_position']);
        });
    }
};
