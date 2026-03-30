<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Admin - Đăng nhập</title>
  <style>
    *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

    body {
      min-height: 100vh;
      display: flex;
      align-items: center;
      justify-content: center;
      background: linear-gradient(135deg, #f5e6d3 0%, #e8d5b7 50%, #d4b896 100%);
      font-family: 'Segoe UI', sans-serif;
    }

    .login-card {
      background: #fff;
      border-radius: 16px;
      box-shadow: 0 8px 40px rgba(0,0,0,0.12);
      padding: 2.5rem 2rem;
      width: 100%;
      max-width: 380px;
      text-align: center;
    }

    .login-icon {
      font-size: 2.5rem;
      margin-bottom: 0.5rem;
    }

    h1 {
      font-size: 1.4rem;
      color: #5a3e2b;
      margin-bottom: 0.3rem;
    }

    .login-subtitle {
      font-size: 0.85rem;
      color: #9a8070;
      margin-bottom: 1.8rem;
    }

    .form-group {
      text-align: left;
      margin-bottom: 1.2rem;
    }

    label {
      display: block;
      font-size: 0.85rem;
      font-weight: 600;
      color: #5a3e2b;
      margin-bottom: 0.4rem;
    }

    input[type="password"] {
      width: 100%;
      padding: 0.7rem 1rem;
      border: 1.5px solid #ddd;
      border-radius: 8px;
      font-size: 1rem;
      outline: none;
      transition: border-color 0.2s;
    }

    input[type="password"]:focus {
      border-color: #b48c64;
    }

    .error-msg {
      color: #d32f2f;
      font-size: 0.82rem;
      margin-top: 0.4rem;
    }

    .btn-login {
      width: 100%;
      padding: 0.8rem;
      background: linear-gradient(135deg, #b48c64, #9a7450);
      color: #fff;
      border: none;
      border-radius: 8px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: opacity 0.2s;
      margin-top: 0.5rem;
    }

    .btn-login:hover { opacity: 0.88; }
  </style>
</head>
<body>
  <div class="login-card">
    <div class="login-icon">🔐</div>
    <h1>Trang Quản Trị</h1>
    <p class="login-subtitle">Xác nhận danh sách tham dự</p>

    <form method="POST" action="<?php echo e(route('admin.login')); ?>">
      <?php echo csrf_field(); ?>
      <div class="form-group">
        <label for="password">Mật khẩu</label>
        <input
          type="password"
          id="password"
          name="password"
          placeholder="Nhập mật khẩu..."
          autofocus
          required
        />
        <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
          <p class="error-msg"><?php echo e($message); ?></p>
        <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
      </div>
      <button type="submit" class="btn-login">Đăng nhập</button>
    </form>
  </div>
</body>
</html>
<?php /**PATH C:\xampp\htdocs\InviteMarried\resources\views/admin/login.blade.php ENDPATH**/ ?>