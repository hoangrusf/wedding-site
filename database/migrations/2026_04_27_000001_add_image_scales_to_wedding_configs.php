<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wedding_configs', function (Blueprint $table) {
            $table->decimal('hero_image_scale', 3, 2)->default(1.00)->after('hero_image_position');
            $table->decimal('groom_image_scale', 3, 2)->default(1.00)->after('groom_image_position');
            $table->decimal('bride_image_scale', 3, 2)->default(1.00)->after('bride_image_position');
        });
    }

    public function down(): void
    {
        Schema::table('wedding_configs', function (Blueprint $table) {
            $table->dropColumn(['hero_image_scale', 'groom_image_scale', 'bride_image_scale']);
        });
    }
};
