<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('gallery_photos', function (Blueprint $table) {
            $table->string('object_fit')->default('cover')->after('layout');       // cover, contain, fill
            $table->string('object_position')->default('center center')->after('object_fit'); // CSS object-position
        });
    }

    public function down(): void
    {
        Schema::table('gallery_photos', function (Blueprint $table) {
            $table->dropColumn(['object_fit', 'object_position']);
        });
    }
};
