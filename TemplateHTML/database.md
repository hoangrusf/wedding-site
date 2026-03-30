# CƠ SỞ DỮ LIỆU — THIỆP CƯỚI ONLINE (Laravel Backend)

> **Database Engine:** MySQL 8+ hoặc PostgreSQL 15+
> **Framework:** Laravel 11.x · PHP 8.2+

---

## 1. Sơ đồ quan hệ (ERD)

```
┌─────────────────────┐
│   wedding_configs    │
├─────────────────────┤
│ id  (PK)            │
│ groom_name           │
│ bride_name           │
│ wedding_date         │
│ groom_parents        │
│ bride_parents        │
│ event_location       │
│ event_address        │
│ map_iframe_url       │
│ bank_account_info    │
│ hero_image_url       │
│ background_music_url │
│ created_at           │
│ updated_at           │
└─────────────────────┘

┌──────────────┐          ┌──────────────────┐
│    guests    │          │      rsvps       │
├──────────────┤    1:N   ├──────────────────┤
│ id  (PK)     │─────────▶│ id  (PK)         │
│ guest_code   │          │ guest_id  (FK)   │
│ display_name │          │ guest_name       │
│ guest_type   │          │ phone_number     │
│ created_at   │          │ is_attending     │
│ updated_at   │          │ companion_count  │
│              │          │ wishes_message   │
│              │          │ created_at       │
│              │          │ updated_at       │
└──────────────┘          └──────────────────┘
```

---

## 2. Laravel Migrations

### 2.1. `create_wedding_configs_table`

```bash
php artisan make:migration create_wedding_configs_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wedding_configs', function (Blueprint $table) {
            $table->id();

            // Thông tin cô dâu chú rể
            $table->string('groom_name');
            $table->string('bride_name');
            $table->dateTime('wedding_date');

            // Thông tin gia đình
            $table->string('groom_parents');
            $table->string('bride_parents');

            // Địa điểm tổ chức
            $table->string('event_location');
            $table->string('event_address');
            $table->text('map_iframe_url')->nullable();

            // Tài khoản mừng cưới
            $table->text('bank_account_info')->nullable();

            // Media
            $table->string('hero_image_url')->nullable();
            $table->string('background_music_url')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wedding_configs');
    }
};
```

### 2.2. `create_guests_table`

```bash
php artisan make:migration create_guests_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();

            // Mã khách dùng trên URL: ?invite=hoang
            $table->string('guest_code')->unique();

            // Tên hiển thị trên thiệp: "Hoàng", "Gia Đình Anh Tuấn"
            $table->string('display_name');

            // Phân loại: 1 = Nhà trai, 2 = Nhà gái, 3 = Bạn chung
            $table->unsignedTinyInteger('guest_type')->default(1);

            $table->timestamps();

            // Index tìm kiếm nhanh theo guest_code
            $table->index('guest_code');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
```

### 2.3. `create_rsvps_table`

```bash
php artisan make:migration create_rsvps_table
```

```php
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rsvps', function (Blueprint $table) {
            $table->id();

            // Khóa ngoại — nullable cho khách vãng lai
            $table->foreignId('guest_id')
                  ->nullable()
                  ->constrained('guests')
                  ->nullOnDelete();

            // Thông tin xác nhận
            $table->string('guest_name');
            $table->string('phone_number')->nullable();
            $table->boolean('is_attending')->default(true);
            $table->unsignedSmallInteger('companion_count')->default(0);
            $table->text('wishes_message')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rsvps');
    }
};
```

---

## 3. Eloquent Models

### 3.1. `app/Models/WeddingConfig.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WeddingConfig extends Model
{
    protected $fillable = [
        'groom_name',
        'bride_name',
        'wedding_date',
        'groom_parents',
        'bride_parents',
        'event_location',
        'event_address',
        'map_iframe_url',
        'bank_account_info',
        'hero_image_url',
        'background_music_url',
    ];

    protected function casts(): array
    {
        return [
            'wedding_date' => 'datetime',
        ];
    }
}
```

### 3.2. `app/Models/Guest.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Guest extends Model
{
    protected $fillable = [
        'guest_code',
        'display_name',
        'guest_type',
    ];

    protected function casts(): array
    {
        return [
            'guest_type' => 'integer',
        ];
    }

    /**
     * Một khách mời có thể gửi nhiều lần xác nhận (hoặc cập nhật).
     */
    public function rsvps(): HasMany
    {
        return $this->hasMany(Rsvp::class);
    }

    /**
     * Lấy RSVP mới nhất của khách.
     */
    public function latestRsvp(): HasMany
    {
        return $this->hasMany(Rsvp::class)->latest()->limit(1);
    }
}
```

### 3.3. `app/Models/Rsvp.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Rsvp extends Model
{
    protected $fillable = [
        'guest_id',
        'guest_name',
        'phone_number',
        'is_attending',
        'companion_count',
        'wishes_message',
    ];

    protected function casts(): array
    {
        return [
            'is_attending'    => 'boolean',
            'companion_count' => 'integer',
        ];
    }

    /**
     * RSVP thuộc về một khách mời (nullable).
     */
    public function guest(): BelongsTo
    {
        return $this->belongsTo(Guest::class);
    }
}
```

---

## 4. Controller Logic — `RsvpController`

```php
<?php

