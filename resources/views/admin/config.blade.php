<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin - Cấu hình thiệp cưới</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      background: #f4ede4;
      font-family: 'Segoe UI', sans-serif;
      color: #3a2c1e;
      min-height: 100vh;
    }

    /* Header */
    .admin-header {
      background: linear-gradient(135deg, #5a3e2b, #7a5c3e);
      color: #fff;
      padding: 1rem 1.5rem;
      display: flex;
      align-items: center;
      justify-content: space-between;
      gap: 1rem;
      flex-wrap: wrap;
    }

    .admin-header h1 { font-size: 1.2rem; font-weight: 700; }
    .admin-header p  { font-size: 0.8rem; opacity: 0.75; margin-top: 0.15rem; }

    .header-actions { display: flex; gap: 0.6rem; align-items: center; flex-wrap: wrap; }

    .btn-nav {
      padding: 0.4rem 1rem;
      background: rgba(255,255,255,0.15);
      color: #fff;
      border: 1px solid rgba(255,255,255,0.3);
      border-radius: 6px;
      font-size: 0.82rem;
      cursor: pointer;
      text-decoration: none;
      transition: background 0.2s;
      white-space: nowrap;
    }
    .btn-nav:hover { background: rgba(255,255,255,0.25); }

    /* Alert */
    .alert {
      margin: 1.2rem 1.5rem 0;
      padding: 0.8rem 1.1rem;
      border-radius: 8px;
      font-size: 0.88rem;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }
    .alert-success { background: #e8f5e9; color: #2e7d32; border: 1px solid #c8e6c9; }
    .alert-error   { background: #ffebee; color: #c62828; border: 1px solid #ffcdd2; }

    /* Content */
    .content { padding: 1.5rem; max-width: 860px; margin: 0 auto; }

    /* Card sections */
    .config-section {
      background: #fff;
      border-radius: 12px;
      box-shadow: 0 2px 12px rgba(0,0,0,0.07);
      margin-bottom: 1.4rem;
      overflow: hidden;
    }

    .section-title {
      background: #f9f0e6;
      border-bottom: 1px solid #ede0cc;
      padding: 0.75rem 1.2rem;
      font-size: 0.88rem;
      font-weight: 700;
      color: #7a5c3e;
      letter-spacing: 0.04em;
      text-transform: uppercase;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .section-body { padding: 1.2rem; }

    /* Form elements */
    .form-grid {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
    }

    .form-group { display: flex; flex-direction: column; gap: 0.3rem; }
    .form-group.full { grid-column: 1 / -1; }

    label {
      font-size: 0.8rem;
      font-weight: 600;
      color: #7a5c3e;
    }

    .field-hint {
      font-size: 0.73rem;
      color: #aaa;
      font-weight: 400;
      margin-left: 0.3rem;
    }

    input[type="text"],
    input[type="datetime-local"],
    textarea {
      padding: 0.6rem 0.85rem;
      border: 1.5px solid #e0d0c0;
      border-radius: 7px;
      font-size: 0.88rem;
      font-family: inherit;
      color: #3a2c1e;
      background: #fff;
      outline: none;
      transition: border-color 0.2s;
      width: 100%;
    }

    input:focus, textarea:focus { border-color: #b48c64; }

    textarea { resize: vertical; min-height: 72px; }

    .input-error { border-color: #e57373 !important; }
    .error-msg   { font-size: 0.75rem; color: #c62828; margin-top: 0.15rem; }

    /* Save button */
    .btn-save {
      padding: 0.75rem 2.5rem;
      background: linear-gradient(135deg, #b48c64, #9a7450);
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 0.95rem;
      font-weight: 600;
      cursor: pointer;
      transition: opacity 0.2s;
      margin-top: 0.5rem;
    }
    .btn-save:hover { opacity: 0.88; }

    .form-actions { display: flex; justify-content: flex-end; padding: 0 0 0.5rem; }

    /* Divider between groom/bride in same section */
    .venue-divider {
      grid-column: 1 / -1;
      border: none;
      border-top: 1px dashed #e0d0c0;
      margin: 0.4rem 0;
    }

    .venue-label {
      grid-column: 1 / -1;
      font-size: 0.78rem;
      font-weight: 700;
      color: #b48c64;
      text-transform: uppercase;
      letter-spacing: 0.05em;
      margin-bottom: -0.3rem;
    }

    @media (max-width: 600px) {
      .form-grid { grid-template-columns: 1fr; }
      .form-group.full { grid-column: 1; }
      .content { padding: 1rem; }
    }
  </style>
</head>
<body>

<header class="admin-header">
  <div>
    <h1>⚙️ Cấu hình thiệp cưới</h1>
    <p>Chỉnh sửa thông tin hiển thị trên thiệp</p>
  </div>
  <div class="header-actions">
    <a href="{{ route('admin.rsvp') }}" class="btn-nav">📋 Danh sách RSVP</a>
    <form method="POST" action="{{ route('admin.logout') }}">
      @csrf
      <button type="submit" class="btn-nav">Đăng xuất</button>
    </form>
  </div>
</header>

@if(session('success'))
  <div class="alert alert-success">✓ {{ session('success') }}</div>
@endif

@if($errors->any())
  <div class="alert alert-error">✗ Vui lòng kiểm tra lại các trường bên dưới.</div>
@endif

<div class="content">
<form method="POST" action="{{ route('admin.config.update') }}">
  @csrf

  {{-- ── CÔ DÂU & CHÚ RỂ ── --}}
  <div class="config-section">
    <div class="section-title">👫 Cô dâu &amp; Chú rể</div>
    <div class="section-body">
      <div class="form-grid">
        <div class="form-group">
          <label>Tên chú rể <span class="required" style="color:#c62828">*</span></label>
          <input type="text" name="groom_name" value="{{ old('groom_name', $config->groom_name) }}" required maxlength="100" class="{{ $errors->has('groom_name') ? 'input-error' : '' }}" />
          @error('groom_name')<p class="error-msg">{{ $message }}</p>@enderror
        </div>
        <div class="form-group">
          <label>Tên cô dâu <span class="required" style="color:#c62828">*</span></label>
          <input type="text" name="bride_name" value="{{ old('bride_name', $config->bride_name) }}" required maxlength="100" class="{{ $errors->has('bride_name') ? 'input-error' : '' }}" />
          @error('bride_name')<p class="error-msg">{{ $message }}</p>@enderror
        </div>
        <div class="form-group">
          <label>Ngày giờ cưới <span class="required" style="color:#c62828">*</span></label>
          <input type="datetime-local" name="wedding_date" value="{{ old('wedding_date', $config->wedding_date->format('Y-m-d\TH:i')) }}" required class="{{ $errors->has('wedding_date') ? 'input-error' : '' }}" />
          @error('wedding_date')<p class="error-msg">{{ $message }}</p>@enderror
        </div>
        <div class="form-group">
          <label>Ảnh nền hero <span class="field-hint">URL ảnh</span></label>
          <input type="text" name="hero_image_url" value="{{ old('hero_image_url', $config->hero_image_url) }}" placeholder="https://..." />
        </div>
        <div class="form-group">
          <label>Nhạc nền <span class="field-hint">URL file .mp3</span></label>
          <input type="text" name="background_music_url" value="{{ old('background_music_url', $config->background_music_url) }}" placeholder="https://..." />
        </div>
      </div>
    </div>
  </div>

  {{-- ── PHỤ HUYNH ── --}}
  <div class="config-section">
    <div class="section-title">👨‍👩‍👦 Thông tin phụ huynh</div>
    <div class="section-body">
      <div class="form-grid">
        <div class="form-group full">
          <label>Bố mẹ nhà trai <span class="field-hint">dùng " &amp; " để phân cách Ông và Bà</span></label>
          <input type="text" name="groom_parents" value="{{ old('groom_parents', $config->groom_parents) }}" placeholder="Ông Nguyễn Văn A & Bà Trần Thị B" />
        </div>
        <div class="form-group full">
          <label>Bố mẹ nhà gái <span class="field-hint">dùng " &amp; " để phân cách Ông và Bà</span></label>
          <input type="text" name="bride_parents" value="{{ old('bride_parents', $config->bride_parents) }}" placeholder="Ông Lê Văn C & Bà Phạm Thị D" />
        </div>
      </div>
    </div>
  </div>

  {{-- ── ĐỊA ĐIỂM ── --}}
  <div class="config-section">
    <div class="section-title">📍 Địa điểm tổ chức</div>
    <div class="section-body">
      <div class="form-grid">

        <div class="venue-label">🤵 Nhà trai (type=1)</div>
        <div class="form-group">
          <label>Tên địa điểm</label>
          <input type="text" name="groom_event_location" value="{{ old('groom_event_location', $config->groom_event_location) }}" placeholder="Trung Tâm Tiệc Cưới White Palace" />
        </div>
        <div class="form-group">
          <label>Địa chỉ</label>
          <input type="text" name="groom_event_address" value="{{ old('groom_event_address', $config->groom_event_address) }}" placeholder="194 Hoàng Văn Thụ, Q.Phú Nhuận" />
        </div>
        <div class="form-group full">
          <label>Link Google Maps trực tiếp <span class="field-hint">URL share từ Google Maps</span></label>
          <input type="text" name="groom_map_url" value="{{ old('groom_map_url', $config->groom_map_url) }}" placeholder="https://maps.app.goo.gl/..." />
        </div>
        <div class="form-group full">
          <label>Google Maps iframe src <span class="field-hint">chỉ lấy phần URL trong src="..."</span></label>
          <textarea name="groom_map_iframe_url" placeholder="https://www.google.com/maps/embed?pb=...">{{ old('groom_map_iframe_url', $config->groom_map_iframe_url) }}</textarea>
        </div>

        <hr class="venue-divider" />

        <div class="venue-label">👰 Nhà gái (type=2)</div>
        <div class="form-group">
          <label>Tên địa điểm</label>
          <input type="text" name="bride_event_location" value="{{ old('bride_event_location', $config->bride_event_location) }}" placeholder="Nhà Hàng Tiệc Cưới..." />
        </div>
        <div class="form-group">
          <label>Địa chỉ</label>
          <input type="text" name="bride_event_address" value="{{ old('bride_event_address', $config->bride_event_address) }}" placeholder="Số X, đường Y, quận Z" />
        </div>
        <div class="form-group full">
          <label>Link Google Maps trực tiếp <span class="field-hint">URL share từ Google Maps</span></label>
          <input type="text" name="bride_map_url" value="{{ old('bride_map_url', $config->bride_map_url) }}" placeholder="https://maps.app.goo.gl/..." />
        </div>
        <div class="form-group full">
          <label>Google Maps iframe src</label>
          <textarea name="bride_map_iframe_url" placeholder="https://www.google.com/maps/embed?pb=...">{{ old('bride_map_iframe_url', $config->bride_map_iframe_url) }}</textarea>
        </div>

      </div>
    </div>
  </div>

  {{-- ── THÔNG TIN NGÂN HÀNG ── --}}
  <div class="config-section">
    <div class="section-title">💳 Thông tin ngân hàng (mừng cưới)</div>
    <div class="section-body">
      <div class="form-grid">

        <div class="venue-label">🤵 Chú rể</div>
        <div class="form-group">
          <label>Tên ngân hàng</label>
          <input type="text" name="groom_bank_name" value="{{ old('groom_bank_name', $bankInfo['groom']['bank_name'] ?? '') }}" placeholder="Vietcombank" />
        </div>
        <div class="form-group">
          <label>Số tài khoản</label>
          <input type="text" name="groom_account_no" value="{{ old('groom_account_no', $bankInfo['groom']['account_no'] ?? '') }}" placeholder="1234567890" />
        </div>
        <div class="form-group full">
          <label>Tên chủ tài khoản</label>
          <input type="text" name="groom_account_name" value="{{ old('groom_account_name', $bankInfo['groom']['account_name'] ?? '') }}" placeholder="NGUYEN VAN A" />
        </div>
        <div class="form-group full">
          <label>Ảnh QR tùy chỉnh <span class="field-hint">URL ảnh QR — nếu để trống sẽ tự sinh từ VietQR</span></label>
          <input type="text" name="groom_qr_url" value="{{ old('groom_qr_url', $bankInfo['groom']['qr_url'] ?? '') }}" placeholder="https://..." />
        </div>

        <hr class="venue-divider" />

        <div class="venue-label">👰 Cô dâu</div>
        <div class="form-group">
          <label>Tên ngân hàng</label>
          <input type="text" name="bride_bank_name" value="{{ old('bride_bank_name', $bankInfo['bride']['bank_name'] ?? '') }}" placeholder="MB Bank" />
        </div>
        <div class="form-group">
          <label>Số tài khoản</label>
          <input type="text" name="bride_account_no" value="{{ old('bride_account_no', $bankInfo['bride']['account_no'] ?? '') }}" placeholder="0987654321" />
        </div>
        <div class="form-group full">
          <label>Tên chủ tài khoản</label>
          <input type="text" name="bride_account_name" value="{{ old('bride_account_name', $bankInfo['bride']['account_name'] ?? '') }}" placeholder="TRAN THI B" />
        </div>
        <div class="form-group full">
          <label>Ảnh QR tùy chỉnh <span class="field-hint">URL ảnh QR — nếu để trống sẽ tự sinh từ VietQR</span></label>
          <input type="text" name="bride_qr_url" value="{{ old('bride_qr_url', $bankInfo['bride']['qr_url'] ?? '') }}" placeholder="https://..." />
        </div>

      </div>
    </div>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn-save">💾 Lưu cấu hình</button>
  </div>

</form>
</div>

</body>
</html>
