<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin - Danh sách xác nhận</title>
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

    .admin-header h1 {
      font-size: 1.2rem;
      font-weight: 700;
      letter-spacing: 0.03em;
    }

    .admin-header p {
      font-size: 0.8rem;
      opacity: 0.75;
      margin-top: 0.15rem;
    }

    .btn-logout {
      padding: 0.4rem 1rem;
      background: rgba(255,255,255,0.15);
      color: #fff;
      border: 1px solid rgba(255,255,255,0.3);
      border-radius: 6px;
      font-size: 0.82rem;
      cursor: pointer;
      transition: background 0.2s;
      white-space: nowrap;
    }

    .btn-logout:hover { background: rgba(255,255,255,0.25); }

    /* Stats */
    .stats-bar {
      display: flex;
      gap: 1rem;
      padding: 1.2rem 1.5rem;
      flex-wrap: wrap;
    }

    .stat-card {
      flex: 1;
      min-width: 140px;
      background: #fff;
      border-radius: 10px;
      padding: 1rem 1.2rem;
      text-align: center;
      box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }

    .stat-card .stat-number {
      font-size: 2rem;
      font-weight: 700;
      line-height: 1;
    }

    .stat-card .stat-label {
      font-size: 0.78rem;
      color: #9a8070;
      margin-top: 0.3rem;
    }

    .stat-card.attending  .stat-number { color: #2e7d32; }
    .stat-card.not-attending .stat-number { color: #c62828; }
    .stat-card.total .stat-number { color: #b48c64; }

    /* Filter bar */
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

    /* Table */
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
      min-width: 600px;
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

    .badge-attending    { background: #e8f5e9; color: #2e7d32; }
    .badge-not-attending { background: #ffebee; color: #c62828; }
    .badge-groom { background: #e3f0fb; color: #1565c0; }
    .badge-bride { background: #fce4ec; color: #880e4f; }
    .badge-common { background: #f3e5f5; color: #6a1b9a; }

    .wish-text {
      font-style: italic;
      color: #7a6552;
      font-size: 0.83rem;
      max-width: 220px;
    }

    .td-name { font-weight: 600; }
    .td-phone { color: #5a3e2b; font-size: 0.85rem; }
    .td-time { color: #aaa; font-size: 0.78rem; white-space: nowrap; }

    .empty-row td {
      text-align: center;
      padding: 2.5rem;
      color: #bbb;
      font-style: italic;
    }

    .btn-del-wish {
      display: inline-flex;
      align-items: center;
      justify-content: center;
      width: 20px;
      height: 20px;
      border-radius: 50%;
      border: none;
      background: #ffcdd2;
      color: #c62828;
      font-size: 0.7rem;
      cursor: pointer;
      vertical-align: middle;
      line-height: 1;
      transition: background 0.2s;
    }

    .btn-del-wish:hover { background: #ef9a9a; }

    /* Print */
    @media print {
      .admin-header form, .filter-bar, .btn-logout { display: none; }
      body { background: #fff; }
    }

    @media (max-width: 480px) {
      .admin-header { padding: 0.8rem 1rem; }
      .stats-bar { padding: 0.8rem 1rem; }
      .filter-bar { padding: 0 1rem 0.8rem; }
      .table-wrapper { padding: 0 0.5rem 2rem; }
    }
  </style>
</head>
<body>

<header class="admin-header">
  <div>
    <h1>📋 Danh sách xác nhận tham dự</h1>
    <p>Cập nhật đến <?php echo e(now()->format('H:i - d/m/Y')); ?></p>
  </div>
  <div class="header-actions" style="display:flex;gap:0.6rem;align-items:center;flex-wrap:wrap;">
    <a href="<?php echo e(route('admin.config')); ?>" style="padding:0.4rem 1rem;background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);border-radius:6px;font-size:0.82rem;text-decoration:none;">⚙️ Cấu hình thiệp</a>
    <a href="<?php echo e(route('admin.gallery')); ?>" style="padding:0.4rem 1rem;background:rgba(255,255,255,0.15);color:#fff;border:1px solid rgba(255,255,255,0.3);border-radius:6px;font-size:0.82rem;text-decoration:none;">🖼️ Album ảnh</a>
    <form method="POST" action="<?php echo e(route('admin.rsvp.deleteAll')); ?>" id="form-delete-all" onsubmit="return confirmDeleteAll()">
      <?php echo csrf_field(); ?>
      <?php echo method_field('DELETE'); ?>
      <button type="submit" style="padding:0.4rem 1rem;background:rgba(220,53,69,0.75);color:#fff;border:1px solid rgba(220,53,69,0.5);border-radius:6px;font-size:0.82rem;cursor:pointer;white-space:nowrap;">🗑️ Xóa tất cả</button>
    </form>
    <form method="POST" action="<?php echo e(route('admin.logout')); ?>">
      <?php echo csrf_field(); ?>
      <button type="submit" class="btn-logout">Đăng xuất</button>
    </form>
  </div>
</header>

<?php if(session('success')): ?>
<div style="margin:1rem 1.5rem 0;padding:0.75rem 1.1rem;border-radius:8px;background:#e8f5e9;color:#2e7d32;border:1px solid #c8e6c9;font-size:0.88rem;">✓ <?php echo e(session('success')); ?></div>
<?php endif; ?>

<!-- Thống kê -->
<div class="stats-bar">
  <div class="stat-card total">
    <div class="stat-number"><?php echo e($rsvps->count()); ?></div>
    <div class="stat-label">Tổng phản hồi</div>
  </div>
  <div class="stat-card attending">
    <div class="stat-number"><?php echo e($totalAttending); ?></div>
    <div class="stat-label">Người sẽ tham dự (gồm đi cùng)</div>
  </div>
  <div class="stat-card not-attending">
    <div class="stat-number"><?php echo e($totalNotAttending); ?></div>
    <div class="stat-label">Không thể tham dự</div>
  </div>
</div>

<!-- Bộ lọc -->
<div class="filter-bar">
  <button class="filter-btn active" onclick="filterTable('all', this)">Tất cả</button>
  <button class="filter-btn" onclick="filterTable('attending', this)">✓ Tham dự</button>
  <button class="filter-btn" onclick="filterTable('not-attending', this)">✗ Không tham dự</button>
</div>

<!-- Bảng -->
<div class="table-wrapper">
  <table id="rsvp-table">
    <thead>
      <tr>
        <th>#</th>
        <th>Họ tên</th>
        <th>Số điện thoại</th>
        <th>Xác nhận</th>
        <th>Đi cùng</th>
        <th>Lời chúc</th>
        <th>Thời gian</th>
      </tr>
    </thead>
    <tbody>
      <?php $__empty_1 = true; $__currentLoopData = $rsvps; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $rsvp): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
      <tr data-status="<?php echo e($rsvp->is_attending ? 'attending' : 'not-attending'); ?>">
        <td><?php echo e($index + 1); ?></td>
        <td class="td-name"><?php echo e($rsvp->guest_name); ?></td>
        <td class="td-phone"><?php echo e($rsvp->phone_number ?? '—'); ?></td>
        <td>
          <?php if($rsvp->is_attending): ?>
            <span class="badge badge-attending">✓ Tham dự</span>
          <?php else: ?>
            <span class="badge badge-not-attending">✗ Không tham dự</span>
          <?php endif; ?>
        </td>
        <td>
          <?php if($rsvp->is_attending): ?>
            <?php echo e($rsvp->companion_count > 0 ? '+ ' . $rsvp->companion_count . ' người' : 'Chỉ mình'); ?>

          <?php else: ?>
            —
          <?php endif; ?>
        </td>
        <td>
          <?php if($rsvp->wishes_message): ?>
            <span class="wish-text">"<?php echo e(Str::limit($rsvp->wishes_message, 80)); ?>"</span>
            <form method="POST" action="<?php echo e(route('admin.wish.delete', $rsvp)); ?>" style="display:inline; margin-left:0.4rem;" onsubmit="return confirm('Xóa lời chúc này?')">
              <?php echo csrf_field(); ?>
              <?php echo method_field('DELETE'); ?>
              <button type="submit" class="btn-del-wish" title="Xóa lời chúc">✕</button>
            </form>
          <?php else: ?>
            <span style="color:#ccc">—</span>
          <?php endif; ?>
        </td>
        <td class="td-time"><?php echo e($rsvp->created_at->format('H:i d/m')); ?></td>
      </tr>
      <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
      <tr class="empty-row">
        <td colspan="7">Chưa có xác nhận nào.</td>
      </tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<script>
  function filterTable(status, btn) {
    document.querySelectorAll('.filter-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    document.querySelectorAll('#rsvp-table tbody tr:not(.empty-row)').forEach(row => {
      if (status === 'all' || row.dataset.status === status) {
        row.style.display = '';
      } else {
        row.style.display = 'none';
      }
    });
  }

  function confirmDeleteAll() {
    return confirm('Bạn có chắc muốn XÓA TOÀN BỘ danh sách xác nhận?\nHành động này không thể hoàn tác!');
  }
</script>

</body>
</html>
<?php /**PATH C:\Users\thispc\Documents\wedding-site\resources\views/admin/rsvp.blade.php ENDPATH**/ ?>