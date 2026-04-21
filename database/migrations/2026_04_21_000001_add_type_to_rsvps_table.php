<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rsvps', function (Blueprint $table) {
            // 1 = Nhà trai, 2 = Nhà gái
            $table->unsignedTinyInteger('type')->default(1)->after('guest_id');
        });
    }

    public function down(): void
    {
        Schema::table('rsvps', function (Blueprint $table) {
            $table->dropColumn('type');
        });
    }
};
