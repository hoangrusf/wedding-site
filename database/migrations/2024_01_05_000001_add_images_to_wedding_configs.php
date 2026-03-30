<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wedding_configs', function (Blueprint $table) {
            $table->string('groom_image_url')->nullable()->after('hero_image_url');
            $table->string('bride_image_url')->nullable()->after('groom_image_url');
        });
    }

    public function down(): void
    {
        Schema::table('wedding_configs', function (Blueprint $table) {
            $table->dropColumn(['groom_image_url', 'bride_image_url']);
        });
    }
};
