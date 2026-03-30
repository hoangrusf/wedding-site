<?php
// Xóa toàn bộ RSVP + lời chúc
// Chạy: php scripts/delete_rsvp.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Rsvp;

$count = Rsvp::count();
Rsvp::truncate();

echo "✅ Đã xóa {$count} bản ghi RSVP và lời chúc!\n";
