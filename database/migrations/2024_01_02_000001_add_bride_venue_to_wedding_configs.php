<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wedding_configs', function (Blueprint $table) {
            // Đổi tên các cột cũ thành tiền tố groom_
            $table->renameColumn('event_location', 'groom_event_location');
            $table->renameColumn('event_address',  'groom_event_address');
            $table->renameColumn('map_iframe_url', 'groom_map_iframe_url');

            // Thêm cột địa điểm nhà gái
            $table->string('bride_event_location')->nullable()->after('groom_map_iframe_url');
            $table->string('bride_event_address')->nullable()->after('bride_event_location');
            $table->text('bride_map_iframe_url')->nullable()->after('bride_event_address');
        });
    }

    public function down(): void
    {
        Schema::table('wedding_configs', function (Blueprint $table) {
            $table->dropColumn(['bride_event_location', 'bride_event_address', 'bride_map_iframe_url']);
            $table->renameColumn('groom_event_location', 'event_location');
            $table->renameColumn('groom_event_address',  'event_address');
            $table->renameColumn('groom_map_iframe_url', 'map_iframe_url');
        });
    }
};
