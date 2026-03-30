<?php
// Cập nhật cấu hình thiệp cưới
// Chạy: php scripts/update_config.php

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\WeddingConfig;

WeddingConfig::first()->update([
    'groom_name'           => 'Công Hoàng',
    'bride_name'           => 'Chu Loan',
    'wedding_date'         => '2026-05-24 11:00:00',
    'groom_parents'        => 'Ông Nguyễn Công Dân & Bà Nguễn Thị Viện',
    'bride_parents'        => 'Ông Chu Văn Cao & Bà Lê Thị Phượng',

    // Địa điểm nhà trai (type=1)
    'groom_event_location' => 'Tiệc thành hôn tại gia',
    'groom_event_address'  => 'Cổng làng Long Đằng, Phường Phùng Chí Kiên, Huyện Mỹ Hào, Tỉnh Hưng Yên (chưa sát nhập)',
    'groom_map_iframe_url' => 'https://www.google.com/maps/embed?pb=!1m26!1m12!1m3!1d348.9831632429855!2d106.09564722834897!3d20.92466232973072!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!4m11!3e0!4m3!3m2!1d20.9248777!2d106.09573259999999!4m5!1s0x3135a30070faec5f%3A0x7882c4a971df9375!2zQ-G7lW5nIGzDoG5nIExvbmcgxJDhurFuZywgVzNGVytSNVIsIExvbmcgxJDhurFuZywgxJDGsOG7nW5nIEjDoG8sIEjGsG5nIFnDqm4sIFZp4buHdCBOYW0!3m2!1d20.924601799999998!2d106.0953868!5e0!3m2!1svi!2s!4v1774854778528!5m2!1svi!2s',

    // Địa điểm nhà gái (type=2) — để null nếu dùng chung 1 địa điểm
    'bride_event_location' => 'Tiệc vu quy tại gia',
    'bride_event_address'  => '123 Đường Lê Lợi, Quận 1, TP.HCM',
    'bride_map_iframe_url' => 'https://www.google.com/maps/embed?pb=...',

    'hero_image_url'       => 'https://link-anh-cua-ban.jpg',
    'background_music_url' => 'https://link-nhac-cua-ban.mp3',
    'bank_account_info'    => json_encode([
        'groom' => [
            'bank_name'    => 'TPBank',
            'account_no'   => 'hoanghiutehy',
            'account_name' => 'Nguyen Cong Hoang',
        ],
        'bride' => [
            'bank_name'    => 'Techcombank',
            'account_no'   => '9876543210',
            'account_name' => 'Chu Thi Thanh Loan',
        ],
    ]),
]);

echo "✅ Cập nhật cấu hình thành công!\n";
