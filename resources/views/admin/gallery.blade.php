<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin - Album ảnh cưới</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      background: #f4ede4;
      font-family: 'Segoe UI', sans-serif;
      color: #3a2c1e;
      min-height: 100vh;
    }

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

    .content { padding: 1.5rem; max-width: 960px; margin: 0 auto; }

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
    input[type="number"],
    select {
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
    input:focus, select:focus { border-color: #b48c64; }

    .btn-save {
      padding: 0.6rem 1.8rem;
      background: linear-gradient(135deg, #b48c64, #9a7450);
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 0.88rem;
      font-weight: 600;
      cursor: pointer;
      transition: opacity 0.2s;
    }
    .btn-save:hover { opacity: 0.88; }

    .btn-delete {
      padding: 0.45rem 0.9rem;
      background: #ffebee;
      color: #c62828;
      border: 1px solid #ffcdd2;
      border-radius: 6px;
      font-size: 0.8rem;
      cursor: pointer;
      transition: background 0.2s;
    }
    .btn-delete:hover { background: #ffcdd2; }

    .btn-edit {
      padding: 0.45rem 0.9rem;
      background: #e3f2fd;
      color: #1565c0;
      border: 1px solid #bbdefb;
      border-radius: 6px;
      font-size: 0.8rem;
      cursor: pointer;
      transition: background 0.2s;
    }
    .btn-edit:hover { background: #bbdefb; }

    /* Photo list */
    .photo-list { margin-top: 1rem; }

    .photo-item {
      display: grid;
      grid-template-columns: 100px 1fr auto;
      gap: 1rem;
      align-items: center;
      padding: 0.8rem 0;
      border-bottom: 1px solid #f0e8dc;
    }
    .photo-item:last-child { border-bottom: none; }

    .photo-thumb {
      width: 100px;
      height: 70px;
      border-radius: 6px;
      background: #f4ede4;
    }

    .photo-info { font-size: 0.85rem; }
    .photo-info .url { color: #999; font-size: 0.75rem; word-break: break-all; }
    .photo-info .meta { color: #7a5c3e; font-size: 0.78rem; margin-top: 0.2rem; }

    .photo-actions { display: flex; gap: 0.4rem; align-items: center; }

    .layout-badge {
      display: inline-block;
      padding: 0.15rem 0.5rem;
      border-radius: 4px;
      font-size: 0.72rem;
      font-weight: 600;
      text-transform: uppercase;
    }
    .layout-normal { background: #e8f5e9; color: #2e7d32; }
    .layout-tall   { background: #e3f2fd; color: #1565c0; }
    .layout-wide   { background: #fff3e0; color: #e65100; }

    .empty-state {
      text-align: center;
      padding: 3rem 1rem;
      color: #999;
      font-size: 0.9rem;
    }

    /* Edit modal */
    .modal-overlay {
      display: none;
      position: fixed;
      inset: 0;
      background: rgba(0,0,0,0.4);
      z-index: 1000;
      justify-content: center;
      align-items: center;
    }
    .modal-overlay.active { display: flex; }
    .modal-box {
      background: #fff;
      border-radius: 12px;
      padding: 1.5rem;
      width: 90%;
      max-width: 500px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.2);
    }
    .modal-box h3 { margin-bottom: 1rem; color: #5a3e2b; font-size: 1rem; }
    .modal-actions { display: flex; gap: 0.6rem; justify-content: flex-end; margin-top: 1rem; }
    .btn-cancel {
      padding: 0.5rem 1.2rem;
      background: #f4ede4;
      color: #7a5c3e;
      border: 1px solid #e0d0c0;
      border-radius: 6px;
      font-size: 0.85rem;
      cursor: pointer;
    }

    @media (max-width: 600px) {
      .form-grid { grid-template-columns: 1fr; }
      .form-group.full { grid-column: 1; }
      .photo-item { grid-template-columns: 80px 1fr; }
      .photo-actions { grid-column: 1 / -1; justify-content: flex-end; }
    }

    /* Position picker grid */
    .position-picker {
      display: inline-grid;
      grid-template-columns: repeat(3, 28px);
      grid-template-rows: repeat(3, 28px);
      gap: 3px;
      border: 1.5px solid #e0d0c0;
      border-radius: 6px;
      padding: 4px;
      background: #faf6f0;
    }
    .position-picker .pos-cell {
      width: 28px;
      height: 28px;
      border-radius: 4px;
      border: 1.5px solid transparent;
      background: #ede0cc;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 0.55rem;
      color: #999;
      transition: all 0.15s;
    }
    .position-picker .pos-cell:hover { background: #d9c8af; }
    .position-picker .pos-cell.active {
      background: #b48c64;
      border-color: #9a7450;
      color: #fff;
      font-weight: 700;
    }

    /* Image preview with fit/position */
    .img-preview-box {
      width: 180px;
      height: 130px;
      border: 2px dashed #d9c8af;
      border-radius: 8px;
      overflow: hidden;
      background: #f9f0e6;
      position: relative;
      cursor: grab;
      user-select: none;
      -webkit-user-select: none;
    }
    .img-preview-box:active { cursor: grabbing; }
    .img-preview-box img {
      width: 100%;
      height: 100%;
      display: block;
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
    .img-position-label {
      font-size: 0.72rem;
      color: #999;
      margin-top: 0.2rem;
    }
    .fit-position-row {
      display: flex;
      gap: 1rem;
      align-items: flex-start;
      flex-wrap: wrap;
    }
    .fit-position-group {
      display: flex;
      flex-direction: column;
      gap: 0.3rem;
    }

    .fit-badge {
      display: inline-block;
      padding: 0.15rem 0.5rem;
      border-radius: 4px;
      font-size: 0.72rem;
      font-weight: 600;
      background: #f3e8db;
      color: #7a5c3e;
    }
  </style>
</head>
<body>

<header class="admin-header">
  <div>
    <h1>🖼️ Album ảnh cưới</h1>
    <p>Quản lý ảnh hiển thị trên thiệp — Tổng: {{ $photos->count() }} ảnh</p>
  </div>
  <div class="header-actions">
    <a href="{{ route('admin.rsvp') }}" class="btn-nav">📋 RSVP</a>
    <a href="{{ route('admin.config') }}" class="btn-nav">⚙️ Cấu hình</a>
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

  {{-- ── THÊM ẢNH MỚI ── --}}
  <div class="config-section">
    <div class="section-title">➕ Thêm ảnh mới</div>
    <div class="section-body">
      <form method="POST" action="{{ route('admin.gallery.store') }}">
        @csrf
        <div class="form-grid">
          <div class="form-group full">
            <label>URL ảnh hoặc tên file <span class="field-hint">tên file trong gallery/ hoặc URL đầy đủ</span></label>
            <input type="text" name="image_url" placeholder="anh_cuoi_1.jpg hoặc https://..." required />
            @error('image_url')<p style="font-size:0.75rem;color:#c62828;">{{ $message }}</p>@enderror
          </div>
          <div class="form-group">
            <label>Mô tả ảnh <span class="field-hint">alt text</span></label>
            <input type="text" name="alt_text" placeholder="Ảnh cưới ngoài trời" maxlength="255" />
          </div>
          <div class="form-group">
            <label>Bố cục</label>
            <select name="layout">
              <option value="normal">Normal — 1 ô</option>
              <option value="tall">Tall — cao 2 hàng</option>
              <option value="wide">Wide — rộng 2 cột</option>
            </select>
          </div>
          <div class="form-group">
            <label>Chế độ cắt ảnh</label>
            <select name="object_fit" id="add-object-fit" onchange="updateAddPreview()">
              <option value="cover">Cover — lấp đầy khung (cắt)</option>
              <option value="contain">Contain — vừa khung (không cắt)</option>
              <option value="fill">Fill — kéo giãn đầy khung</option>
            </select>
          </div>
          <div class="form-group full">
            <label>Căn chỉnh ảnh trong khung <span class="field-hint">nhấn giữ & kéo ảnh để căn chỉnh vị trí</span></label>
            <div class="fit-position-row">
              <div class="fit-position-group">
                <div class="position-picker" id="add-position-picker">
                  <div class="pos-cell" data-pos="top left" title="Trên-Trái">↖</div>
                  <div class="pos-cell" data-pos="top center" title="Trên-Giữa">↑</div>
                  <div class="pos-cell" data-pos="top right" title="Trên-Phải">↗</div>
                  <div class="pos-cell" data-pos="center left" title="Giữa-Trái">←</div>
                  <div class="pos-cell active" data-pos="center center" title="Giữa">●</div>
                  <div class="pos-cell" data-pos="center right" title="Giữa-Phải">→</div>
                  <div class="pos-cell" data-pos="bottom left" title="Dưới-Trái">↙</div>
                  <div class="pos-cell" data-pos="bottom center" title="Dưới-Giữa">↓</div>
                  <div class="pos-cell" data-pos="bottom right" title="Dưới-Phải">↘</div>
                </div>
                <input type="hidden" name="object_position" id="add-object-position" value="center center" />
              </div>
              <div class="fit-position-group">
                <label style="font-size:0.73rem;">Xem trước <span class="field-hint">— kéo ảnh để chỉnh vị trí</span></label>
                <div class="img-preview-box" id="add-preview-box" data-position-input="add-object-position">
                  <img id="add-preview-img" src="" alt="Preview" style="object-fit:cover; object-position:center center; display:none;" />
                  <span class="drag-hint">Nhấn giữ & kéo để căn chỉnh</span>
                </div>
                <div class="img-position-label" id="add-pos-label">Vị trí: center center</div>
              </div>
            </div>
          </div>
          <div class="form-group">
            <label>Thứ tự <span class="field-hint">số nhỏ hiển thị trước</span></label>
            <input type="number" name="sort_order" value="0" min="0" />
          </div>
          <div class="form-group" style="justify-content:flex-end;">
            <button type="submit" class="btn-save">➕ Thêm ảnh</button>
          </div>
        </div>
      </form>
    </div>
  </div>

  {{-- ── DANH SÁCH ẢNH ── --}}
  <div class="config-section">
    <div class="section-title">📸 Danh sách ảnh ({{ $photos->count() }})</div>
    <div class="section-body">
      @if($photos->isEmpty())
        <div class="empty-state">
          <p>Chưa có ảnh nào trong album.</p>
          <p>Hãy thêm ảnh ở form bên trên!</p>
        </div>
      @else
        <div class="photo-list">
          @foreach($photos as $photo)
            @php
              $imgSrc = $photo->image_url;
              if ($imgSrc && !str_starts_with($imgSrc, 'http://') && !str_starts_with($imgSrc, 'https://')) {
                  $imgSrc = asset('gallery/' . $imgSrc);
              }
            @endphp
            <div class="photo-item">
              <img src="{{ $imgSrc }}" alt="{{ $photo->alt_text ?? 'Ảnh cưới' }}" class="photo-thumb" loading="lazy"
                   style="object-fit: {{ $photo->object_fit ?? 'cover' }}; object-position: {{ $photo->object_position ?? 'center center' }};" />
              <div class="photo-info">
                <div class="url">{{ $photo->image_url }}</div>
                <div class="meta">
                  <span class="layout-badge layout-{{ $photo->layout }}">{{ $photo->layout }}</span>
                  <span class="fit-badge">{{ $photo->object_fit ?? 'cover' }}</span>
                  <span class="fit-badge">{{ $photo->object_position ?? 'center center' }}</span>
                  &nbsp;·&nbsp; Thứ tự: {{ $photo->sort_order }}
                  @if($photo->alt_text)
                    &nbsp;·&nbsp; {{ $photo->alt_text }}
                  @endif
                </div>
              </div>
              <div class="photo-actions">
                <button type="button" class="btn-edit" onclick="openEditModal({{ $photo->id }}, '{{ e($photo->image_url) }}', '{{ e($photo->alt_text) }}', '{{ $photo->layout }}', {{ $photo->sort_order }}, '{{ $photo->object_fit ?? 'cover' }}', '{{ $photo->object_position ?? 'center center' }}')">✏️ Sửa</button>
                <form method="POST" action="{{ route('admin.gallery.delete', $photo) }}" onsubmit="return confirm('Xóa ảnh này?')">
                  @csrf
                  @method('DELETE')
                  <button type="submit" class="btn-delete">🗑️ Xóa</button>
                </form>
              </div>
            </div>
          @endforeach
        </div>
      @endif
    </div>
  </div>

</div>

{{-- ── MODAL CHỈNH SỬA ── --}}
<div class="modal-overlay" id="edit-modal">
  <div class="modal-box">
    <h3>✏️ Chỉnh sửa ảnh</h3>
    <form method="POST" id="edit-form">
      @csrf
      @method('PUT')
      <div class="form-grid">
        <div class="form-group full">
          <label>URL ảnh hoặc tên file</label>
          <input type="text" name="image_url" id="edit-image-url" required />
        </div>
        <div class="form-group">
          <label>Mô tả ảnh</label>
          <input type="text" name="alt_text" id="edit-alt-text" maxlength="255" />
        </div>
        <div class="form-group">
          <label>Bố cục</label>
          <select name="layout" id="edit-layout">
            <option value="normal">Normal</option>
            <option value="tall">Tall</option>
            <option value="wide">Wide</option>
          </select>
        </div>
        <div class="form-group">
          <label>Chế độ cắt ảnh</label>
          <select name="object_fit" id="edit-object-fit" onchange="updateEditPreview()">
            <option value="cover">Cover — lấp đầy khung</option>
            <option value="contain">Contain — vừa khung</option>
            <option value="fill">Fill — kéo giãn</option>
          </select>
        </div>
        <div class="form-group full">
          <label>Căn chỉnh ảnh trong khung <span class="field-hint">nhấn giữ & kéo ảnh để căn chỉnh vị trí</span></label>
          <div class="fit-position-row">
            <div class="fit-position-group">
              <div class="position-picker" id="edit-position-picker">
                <div class="pos-cell" data-pos="top left" title="Trên-Trái">↖</div>
                <div class="pos-cell" data-pos="top center" title="Trên-Giữa">↑</div>
                <div class="pos-cell" data-pos="top right" title="Trên-Phải">↗</div>
                <div class="pos-cell" data-pos="center left" title="Giữa-Trái">←</div>
                <div class="pos-cell active" data-pos="center center" title="Giữa">●</div>
                <div class="pos-cell" data-pos="center right" title="Giữa-Phải">→</div>
                <div class="pos-cell" data-pos="bottom left" title="Dưới-Trái">↙</div>
                <div class="pos-cell" data-pos="bottom center" title="Dưới-Giữa">↓</div>
                <div class="pos-cell" data-pos="bottom right" title="Dưới-Phải">↘</div>
              </div>
              <input type="hidden" name="object_position" id="edit-object-position" value="center center" />
            </div>
            <div class="fit-position-group">
              <label style="font-size:0.73rem;">Xem trước <span class="field-hint">— kéo ảnh để chỉnh vị trí</span></label>
              <div class="img-preview-box" id="edit-preview-box" data-position-input="edit-object-position">
                <img id="edit-preview-img" src="" alt="Preview" style="object-fit:cover; object-position:center center; display:none;" />
                <span class="drag-hint">Nhấn giữ & kéo để căn chỉnh</span>
              </div>
              <div class="img-position-label" id="edit-pos-label">Vị trí: center center</div>
            </div>
          </div>
        </div>
        <div class="form-group">
          <label>Thứ tự</label>
          <input type="number" name="sort_order" id="edit-sort-order" min="0" />
        </div>
      </div>
      <div class="modal-actions">
        <button type="button" class="btn-cancel" onclick="closeEditModal()">Hủy</button>
        <button type="submit" class="btn-save">💾 Lưu</button>
      </div>
    </form>
  </div>
</div>

<script>
  // ── Position picker logic ──
  function initPositionPicker(pickerId, hiddenInputId, previewImgId) {
    const picker = document.getElementById(pickerId);
    const hiddenInput = document.getElementById(hiddenInputId);
    const previewImg = document.getElementById(previewImgId);

    picker.querySelectorAll('.pos-cell').forEach(cell => {
      cell.addEventListener('click', function() {
        picker.querySelectorAll('.pos-cell').forEach(c => c.classList.remove('active'));
        this.classList.add('active');
        hiddenInput.value = this.dataset.pos;
        if (previewImg) previewImg.style.objectPosition = this.dataset.pos;
        // Update label
        var labelId = hiddenInputId.replace('object-position', 'pos-label');
        var label = document.getElementById(labelId);
        if (label) label.textContent = 'Vị trí: ' + this.dataset.pos;
      });
    });
  }

  function setPositionPicker(pickerId, hiddenInputId, value) {
    const picker = document.getElementById(pickerId);
    const hiddenInput = document.getElementById(hiddenInputId);
    hiddenInput.value = value;
    picker.querySelectorAll('.pos-cell').forEach(c => {
      c.classList.toggle('active', c.dataset.pos === value);
    });
    var labelId = hiddenInputId.replace('object-position', 'pos-label');
    var label = document.getElementById(labelId);
    if (label) label.textContent = 'Vị trí: ' + value;
  }

  // ── Drag-to-reposition logic ──
  function initDragReposition(boxId, hiddenInputId, previewImgId, labelId) {
    var box = document.getElementById(boxId);
    var posInput = document.getElementById(hiddenInputId);
    var img = document.getElementById(previewImgId);
    var label = document.getElementById(labelId);
    var dragging = false;
    var startX, startY, startPosX, startPosY;

    function parsePosition(pos) {
      var parts = (pos || '50% 50%').replace(/center/g, '50%').split(/\s+/);
      var x = parseFloat(parts[0]);
      var y = parseFloat(parts[1]);
      if (isNaN(x)) x = 50;
      if (isNaN(y)) y = 50;
      return { x: x, y: y };
    }

    function onStart(e) {
      if (!img || img.style.display === 'none') return;
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
      var newX = Math.max(0, Math.min(100, startPosX - dx * 0.5));
      var newY = Math.max(0, Math.min(100, startPosY - dy * 0.5));
      var pos = Math.round(newX) + '% ' + Math.round(newY) + '%';
      posInput.value = pos;
      if (label) label.textContent = 'Vị trí: ' + pos;
      if (img) img.style.objectPosition = pos;
      // Also clear grid picker active state (user is using freeform drag)
      var pickerId = hiddenInputId.replace('object-position', 'position-picker');
      var picker = document.getElementById(pickerId);
      if (picker) picker.querySelectorAll('.pos-cell').forEach(c => c.classList.remove('active'));
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
  }

  // ── Add form: live preview ──
  const addUrlInput = document.querySelector('input[name="image_url"]');
  addUrlInput.addEventListener('input', updateAddPreview);

  function updateAddPreview() {
    const url = addUrlInput.value.trim();
    const img = document.getElementById('add-preview-img');
    const fit = document.getElementById('add-object-fit').value;
    const pos = document.getElementById('add-object-position').value;

    if (url) {
      let src = url;
      if (!url.startsWith('http://') && !url.startsWith('https://')) {
        src = '/gallery/' + url;
      }
      img.src = src;
      img.style.display = 'block';
    } else {
      img.style.display = 'none';
    }
    img.style.objectFit = fit;
    img.style.objectPosition = pos;
  }

  initPositionPicker('add-position-picker', 'add-object-position', 'add-preview-img');
  initDragReposition('add-preview-box', 'add-object-position', 'add-preview-img', 'add-pos-label');

  // ── Edit modal ──
  function openEditModal(id, imageUrl, altText, layout, sortOrder, objectFit, objectPosition) {
    document.getElementById('edit-form').action = '/admin/gallery/' + id;
    document.getElementById('edit-image-url').value = imageUrl;
    document.getElementById('edit-alt-text').value = altText;
    document.getElementById('edit-layout').value = layout;
    document.getElementById('edit-sort-order').value = sortOrder;
    document.getElementById('edit-object-fit').value = objectFit || 'cover';
    setPositionPicker('edit-position-picker', 'edit-object-position', objectPosition || 'center center');

    // Preview
    const img = document.getElementById('edit-preview-img');
    let src = imageUrl;
    if (!imageUrl.startsWith('http://') && !imageUrl.startsWith('https://')) {
      src = '/gallery/' + imageUrl;
    }
    img.src = src;
    img.style.objectFit = objectFit || 'cover';
    img.style.objectPosition = objectPosition || 'center center';
    img.style.display = 'block';

    var label = document.getElementById('edit-pos-label');
    if (label) label.textContent = 'Vị trí: ' + (objectPosition || 'center center');

    document.getElementById('edit-modal').classList.add('active');
  }

  function updateEditPreview() {
    const img = document.getElementById('edit-preview-img');
    img.style.objectFit = document.getElementById('edit-object-fit').value;
    img.style.objectPosition = document.getElementById('edit-object-position').value;
  }

  initPositionPicker('edit-position-picker', 'edit-object-position', 'edit-preview-img');
  initDragReposition('edit-preview-box', 'edit-object-position', 'edit-preview-img', 'edit-pos-label');

  function closeEditModal() {
    document.getElementById('edit-modal').classList.remove('active');
  }

  document.getElementById('edit-modal').addEventListener('click', function(e) {
    if (e.target === this) closeEditModal();
  });
</script>

</body>
</html>
