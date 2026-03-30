<?php

namespace Database\Seeders;

use App\Models\Guest;
use App\Models\Rsvp;
use App\Models\WeddingConfig;
use Illuminate\Database\Seeder;

class WeddingSeeder extends Seeder
{
    public function run(): void
    {
        // Cấu hình thiệp
        WeddingConfig::create([
            'groom_name'           => 'Minh Anh',
            'bride_name'           => 'Thuỳ Linh',
            'wedding_date'         => '2026-08-15 17:00:00',
            'groom_parents'        => 'Ông Nguyễn Văn A & Bà Trần Thị B',
            'bride_parents'        => 'Ông Lê Văn C & Bà Phạm Thị D',
            'event_location'       => 'Trung Tâm Tiệc Cưới White Palace',
            'event_address'        => '194 Hoàng Văn Thụ, Phường 9, Quận Phú Nhuận, TP.HCM',
            'map_iframe_url'       => 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3919.0654572684!2d106.6686847!3d10.8014597!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3175292ded6f2827%3A0xc11b08c4d4e1d12!2sWhite%20Palace!5e0!3m2!1svi!2svn!4v1700000000000!5m2!1svi!2svn',
            'bank_account_info'    => json_encode([
                'groom' => [
                    'bank_name'    => 'Vietcombank',
                    'account_no'   => '1234567890123',
                    'account_name' => 'NGUYEN MINH ANH',
                ],
                'bride' => [
                    'bank_name'    => 'MB Bank',
                    'account_no'   => '9876543210123',
                    'account_name' => 'LE THUY LINH',
                ],
            ]),
            'hero_image_url'       => 'https://images.unsplash.com/photo-1519741497674-611481863552?w=1400&q=80',
            'background_music_url' => 'https://www.soundhelix.com/examples/mp3/SoundHelix-Song-1.mp3',
        ]);

        // Khách mời mẫu
        $guests = [
            ['guest_code' => 'hoang',              'display_name' => 'Hoàng',               'guest_type' => 1],
            ['guest_code' => 'gia-dinh-anh-tuan',  'display_name' => 'Gia Đình Anh Tuấn',   'guest_type' => 1],
            ['guest_code' => 'loan',               'display_name' => 'Loan',                'guest_type' => 2],
            ['guest_code' => 'chi-mai',            'display_name' => 'Chị Mai',             'guest_type' => 2],
            ['guest_code' => 'nhom-ban-than',      'display_name' => 'Nhóm Bạn Thân',       'guest_type' => 3],
        ];

        foreach ($guests as $guest) {
            Guest::create($guest);
        }

        // RSVP mẫu (lời chúc)
        Rsvp::create([
            'guest_name'      => 'Ngọc Trinh',
            'phone_number'    => '0901234567',
            'is_attending'    => true,
            'companion_count' => 1,
            'wishes_message'  => 'Chúc hai bạn trăm năm hạnh phúc, sớm có thiên thần nhỏ! 🎉',
        ]);

        Rsvp::create([
            'guest_name'      => 'Hoàng Nam',
            'phone_number'    => '0912345678',
            'is_attending'    => true,
            'companion_count' => 0,
            'wishes_message'  => 'Hạnh phúc mãi bên nhau nha! Yêu thương hai bạn nhiều lắm 💕',
        ]);
    }
}
