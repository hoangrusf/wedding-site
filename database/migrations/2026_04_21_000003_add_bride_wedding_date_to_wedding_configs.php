<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('wedding_configs', function (Blueprint $table) {
            // Ngày giờ riêng cho nhà gái (type=2); nếu null thì dùng wedding_date chung
            $table->dateTime('bride_wedding_date')->nullable()->after('wedding_date');
        });
    }

    public function down(): void
    {
        Schema::table('wedding_configs', function (Blueprint $table) {
            $table->dropColumn('bride_wedding_date');
        });
    }
};
