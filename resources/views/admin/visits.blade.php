<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin - Lượt xem thiệp</title>
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

    .admin-header h1 { font-size: 1.2rem; font-weight: 700; letter-spacing: 0.03em; }
    .admin-header p  { font-size: 0.8rem; opacity: 0.75; margin-top: 0.15rem; }

    .btn-back {
      padding: 0.4rem 1rem;
      background: rgba(255,255,255,0.15);
      color: #fff;
      border: 1px solid rgba(255,255,255,0.3);
      border-radius: 6px;
      font-size: 0.82rem;
      text-decoration: none;
      white-space: nowrap;
    }

    .stats-bar {
      display: flex;
      gap: 1rem;
      padding: 1.2rem 1.5rem;
      flex-wrap: wrap;
    }

    .stat-card {
      flex: 1;
      min-width: 130px;
      background: #fff;
      border-radius: 10px;
      padding: 1rem 1.2rem;
      text-align: center;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }

    .stat-card .stat-number { font-size: 2rem; font-weight: 700; line-height: 1; color: #b48c64; }
    .stat-card .stat-label  { font-size: 0.78rem; color: #9a8070; margin-top: 0.3rem; }
    .stat-card.groom .stat-number { color: #1565c0; }
    .stat-card.bride .stat-number { color: #880e4f; }

    .filter-bar {
      padding: 0 1.5rem 1rem;
      display: flex;
      gap: 0.5rem;
      flex-wrap: wrap;
      align-items: center;
    }

    .filter-btn {
      padding: 0.35rem 0.9rem;
      border-radius: 20px;
      border: 1.5px solid #ddd;
      background: #fff;
      font-size: 0.82rem;
      cursor: pointer;
      transition: all 0.2s;
      color: #5a3e2b;
    }

    .filter-btn.active,
    .filter-btn:hover {
      background: #b48c64;
      border-color: #b48c64;
      color: #fff;
    }

    .table-wrapper {
      padding: 0 1.5rem 2rem;
      overflow-x: auto;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      background: #fff;
      border-radius: 12px;
      overflow: hidden;
      box-shadow: 0 2px 12px rgba(0,0,0,0.07);
      min-width: 500px;
    }

    thead {
      background: #5a3e2b;
      color: #fff;
    }

    thead th {
      padding: 0.8rem 1rem;
      text-align: left;
      font-size: 0.82rem;
      font-weight: 600;
      letter-spacing: 0.04em;
      white-space: nowrap;
    }

    tbody tr {
      border-bottom: 1px solid #f0e8df;
      transition: background 0.15s;
    }

    tbody tr:hover { background: #fdf8f3; }
    tbody tr:last-child { border-bottom: none; }

    tbody td {
      padding: 0.75rem 1rem;
      font-size: 0.88rem;
      vertical-align: top;
    }

    .badge {
      display: inline-block;
      padding: 0.2rem 0.65rem;
      border-radius: 12px;
      font-size: 0.75rem;
      font-weight: 600;
      white-space: nowrap;
    }

    .badge-groom  { background: #e3f0fb; color: #1565c0; }
    .badge-bride  { background: #fce4ec; color: #880e4f; }

    .td-name { font-weight: 600; }
    .td-time { color: #aaa; font-size: 0.78rem; white-space: nowrap; }

    .empty-row td {
      text-align: center;
      padding: 2.5rem;
      color: #bbb;
      font-style: italic;
    }

    @media (max-width: 480px) {
      .admin-header { padding: 0.8rem 1rem; }
      .stats-bar    { padding: 0.8rem 1rem; }
      .filter-bar   { padding: 0 1rem 0.8rem; }
      .table-wrapper{ padding: 0 0.5rem 2rem; }
    }
  </style>
</head>
<body>

<header class="admin-header">
  <div>
    <h1>👁️ Danh sách lượt xem thiệp</h1>
    <p>Cập nhật đến {{ now()->format('H:i - d/m/Y') }}</p>
  </div>
  <div style="display:flex;gap:0.6rem;align-items:center;flex-wrap:wrap;">
    <a href="{{ route('admin.rsvp') }}" class="btn-back">📋 Xác nhận tham dự</a>
    <a href="{{ route('admin.link') }}" class="btn-back">🔗 Tạo link thiệp</a>
    <a href="{{ route('admin.config') }}" class="btn-back">⚙️ Cấu hình thiệp</a>
    <a href="{{ route('admin.gallery') }}" class="btn-back">🖼️ Album ảnh</a>
    <form method="POST" action="{{ route('admin.visits.deleteAll') }}" onsubmit="return confirm('Xóa toàn bộ lịch sử truy cập?')">
      @csrf
      @method('DELETE')
      <button type="submit" class="btn-back" style="cursor:pointer;background:rgba(220,53,69,0.75);border-color:rgba(220,53,69,0.5);">🗑️ Xóa tất cả</button>
    </form>
    <form method="POST" action="{{ route('admin.logout') }}">
      @csrf
      <button type="submit" class="btn-back" style="cursor:pointer;border:1px solid rgba(255,255,255,0.3);">Đăng xuất</button>
    </form>
  </div>
</header>

<!-- Thống kê -->
<div class="stats-bar">
  <div class="stat-card">
    <div class="stat-number">{{ $visits->count() }}</div>
    <div class="stat-label">Tổng lượt xem</div>
  </div>
  <div class="stat-card groom">
    <div class="stat-number">{{ $visits->where('type', 1)->count() }}</div>
    <div class="stat-label">Nhà Trai</div>
  </div>
  <div class="stat-card bride">
    <div class="stat-number">{{ $visits->where('type', 2)->count() }}</div>
    <div class="stat-label">Nhà Gái</div>
  </div>
  <div class="stat-card">
    <div class="stat-number">{{ $visits->whereNotNull('invite_name')->count() }}</div>
    <div class="stat-label">Có tên mời</div>
  </div>
</div>

<!-- Bộ lọc -->
<div class="filter-bar">
  <button class="filter-btn active" onclick="filterTable('all', this)">Tất cả</button>
  <button class="filter-btn" onclick="filterTable('groom', this)">🔵 Nhà Trai</button>
  <button class="filter-btn" onclick="filterTable('bride', this)">🧝 Nhà Gái</button>
  <button class="filter-btn" onclick="filterTable('named', this)">🏷️ Có tên mời</button>
</div>

<!-- Bảng -->
<div class="table-wrapper">
  <table id="visits-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Tên</th>
        <th>Nhà trai / Nhà gái</th>
        <th>Thời gian</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      @forelse($visits as $index => $visit)
      <tr data-type="{{ $visit->type == 2 ? 'bride' : 'groom' }}" data-named="{{ $visit->invite_name ? 'yes' : 'no' }}">
        <td>{{ $index + 1 }}</td>
        <td class="td-name">{{ $visit->invite_name ?? 'Khách mời' }}</td>
        <td>
          @if($visit->type == 2)
            <span class="badge badge-bride">Nhà Gái</span>
          @else
            <span class="badge badge-groom">Nhà Trai</span>
          @endif
        </td>
        <td class="td-time">{{ $visit->created_at->format('H:i d/m/Y') }}</td>
        <td>
          <form method="POST" action="{{ route('admin.visit.delete', $visit) }}" onsubmit="return confirm('Xóa lượt xem này?')" style="display:inline;">
            @csrf
            @method('DELETE')
            <button type="submit" style="padding:0.2rem 0.5rem;background:#ffcdd2;color:#c62828;border:none;border-radius:6px;font-size:0.75rem;cursor:pointer;" title="Xóa">✕</button>
          </form>
        </td>
      </tr>
      @empty
      <tr class="empty-row">
        <td colspan="5">Chưa có lượt xem nào.</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>

<script>
  function filterTable(filter, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    document.querySelectorAll('#visits-table tbody tr:not(.empty-row)').forEach(row => {
      const show =
        filter === 'all'   ||
        (filter === 'groom' && row.dataset.type === 'groom') ||
        (filter === 'bride' && row.dataset.type === 'bride') ||
        (filter === 'named' && row.dataset.named === 'yes');
      row.style.display = show ? '' : 'none';
    });
  }
</script>

</body>
</html>
