<?php
// Thêm / sửa khách mời
// Chạy: php scripts/manage_guests.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Guest;

// ---- THÊM KHÁCH MỜI ----
// guest_type: 1 = Nhà trai, 2 = Nhà gái, 3 = Bạn chung
$newGuests = [
    // ['guest_code' => 'tuan', 'display_name' => 'Anh Tuấn', 'guest_type' => 1],
    // ['guest_code' => 'mai',  'display_name' => 'Chị Mai',  'guest_type' => 2],
];

foreach ($newGuests as $g) {
    Guest::updateOrCreate(['guest_code' => $g['guest_code']], $g);
    echo "✅ Đã thêm/cập nhật khách: {$g['display_name']}\n";
}

// ---- XÓA KHÁCH MỜI ----
// Guest::where('guest_code', 'ten-khach')->delete();

// ---- XEM DANH SÁCH ----
echo "\n=== DANH SÁCH KHÁCH MỜI ===\n";
Guest::all()->each(function ($g) {
    $type = ['', 'Nhà trai', 'Nhà gái', 'Bạn chung'][$g->guest_type] ?? '?';
    echo "  [{$g->guest_code}] {$g->display_name} ({$type}) → /?invite={$g->guest_code}\n";
});
