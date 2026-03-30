<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wedding_configs', function (Blueprint $table) {
            $table->string('groom_map_url', 2000)->nullable()->after('groom_map_iframe_url');
            $table->string('bride_map_url', 2000)->nullable()->after('bride_map_iframe_url');
        });
    }

    public function down(): void
    {
        Schema::table('wedding_configs', function (Blueprint $table) {
            $table->dropColumn(['groom_map_url', 'bride_map_url']);
        });
    }
};
