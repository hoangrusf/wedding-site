<?php

namespace App\Http\Controllers;

use App\Models\GalleryPhoto;
use App\Models\Guest;
use App\Models\Rsvp;
use App\Models\VisitLog;
use App\Models\WeddingConfig;
use App\Services\GoogleSheetsService;
use Illuminate\Http\Request;

class RsvpController extends Controller
{
    /**
     * Hiển thị trang thiệp cưới.
     * URL: GET /?invite=hoang&type=1
     */
    public function show(Request $request)
    {
        $config = WeddingConfig::firstOrFail();

        $inviteCode  = $request->query('invite');
        $type        = $request->query('type', 1);
        $guest       = null;
        $displayName = $inviteCode ?: 'Quý Khách';

        // Chọn địa điểm theo type: 1 = nhà trai, 2 = nhà gái
        if ($type == 2 && $config->bride_event_location) {
            $venue = [
                'location'    => $config->bride_event_location,
                'address'     => $config->bride_event_address,
                'map_url'     => $config->bride_map_url,
                'map_iframe'  => $config->bride_map_iframe_url,
            ];
        } else {
            $venue = [
                'location'    => $config->groom_event_location,
                'address'     => $config->groom_event_address,
                'map_url'     => $config->groom_map_url,
                'map_iframe'  => $config->groom_map_iframe_url,
            ];
        }

        // Ghi nhận lượt truy cập
        VisitLog::create([
            'invite_name' => $inviteCode ?: null,
            'type'        => (int) $type,
            'ip_address'  => $request->ip(),
            'user_agent'  => $request->userAgent(),
        ]);

        // Lấy lời chúc đã có (lọc theo type)
        $wishes = Rsvp::whereNotNull('wishes_message')
            ->where('wishes_message', '!=', '')
            ->where('type', (int) $type)
            ->latest()
            ->get();

        $bankInfo = json_decode($config->bank_account_info, true) ?? [];

        $galleryPhotos = GalleryPhoto::orderBy('sort_order')->orderBy('id')->get();

        return view('wedding', [
            'config'      => $config,
            'guest'       => $guest,
            'displayName' => $displayName,
            'type'        => (int) $type,
            'wishes'      => $wishes,
            'bankInfo'    => $bankInfo,
            'venue'       => $venue,
            'galleryPhotos' => $galleryPhotos,
        ]);
    }

    /**
     * Xử lý form xác nhận tham dự.
     * URL: POST /rsvp
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'guest_id'        => ['nullable', 'exists:guests,id'],
            'type'            => ['nullable', 'integer', 'in:1,2'],
            'guest_name'      => ['required', 'string', 'max:255'],
            'phone_number'    => ['nullable', 'string', 'max:20'],
            'is_attending'    => ['required', 'boolean'],
            'companion_count' => ['nullable', 'integer', 'min:0', 'max:10'],
            'wishes_message'  => ['nullable', 'string', 'max:1000'],
        ]);

        $validated['companion_count'] = $validated['companion_count'] ?? 0;
        $validated['type'] = $validated['type'] ?? 1;

        $rsvp = Rsvp::create($validated);

        // Đồng bộ lên Google Sheets (không blocking — lỗi chỉ ghi log)
        try {
            (new GoogleSheetsService())->appendRow([
                $rsvp->id,
                $rsvp->created_at->format('H:i d/m/Y'),
                $rsvp->guest_name,
                $rsvp->type == 2 ? 'Nhà Gái' : 'Nhà Trai',
                $rsvp->phone_number ?? '',
                $rsvp->is_attending ? 'Tham dự' : 'Không tham dự',
                $rsvp->companion_count,
                $rsvp->wishes_message ?? '',
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('GoogleSheets sync failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Cảm ơn bạn đã xác nhận tham dự!',
            'data'    => $rsvp,
        ], 201);
    }

    /**
     * Lấy danh sách lời chúc (API cho AJAX load thêm).
     * Lọc theo type nếu có: ?type=1 hoặc ?type=2
     */
    public function wishes(Request $request)
    {
        $query = Rsvp::whereNotNull('wishes_message')
            ->where('wishes_message', '!=', '')
            ->latest();

        if ($request->has('type')) {
            $query->where('type', (int) $request->query('type'));
        }

        $wishes = $query->get(['guest_name', 'wishes_message', 'created_at']);

        return response()->json($wishes);
    }

