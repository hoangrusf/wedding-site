<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin - Tạo link thiệp</title>
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

    /* Container */
    .container {
      max-width: 700px;
      margin: 2rem auto;
      padding: 0 1rem;
    }

    .card {
      background: #fff;
      border-radius: 12px;
      padding: 2rem;
      box-shadow: 0 4px 16px rgba(0,0,0,0.08);
    }

    .card h2 {
      font-size: 1.4rem;
      margin-bottom: 1.5rem;
      color: #5a3e2b;
      text-align: center;
    }

    .form-group {
      margin-bottom: 1.5rem;
    }

    .form-group label {
      display: block;
      font-size: 0.9rem;
      font-weight: 600;
      margin-bottom: 0.5rem;
      color: #5a3e2b;
    }

    .form-group input[type="text"] {
      width: 100%;
      padding: 0.75rem;
      border: 2px solid #e0d5c7;
      border-radius: 8px;
      font-size: 1rem;
      transition: border-color 0.2s;
    }

    .form-group input[type="text"]:focus {
      outline: none;
      border-color: #b48c64;
    }

    .radio-group {
      display: flex;
      gap: 1.5rem;
      flex-wrap: wrap;
    }

    .radio-option {
      display: flex;
      align-items: center;
      gap: 0.5rem;
      cursor: pointer;
    }

    .radio-option input[type="radio"] {
      width: 18px;
      height: 18px;
      cursor: pointer;
    }

    .radio-option label {
      margin: 0;
      font-weight: 500;
      cursor: pointer;
    }

    .btn-generate {
      width: 100%;
      padding: 0.9rem;
      background: linear-gradient(135deg, #5a3e2b, #7a5c3e);
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: transform 0.2s;
    }

    .btn-generate:hover {
      transform: translateY(-2px);
    }

    .btn-generate:active {
      transform: translateY(0);
    }

    /* Result section */
    .result-section {
      margin-top: 2rem;
      padding-top: 2rem;
      border-top: 2px dashed #e0d5c7;
      display: none;
    }

    .result-section.show {
      display: block;
    }

    .result-section h3 {
      font-size: 1.1rem;
      margin-bottom: 1rem;
      color: #5a3e2b;
    }

    .link-display {
      display: flex;
      gap: 0.5rem;
      align-items: stretch;
    }

    .link-display input {
      flex: 1;
      padding: 0.75rem;
      border: 2px solid #b48c64;
      border-radius: 8px;
      font-size: 0.9rem;
      background: #faf8f5;
      color: #3a2c1e;
    }

    .btn-copy {
      padding: 0.75rem 1.5rem;
      background: #2e7d32;
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 0.9rem;
      font-weight: 600;
      cursor: pointer;
      white-space: nowrap;
      transition: background 0.2s;
    }

    .btn-copy:hover {
      background: #1b5e20;
    }

    .btn-copy.copied {
      background: #1976d2;
    }

    .helper-text {
      margin-top: 0.5rem;
      font-size: 0.8rem;
      color: #9a8070;
    }

    .note {
      margin-top: 1.5rem;
      padding: 1rem;
      background: #fff9e6;
      border-left: 4px solid #ffc107;
      border-radius: 4px;
      font-size: 0.85rem;
      line-height: 1.5;
    }
  </style>
</head>
<body>

<header class="admin-header">
  <div>
    <h1>🔗 Tạo link thiệp cưới</h1>
    <p>Tạo link cá nhân hóa cho khách mời</p>
  </div>
  <div class="header-actions">
    <a href="{{ route('admin.rsvp') }}" class="btn-nav">📋 Danh sách RSVP</a>
    <a href="{{ route('admin.config') }}" class="btn-nav">⚙️ Cấu hình</a>
    <a href="{{ route('admin.gallery') }}" class="btn-nav">🖼️ Album ảnh</a>
    <form method="POST" action="{{ route('admin.logout') }}">
      @csrf
      <button type="submit" class="btn-nav">Đăng xuất</button>
    </form>
  </div>
</header>

<div class="container">
  <div class="card">
    <h2>📝 Tạo link thiệp mời</h2>

    <form id="linkForm">
      <div class="form-group">
        <label>Chọn loại thiệp</label>
        <div class="radio-group">
          <div class="radio-option">
            <input type="radio" id="type-groom" name="type" value="1" checked>
            <label for="type-groom">🤵 Nhà trai</label>
          </div>
          <div class="radio-option">
            <input type="radio" id="type-bride" name="type" value="2">
            <label for="type-bride">👰 Nhà gái</label>
          </div>
        </div>
      </div>

      <div class="form-group">
        <label for="guestName">Tên khách mời</label>
        <input 
          type="text" 
          id="guestName" 
          name="guestName" 
          placeholder="Ví dụ: Bạn Hoàng, Anh Minh, Chị Lan..." 
          required
        />
        <div class="helper-text">
          💡 Có thể dùng dấu cách, hệ thống sẽ tự động chuyển sang dấu gạch dưới (_) khi tạo link
        </div>
      </div>

      <button type="submit" class="btn-generate">✨ Tạo link</button>
    </form>

    <div class="result-section" id="resultSection">
      <h3>🎉 Link đã tạo thành công!</h3>
      
      <div class="link-display">
        <input type="text" id="generatedLink" readonly />
        <button type="button" class="btn-copy" id="btnCopy">📋 Copy</button>
      </div>

      <div class="note">
        <strong>📌 Lưu ý:</strong><br>
        • Link này đã được mã hóa để có thể chia sẻ trên Facebook không bị cắt<br>
        • Khi khách mở link, tên sẽ hiển thị đầy đủ với dấu cách<br>
        • Có thể tạo nhiều link cho nhiều khách mời khác nhau
      </div>
    </div>
  </div>
</div>

<script>
  const baseUrl = "{{ $baseUrl }}";
  const form = document.getElementById('linkForm');
  const resultSection = document.getElementById('resultSection');
  const generatedLink = document.getElementById('generatedLink');
  const btnCopy = document.getElementById('btnCopy');
  const guestNameInput = document.getElementById('guestName');

  form.addEventListener('submit', function(e) {
    e.preventDefault();
    
    const guestName = guestNameInput.value.trim();
    if (!guestName) {
      alert('Vui lòng nhập tên khách mời');
      return;
    }

    // Chuyển dấu cách thành dấu gạch dưới
    const encodedName = guestName.replace(/\s+/g, '_');
    
    // Lấy loại thiệp
    const type = document.querySelector('input[name="type"]:checked').value;
    
    // Tạo link
    const link = `${baseUrl}?invite=${encodedName}&type=${type}`;
    
    // Hiển thị kết quả
    generatedLink.value = link;
    resultSection.classList.add('show');
    
    // Reset nút copy
    btnCopy.textContent = '📋 Copy';
    btnCopy.classList.remove('copied');
  });

  btnCopy.addEventListener('click', function() {
    generatedLink.select();
    document.execCommand('copy');
    
    // Thử cách mới hơn (nếu browser hỗ trợ)
    if (navigator.clipboard) {
      navigator.clipboard.writeText(generatedLink.value);
    }
    
    // Hiệu ứng đã copy
    btnCopy.textContent = '✓ Đã copy!';
    btnCopy.classList.add('copied');
    
    setTimeout(() => {
      btnCopy.textContent = '📋 Copy';
      btnCopy.classList.remove('copied');
    }, 2000);
  });
</script>

</body>
</html>
