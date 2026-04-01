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

    /* Image preview with drag-to-reposition */
    .img-preview-wrapper {
      margin-top: 0.4rem;
      display: flex;
      align-items: flex-start;
      gap: 0.7rem;
    }

    .img-preview-box {
      width: 180px;
      height: 120px;
      border: 2px dashed #d9c8af;
      border-radius: 8px;
      overflow: hidden;
      background: #f9f0e6;
      position: relative;
      cursor: grab;
      flex-shrink: 0;
      user-select: none;
      -webkit-user-select: none;
    }
    .img-preview-box:active { cursor: grabbing; }

    .img-preview-box img {
      width: 100%;
      height: 100%;
      display: block;
      object-fit: cover;
      pointer-events: none;
    }

    .img-preview-box .drag-hint {
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      background: rgba(0,0,0,0.5);
      color: #fff;
      font-size: 0.65rem;
      text-align: center;
      padding: 2px 4px;
      opacity: 0;
      transition: opacity 0.2s;
      pointer-events: none;
    }
    .img-preview-box:hover .drag-hint { opacity: 1; }

    .img-preview-box.empty {
      display: flex;
      align-items: center;
      justify-content: center;
      cursor: default;
    }
    .img-preview-box.empty .placeholder-text {
      font-size: 0.75rem;
      color: #bba78f;
    }

    .img-position-label {
      font-size: 0.72rem;
      color: #999;
      margin-top: 0.25rem;
    }

    @media (max-width: 600px) {
      .form-grid { grid-template-columns: 1fr; }
      .form-group.full { grid-column: 1; }
      .content { padding: 1rem; }
      .img-preview-box { width: 140px; height: 95px; }
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
    <a href="{{ route('admin.gallery') }}" class="btn-nav">🖼️ Album ảnh</a>
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
          <label>Ảnh nền hero <span class="field-hint">URL hoặc tên file trong hero_image_url/</span></label>
          <input type="text" name="hero_image_url" value="{{ old('hero_image_url', $config->hero_image_url) }}" placeholder="ten_anh.jpg hoặc https://..." data-preview="preview-hero" data-folder="hero_image_url" />
          <input type="hidden" name="hero_image_position" id="pos-hero" value="{{ old('hero_image_position', $config->hero_image_position ?? 'center center') }}" />
          <div class="img-preview-wrapper">
            <div class="img-preview-box" id="preview-hero" data-position-input="pos-hero"></div>
            <div class="img-position-label" id="pos-label-hero">Vị trí: {{ old('hero_image_position', $config->hero_image_position ?? 'center center') }}</div>
          </div>
        </div>
        <div class="form-group">
          <label>Nhạc nền <span class="field-hint">URL hoặc tên file trong bg-music/</span></label>
          <input type="text" name="background_music_url" value="{{ old('background_music_url', $config->background_music_url) }}" placeholder="ten_file.mp3 hoặc https://..." />
        </div>
        <div class="form-group">
          <label>Ảnh chú rể <span class="field-hint">URL hoặc tên file trong groom_image_url/</span></label>
          <input type="text" name="groom_image_url" value="{{ old('groom_image_url', $config->groom_image_url) }}" placeholder="ten_anh.jpg hoặc https://..." data-preview="preview-groom" data-folder="groom_image_url" />
          <input type="hidden" name="groom_image_position" id="pos-groom" value="{{ old('groom_image_position', $config->groom_image_position ?? 'center center') }}" />
          <div class="img-preview-wrapper">
            <div class="img-preview-box" id="preview-groom" data-position-input="pos-groom"></div>
            <div class="img-position-label" id="pos-label-groom">Vị trí: {{ old('groom_image_position', $config->groom_image_position ?? 'center center') }}</div>
          </div>
        </div>
        <div class="form-group">
          <label>Ảnh cô dâu <span class="field-hint">URL hoặc tên file trong bride_image_url/</span></label>
          <input type="text" name="bride_image_url" value="{{ old('bride_image_url', $config->bride_image_url) }}" placeholder="ten_anh.jpg hoặc https://..." data-preview="preview-bride" data-folder="bride_image_url" />
          <input type="hidden" name="bride_image_position" id="pos-bride" value="{{ old('bride_image_position', $config->bride_image_position ?? 'center center') }}" />
          <div class="img-preview-wrapper">
            <div class="img-preview-box" id="preview-bride" data-position-input="pos-bride"></div>
            <div class="img-position-label" id="pos-label-bride">Vị trí: {{ old('bride_image_position', $config->bride_image_position ?? 'center center') }}</div>
          </div>
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
          <label>Ảnh QR tùy chỉnh <span class="field-hint">URL hoặc tên file trong groom_qr_url/ — để trống sẽ tự sinh từ VietQR</span></label>
          <input type="text" name="groom_qr_url" value="{{ old('groom_qr_url', $bankInfo['groom']['qr_url'] ?? '') }}" placeholder="qr_chure.jpg hoặc https://..." />
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
          <label>Ảnh QR tùy chỉnh <span class="field-hint">URL hoặc tên file trong bride_qr_url/ — để trống sẽ tự sinh từ VietQR</span></label>
          <input type="text" name="bride_qr_url" value="{{ old('bride_qr_url', $bankInfo['bride']['qr_url'] ?? '') }}" placeholder="qr_codau.jpg hoặc https://..." />
        </div>

      </div>
    </div>
  </div>

  <div class="form-actions">
    <button type="submit" class="btn-save">💾 Lưu cấu hình</button>
  </div>

</form>
</div>

<script>
(function() {
  // ── Resolve image URL ──
  function resolveUrl(val, folder) {
    if (!val) return '';
    val = val.trim();
    if (/^https?:\/\//i.test(val)) return val;
    return '/' + folder + '/' + val;
  }

  // ── Render preview image ──
  function renderPreview(box, src, position) {
    if (!src) {
      box.classList.add('empty');
      box.innerHTML = '<span class="placeholder-text">Chưa có ảnh</span>';
      return;
    }
    box.classList.remove('empty');
    box.innerHTML = '<img src="' + src + '" style="object-position:' + position + '" /><span class="drag-hint">Nhấn giữ & kéo để căn chỉnh</span>';
  }

  // ── Init all image inputs ──
  document.querySelectorAll('input[data-preview]').forEach(function(input) {
    var previewId = input.getAttribute('data-preview');
    var folder    = input.getAttribute('data-folder');
    var box       = document.getElementById(previewId);
    var posInput  = document.getElementById(box.getAttribute('data-position-input'));
    var labelId   = 'pos-label-' + previewId.replace('preview-', '');
    var label     = document.getElementById(labelId);

    // Initial render
    renderPreview(box, resolveUrl(input.value, folder), posInput.value);

    // Live update on URL change
    input.addEventListener('input', function() {
      renderPreview(box, resolveUrl(input.value, folder), posInput.value);
    });

    // ── Drag-to-reposition ──
    var dragging = false;
    var startX, startY, startPosX, startPosY;

    function parsePosition(pos) {
      var parts = (pos || 'center center').split(/\s+/);
      var x = parseFloat(parts[0]);
      var y = parseFloat(parts[1]);
      if (isNaN(x)) x = 50;
      if (isNaN(y)) y = 50;
      return { x: x, y: y };
    }

    function onStart(e) {
      var img = box.querySelector('img');
      if (!img) return;
      e.preventDefault();
      dragging = true;
      var pt = e.touches ? e.touches[0] : e;
      startX = pt.clientX;
      startY = pt.clientY;
      var cur = parsePosition(posInput.value);
      startPosX = cur.x;
      startPosY = cur.y;
      box.style.cursor = 'grabbing';
    }

    function onMove(e) {
      if (!dragging) return;
      e.preventDefault();
      var pt = e.touches ? e.touches[0] : e;
      var dx = pt.clientX - startX;
      var dy = pt.clientY - startY;

      // Sensitivity: ~0.5% per pixel moved
      var newX = Math.max(0, Math.min(100, startPosX - dx * 0.5));
      var newY = Math.max(0, Math.min(100, startPosY - dy * 0.5));

      var pos = Math.round(newX) + '% ' + Math.round(newY) + '%';
      posInput.value = pos;
      if (label) label.textContent = 'Vị trí: ' + pos;

      var img = box.querySelector('img');
      if (img) img.style.objectPosition = pos;
    }

    function onEnd() {
      if (!dragging) return;
      dragging = false;
      box.style.cursor = 'grab';
    }

    box.addEventListener('mousedown', onStart);
    document.addEventListener('mousemove', onMove);
    document.addEventListener('mouseup', onEnd);

    box.addEventListener('touchstart', onStart, { passive: false });
    document.addEventListener('touchmove', onMove, { passive: false });
    document.addEventListener('touchend', onEnd);
  });
})();
</script>
</body>
</html>
