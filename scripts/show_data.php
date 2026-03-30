<?php
// Xem toàn bộ dữ liệu hiện tại trong DB
// Chạy: php scripts/show_data.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Guest;
use App\Models\Rsvp;
use App\Models\WeddingConfig;

echo "=== WEDDING CONFIG ===\n";
print_r(WeddingConfig::first()->toArray());

echo "\n=== GUESTS (" . Guest::count() . ") ===\n";
print_r(Guest::all()->toArray());

echo "\n=== RSVP (" . Rsvp::count() . ") ===\n";
print_r(Rsvp::all()->toArray());
