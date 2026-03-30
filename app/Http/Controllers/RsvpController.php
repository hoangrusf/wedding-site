<?php

namespace App\Http\Controllers;

use App\Models\GalleryPhoto;
use App\Models\Guest;
use App\Models\Rsvp;
use App\Models\WeddingConfig;
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

        // Lấy lời chúc đã có
        $wishes = Rsvp::whereNotNull('wishes_message')
            ->where('wishes_message', '!=', '')
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

    /**
     * Lấy danh sách lời chúc (API cho AJAX load thêm).
     */
    public function wishes()
    {
        $wishes = Rsvp::whereNotNull('wishes_message')
            ->where('wishes_message', '!=', '')
            ->latest()
            ->get(['guest_name', 'wishes_message', 'created_at']);

        return response()->json($wishes);
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

        $totalAttending     = $rsvps->where('is_attending', true)->sum(fn($r) => 1 + $r->companion_count);
        $totalNotAttending  = $rsvps->where('is_attending', false)->count();

        return view('admin.rsvp', compact('rsvps', 'totalAttending', 'totalNotAttending'));
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
            'groom_image_url'       => ['nullable', 'string', 'max:2000'],
            'bride_image_url'       => ['nullable', 'string', 'max:2000'],
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
            'groom_image_url'      => $validated['groom_image_url'],
            'bride_image_url'      => $validated['bride_image_url'],
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
            'image_url'  => ['required', 'string', 'max:2000'],
            'alt_text'   => ['nullable', 'string', 'max:255'],
            'layout'     => ['required', 'in:normal,tall,wide'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
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
            'image_url'  => ['required', 'string', 'max:2000'],
            'alt_text'   => ['nullable', 'string', 'max:255'],
            'layout'     => ['required', 'in:normal,tall,wide'],
            'sort_order' => ['nullable', 'integer', 'min:0'],
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
}