    /**
     * Gửi lời chúc (không cần xác nhận tham dự).
     * POST /wishes
     */
    public function storeWish(Request $request)
    {
        $validated = $request->validate([
            'guest_id'       => ['nullable', 'exists:guests,id'],
            'type'           => ['nullable', 'integer', 'in:1,2'],
            'guest_name'     => ['required', 'string', 'max:255'],
            'wishes_message' => ['required', 'string', 'max:1000'],
        ]);

        $validated['type'] = $validated['type'] ?? 1;
        $validated['is_attending'] = false; // Chỉ gửi lời chúc, chưa xác nhận tham dự
        $validated['phone_number'] = null;
        $validated['companion_count'] = 0;

        $rsvp = Rsvp::create($validated);

        // Đồng bộ lên Google Sheets
        try {
            (new GoogleSheetsService())->appendRow([
                $rsvp->id,
                $rsvp->created_at->format('H:i d/m/Y'),
                $rsvp->guest_name,
                $rsvp->type == 2 ? 'Nhà Gái' : 'Nhà Trai',
                '',
                'Lời chúc',
                0,
                $rsvp->wishes_message ?? '',
            ]);
        } catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::warning('GoogleSheets sync failed: ' . $e->getMessage());
        }

        return response()->json([
            'success' => true,
            'message' => 'Cảm ơn lời chúc của bạn!',
            'data'    => $rsvp,
        ], 201);
    }

    /**
     * Trang admin - danh sách xác nhận tham dự.
     * URL: GET /admin/rsvp
     */
    public function adminRsvp(Request $request)
    {
        if (!$request->session()->get('admin_auth')) {
            return view('admin.login');
        }

        $rsvps = Rsvp::with('guest')
            ->latest()
            ->get();

        $totalAttending       = $rsvps->where('is_attending', true)->sum(fn($r) => 1 + $r->companion_count);
        $totalNotAttending    = $rsvps->where('is_attending', false)->count();
        $totalGroomAttending  = $rsvps->where('is_attending', true)->where('type', 1)->sum(fn($r) => 1 + $r->companion_count);
        $totalBrideAttending  = $rsvps->where('is_attending', true)->where('type', 2)->sum(fn($r) => 1 + $r->companion_count);

        return view('admin.rsvp', compact(
            'rsvps',
            'totalAttending',
            'totalNotAttending',
            'totalGroomAttending',
            'totalBrideAttending'
        ));
    }

    /**
     * Test kết nối Google Sheets.
     * URL: GET /admin/sheets-test
     */
    public function testGoogleSheets(Request $request)
    {
        if (!$request->session()->get('admin_auth')) {
            abort(403);
        }

        $spreadsheetId   = config('services.google_sheets.spreadsheet_id');
        $sheetName       = config('services.google_sheets.sheet_name');
        $credentialsPath = config('services.google_sheets.credentials_path');

        $checks = [
            'GOOGLE_SHEETS_SPREADSHEET_ID' => !empty($spreadsheetId) ? "✅ {$spreadsheetId}" : '❌ Chưa cấu hình',
            'GOOGLE_SHEETS_SHEET_NAME'     => !empty($sheetName) ? "✅ {$sheetName}" : '❌ Chưa cấu hình',
            'Credentials file'             => file_exists($credentialsPath) ? "✅ {$credentialsPath}" : "❌ Không tìm thấy: {$credentialsPath}",
        ];

        $appendResult = null;
        if (!empty($spreadsheetId) && file_exists($credentialsPath)) {
            try {
                $ok = (new GoogleSheetsService())->appendRow([
                    'TEST',
                    now()->format('H:i d/m/Y'),
                    'Test từ Admin',
                    'Test',
                    '',
                    'Kiểm tra kết nối',
                    0,
                    'Dòng này được tạo tự động để kiểm tra kết nối',
                ]);
                $appendResult = $ok ? '✅ Ghi thành công vào Google Sheets!' : '❌ Ghi thất bại (xem laravel.log)';
            } catch (\Throwable $e) {
                $appendResult = '❌ Lỗi: ' . $e->getMessage();
            }
        }

        $html = '<style>body{font-family:sans-serif;padding:2rem;background:#f4ede4;} h2{color:#5a3e2b;} table{border-collapse:collapse;width:100%;max-width:700px;background:#fff;border-radius:8px;overflow:hidden;box-shadow:0 2px 8px rgba(0,0,0,.08);} td,th{padding:.7rem 1rem;border-bottom:1px solid #f0e8df;font-size:.9rem;} th{background:#5a3e2b;color:#fff;text-align:left;} .result{margin-top:1.5rem;padding:1rem 1.5rem;border-radius:8px;background:#fff;font-size:1rem;} a{color:#b48c64;}</style>';
        $html .= '<h2>🔧 Kiểm tra Google Sheets</h2><table><tr><th>Mục</th><th>Trạng thái</th></tr>';
        foreach ($checks as $k => $v) {
            $html .= "<tr><td><b>{$k}</b></td><td>{$v}</td></tr>";
        }
        $html .= '</table>';
        if ($appendResult) {
            $html .= "<div class='result'><b>Kết quả ghi:</b> {$appendResult}</div>";
        }
        $html .= '<p style="margin-top:1.5rem"><a href="' . route('admin.rsvp') . '">← Quay lại Admin</a></p>';

        return response($html);
    }

    /**
     * Xử lý đăng nhập admin.
     * URL: POST /admin/login
     */
    public function adminLogin(Request $request)
    {
        $password = $request->input('password');

        if ($password === '0357516258') {
            $request->session()->put('admin_auth', true);
            return redirect()->route('admin.rsvp');
        }

        return back()->withErrors(['password' => 'Mật khẩu không đúng.']);
    }

    /**
     * Đăng xuất admin.
     * URL: POST /admin/logout
     */
    public function adminLogout(Request $request)
    {
        $request->session()->forget('admin_auth');
        return redirect()->route('admin.rsvp');
    }

    /**
     * Trang admin - danh sách truy cập thiệp.
     * URL: GET /admin/visits
     */
    public function adminVisits(Request $request)
    {
        if (!$request->session()->get('admin_auth')) {
            return view('admin.login');
        }

        $visits = VisitLog::latest()->get();

        return view('admin.visits', compact('visits'));
    }

    /**
     * Xóa một lượt xem.
     * URL: DELETE /admin/visits/{visit}
     */
    public function deleteVisit(Request $request, VisitLog $visit)
    {
        if (!$request->session()->get('admin_auth')) {
            abort(403);
        }
        $visit->delete();
        return back()->with('success', 'Đã xóa lượt xem.');
    }

    /**
     * Xóa toàn bộ lượt xem.
     * URL: DELETE /admin/visits
     */
    public function deleteAllVisits(Request $request)
    {
        if (!$request->session()->get('admin_auth')) {
            abort(403);
        }
        VisitLog::truncate();
        return back()->with('success', 'Đã xóa toàn bộ lượt xem.');
    }

    /**
     * Xóa lời chúc của một RSVP.
     * URL: DELETE /admin/rsvp/{rsvp}/wish
     */
    public function deleteWish(Request $request, Rsvp $rsvp)
    {
        if (!$request->session()->get('admin_auth')) {
            abort(403);
        }

        $rsvp->update(['wishes_message' => null]);

        return back()->with('success', 'Đã xóa lời chúc.');
    }

    /**
     * Trang cấu hình thiệp cưới.
     * URL: GET /admin/config
     */
    public function adminConfig(Request $request)
    {
        if (!$request->session()->get('admin_auth')) {
            return view('admin.login');
        }

        $config = WeddingConfig::firstOrFail();
        $bankInfo = json_decode($config->bank_account_info, true) ?? [];

        return view('admin.config', compact('config', 'bankInfo'));
    }

    /**
     * Lưu cấu hình thiệp cưới.
     * URL: POST /admin/config
     */
    public function updateConfig(Request $request)
    {
        if (!$request->session()->get('admin_auth')) {
            abort(403);
        }

        $validated = $request->validate([
            'groom_name'            => ['required', 'string', 'max:100'],
            'bride_name'            => ['required', 'string', 'max:100'],
            'wedding_date'          => ['required', 'date'],
            'bride_wedding_date'     => ['nullable', 'date'],
            'groom_parents'         => ['nullable', 'string', 'max:255'],
            'bride_parents'         => ['nullable', 'string', 'max:255'],
            'groom_event_location'  => ['nullable', 'string', 'max:255'],
            'groom_event_address'   => ['nullable', 'string', 'max:500'],
            'groom_map_url'         => ['nullable', 'string', 'max:2000'],
            'groom_map_iframe_url'  => ['nullable', 'string', 'max:2000'],
            'bride_event_location'  => ['nullable', 'string', 'max:255'],
            'bride_event_address'   => ['nullable', 'string', 'max:500'],
            'bride_map_url'         => ['nullable', 'string', 'max:2000'],
            'bride_map_iframe_url'  => ['nullable', 'string', 'max:2000'],
            'hero_image_url'        => ['nullable', 'string', 'max:2000'],
            'hero_image_position'   => ['nullable', 'string', 'max:50'],
            'hero_image_scale'      => ['nullable', 'numeric', 'min:0.5', 'max:3'],
            'groom_image_url'       => ['nullable', 'string', 'max:2000'],
            'groom_image_position'  => ['nullable', 'string', 'max:50'],
            'groom_image_scale'     => ['nullable', 'numeric', 'min:0.5', 'max:3'],
            'bride_image_url'       => ['nullable', 'string', 'max:2000'],
            'bride_image_position'  => ['nullable', 'string', 'max:50'],
            'bride_image_scale'     => ['nullable', 'numeric', 'min:0.5', 'max:3'],
            'background_music_url'  => ['nullable', 'string', 'max:2000'],
            // Bank info fields
            'groom_bank_name'       => ['nullable', 'string', 'max:100'],
            'groom_account_no'      => ['nullable', 'string', 'max:50'],
            'groom_account_name'    => ['nullable', 'string', 'max:100'],
            'groom_qr_url'          => ['nullable', 'string', 'max:2000'],
            'bride_bank_name'       => ['nullable', 'string', 'max:100'],
            'bride_account_no'      => ['nullable', 'string', 'max:50'],
            'bride_account_name'    => ['nullable', 'string', 'max:100'],
            'bride_qr_url'          => ['nullable', 'string', 'max:2000'],
        ]);

        $bankInfo = [];
        if ($validated['groom_account_no']) {
            $bankInfo['groom'] = [
                'bank_name'    => $validated['groom_bank_name'] ?? '',
                'account_no'   => $validated['groom_account_no'],
                'account_name' => $validated['groom_account_name'] ?? '',
                'qr_url'       => $validated['groom_qr_url'] ?? '',
            ];
        }
        if ($validated['bride_account_no']) {
            $bankInfo['bride'] = [
                'bank_name'    => $validated['bride_bank_name'] ?? '',
                'account_no'   => $validated['bride_account_no'],
                'account_name' => $validated['bride_account_name'] ?? '',
                'qr_url'       => $validated['bride_qr_url'] ?? '',
            ];
        }

        $config = WeddingConfig::firstOrFail();
        $config->update([
            'groom_name'           => $validated['groom_name'],
            'bride_name'           => $validated['bride_name'],
            'wedding_date'         => $validated['wedding_date'],
            'bride_wedding_date'   => $validated['bride_wedding_date'] ?? null,
            'groom_parents'        => $validated['groom_parents'],
            'bride_parents'        => $validated['bride_parents'],
            'groom_event_location' => $validated['groom_event_location'],
            'groom_event_address'  => $validated['groom_event_address'],
            'groom_map_url'        => $validated['groom_map_url'],
            'groom_map_iframe_url' => $validated['groom_map_iframe_url'],
            'bride_event_location' => $validated['bride_event_location'],
            'bride_event_address'  => $validated['bride_event_address'],
            'bride_map_url'        => $validated['bride_map_url'],
            'bride_map_iframe_url' => $validated['bride_map_iframe_url'],
            'hero_image_url'       => $validated['hero_image_url'],
            'hero_image_position'  => $validated['hero_image_position'] ?? 'center center',
            'hero_image_scale'     => $validated['hero_image_scale'] ?? 1,
            'groom_image_url'      => $validated['groom_image_url'],
            'groom_image_position' => $validated['groom_image_position'] ?? 'center center',
            'groom_image_scale'    => $validated['groom_image_scale'] ?? 1,
            'bride_image_url'      => $validated['bride_image_url'],
            'bride_image_position' => $validated['bride_image_position'] ?? 'center center',
            'bride_image_scale'    => $validated['bride_image_scale'] ?? 1,
            'background_music_url' => $validated['background_music_url'],
            'bank_account_info'    => json_encode($bankInfo, JSON_UNESCAPED_UNICODE),
        ]);

        return back()->with('success', 'Đã lưu cấu hình thành công!');
    }

    /**
     * Trang quản lý album ảnh cưới.
     * URL: GET /admin/gallery
     */
    public function adminGallery(Request $request)
    {
        if (!$request->session()->get('admin_auth')) {
            return view('admin.login');
        }

        $photos = GalleryPhoto::orderBy('sort_order')->orderBy('id')->get();

        return view('admin.gallery', compact('photos'));
    }

    /**
     * Thêm ảnh vào album.
     * URL: POST /admin/gallery
     */
    public function storeGalleryPhoto(Request $request)
    {
        if (!$request->session()->get('admin_auth')) {
            abort(403);
        }

        $validated = $request->validate([
            'image_url'       => ['required', 'string', 'max:2000'],
            'alt_text'        => ['nullable', 'string', 'max:255'],
            'layout'          => ['required', 'in:normal,tall,wide'],
            'object_fit'      => ['required', 'in:cover,contain,fill'],
            'object_position' => ['required', 'string', 'max:50'],
            'sort_order'      => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        GalleryPhoto::create($validated);

        return back()->with('success', 'Đã thêm ảnh thành công!');
    }

    /**
     * Cập nhật ảnh trong album.
     * URL: PUT /admin/gallery/{photo}
     */
    public function updateGalleryPhoto(Request $request, GalleryPhoto $photo)
    {
        if (!$request->session()->get('admin_auth')) {
            abort(403);
        }

        $validated = $request->validate([
            'image_url'       => ['required', 'string', 'max:2000'],
            'alt_text'        => ['nullable', 'string', 'max:255'],
            'layout'          => ['required', 'in:normal,tall,wide'],
            'object_fit'      => ['required', 'in:cover,contain,fill'],
            'object_position' => ['required', 'string', 'max:50'],
            'sort_order'      => ['nullable', 'integer', 'min:0'],
        ]);

        $validated['sort_order'] = $validated['sort_order'] ?? 0;

        $photo->update($validated);

        return back()->with('success', 'Đã cập nhật ảnh thành công!');
    }

    /**
     * Xóa ảnh khỏi album.
     * URL: DELETE /admin/gallery/{photo}
     */
    public function deleteGalleryPhoto(Request $request, GalleryPhoto $photo)
    {
        if (!$request->session()->get('admin_auth')) {
            abort(403);
        }

        $photo->delete();

        return back()->with('success', 'Đã xóa ảnh thành công!');
    }

    /**
     * Xóa tất cả RSVP.
     * URL: DELETE /admin/rsvp
     */
    public function deleteAllRsvps(Request $request)
    {
        if (!$request->session()->get('admin_auth')) {
            abort(403);
        }

        Rsvp::truncate();

        return redirect()->route('admin.rsvp')->with('success', 'Đã xóa tất cả RSVP thành công!');
    }
}
