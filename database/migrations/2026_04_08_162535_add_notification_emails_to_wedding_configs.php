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
        Schema::table('wedding_configs', function (Blueprint $table) {
            $table->string('groom_notification_email')->nullable()->after('groom_map_iframe_url');
            $table->string('bride_notification_email')->nullable()->after('bride_map_iframe_url');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('wedding_configs', function (Blueprint $table) {
            $table->dropColumn(['groom_notification_email', 'bride_notification_email']);
        });
    }
};