namespace App\Http\Controllers;

use App\Models\Guest;
use App\Models\Rsvp;
use App\Models\WeddingConfig;
use Illuminate\Http\Request;

class RsvpController extends Controller
{
    /**
     * Hiển thị trang thiệp cưới.
     *
     * URL: GET /?invite=hoang&type=1
     */
    public function show(Request $request)
    {
        // Lấy cấu hình thiệp (chỉ có 1 bản ghi)
        $config = WeddingConfig::firstOrFail();

        // Đọc tham số ?invite= từ URL
        $inviteCode = $request->query('invite');
        $type       = $request->query('type', 1); // Mặc định type = 1

        $guest = null;
        $displayName = 'Quý Khách'; // Fallback

        if ($inviteCode) {
            $guest = Guest::where('guest_code', $inviteCode)->first();

            if ($guest) {
                $displayName = $guest->display_name;

                // Nếu không truyền type, suy luận từ guest_type
                if (!$request->has('type')) {
                    $type = $guest->guest_type;
                }
            }
        }

        return view('wedding', [
            'config'      => $config,
            'guest'       => $guest,
            'displayName' => $displayName,
            'type'        => (int) $type,
        ]);
    }

    /**
     * Xử lý form xác nhận tham dự.
     *
     * URL: POST /rsvp
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'guest_id'        => ['nullable', 'exists:guests,id'],
            'guest_name'      => ['required', 'string', 'max:255'],
            'phone_number'    => ['nullable', 'string', 'max:20'],
            'is_attending'    => ['required', 'boolean'],
            'companion_count' => ['nullable', 'integer', 'min:0', 'max:10'],
            'wishes_message'  => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['companion_count'] = $validated['companion_count'] ?? 0;

        $rsvp = Rsvp::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'Cảm ơn bạn đã xác nhận tham dự!',
            'data'    => $rsvp,
        ], 201);
    }
}
```

---

## 5. Routes

```php
// routes/web.php

use App\Http\Controllers\RsvpController;

Route::get('/', [RsvpController::class, 'show'])->name('wedding.show');
Route::post('/rsvp', [RsvpController::class, 'store'])->name('rsvp.store');
```

---

## 6. Seeder mẫu

```php
<?php
// database/seeders/WeddingSeeder.php

namespace Database\Seeders;

use App\Models\Guest;
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
            'wedding_date'         => '2026-08-15 10:00:00',
            'groom_parents'        => 'Ông Nguyễn Văn A & Bà Trần Thị B',
            'bride_parents'        => 'Ông Lê Văn C & Bà Phạm Thị D',
            'event_location'       => 'Trung tâm Hội nghị Tiệc cưới ABC Palace',
            'event_address'        => '123 Đường Lê Lợi, Quận 1, TP.HCM',
            'map_iframe_url'       => null,
            'bank_account_info'    => 'Nguyễn Minh Anh - Vietcombank - 0123456789',
            'hero_image_url'       => null,
            'background_music_url' => null,
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
    }
}
```

---

## 7. Tổng kết cấu trúc thư mục

```
app/
├── Http/Controllers/
│   └── RsvpController.php
└── Models/
    ├── Guest.php
    ├── Rsvp.php
    └── WeddingConfig.php

database/
├── migrations/
│   ├── xxxx_xx_xx_000001_create_wedding_configs_table.php
│   ├── xxxx_xx_xx_000002_create_guests_table.php
│   └── xxxx_xx_xx_000003_create_rsvps_table.php
└── seeders/
    └── WeddingSeeder.php

routes/
└── web.php
```

### Lệnh khởi tạo nhanh

```bash
# Chạy migration
php artisan migrate

# Seed dữ liệu mẫu
php artisan db:seed --class=WeddingSeeder

# Truy cập
# http://localhost:8000/?invite=hoang&type=1
# http://localhost:8000/?invite=loan&type=2
```
