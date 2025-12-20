<?php
require __DIR__ . '/../app/core/bootstrap.php';

if (!empty($_SESSION['user_id'])) {
  if (($_SESSION['role'] ?? '') === 'admin') {
    header('Location: ../dashboard/admin/admin.php');
  } else {
    header('Location: ../dashboard/user/user.php');
  }
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'] ?? '';
    $pass  = $_POST['password'] ?? '';

    $pdo = db();
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($pass, $user['password_hash'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role']    = $user['role'];

        // ingat email kalau checkbox dicentang
        if (!empty($_POST['remember'])) {
            setcookie('remember_email', $email, time() + 30*24*60*60, '/');
        } else {
            setcookie('remember_email', '', time() - 3600, '/');
        }

        header('Location: ' . ($user['role'] === 'admin'
            ? '../dashboard/admin/admin.php'
            : '../dashboard/user/user.php'));
        exit;
    }

    $error = 'Email atau password salah.';

$emailValue      = $_POST['email'] ?? ($_COOKIE['remember_email'] ?? '');
$rememberChecked = isset($_COOKIE['remember_email']) ? 'checked' : '';
}
$emailValue = $_POST['email'] ?? ($_COOKIE['remember_email'] ?? '');
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Login | TriHealth</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <style>
    body {
      min-height: 100vh;
      background: linear-gradient(135deg, #38b000, #468903ff);
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
    }
    .auth-card {
      max-width: 420px;
      width: 100%;
      border: 0;
      border-radius: 18px;
      box-shadow: 0 18px 45px rgba(0,0,0,.15);
      overflow: hidden;
    }
    .auth-header {
      background: #fff;
      padding: 1.5rem 1.5rem 0.75rem;
      border-bottom: 0;
    }
    .auth-header h4 {
      margin: 0;
      font-weight: 500;
    }
    .auth-header p {
      margin-bottom: 0;
      color: #6c757d;
      font-size: .9rem;
    }
    .auth-body {
      padding: 1.5rem;
      background: #f8f9fa;
    }
    .brand-badge {
      width: 90px;
      height: 75px;
      border-radius: 14px;
      background: rgba(56,176,0,.1);
      display: flex;
      align-items: center;
      justify-content: center;
      color: #2b9348;
      margin-bottom: .75rem;
      font-size: 1.4rem;
    }
    .brand-badge img {
      width: 100%;
      height: 100%;
      object-fit: cover;
      border-radius: inherit;
      display: block;
    } 
    .form-control:focus {
      border-color: #2b9348;
      box-shadow: 0 0 0 .15rem rgba(43,147,72,.25);
    }
    .btn-success {
      background-color: #2b9348;
      border-color: #2b9348;
      font-weight: 600;
    }
    .btn-success:hover {
      background-color: #23863f;
      border-color: #23863f;
    }
    .auth-footer {
      font-size: .85rem;
      color: #6c757d;
    }
  </style>
</head>
<body>

<div class="card auth-card">
  <div class="auth-header text-center">
    <div class="brand-badge mx-auto">
      <img src="../public/img/logo.jpg" alt="TriHealth logo">
    </div>
    <h4>Masuk ke TriHealth</h4>
    <p>Catat dan kelola pola makanmu setiap hari.</p>
  </div>
  <div class="auth-body">
    <?php if (!empty($error)): ?>
      <div class="alert alert-danger py-2 small mb-3"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="post" novalidate>
      <div class="mb-3">
        <label class="form-label small">Email</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-envelope"></i></span>
          <input
            type="email"
            name="email"
            class="form-control"
            value="<?= htmlspecialchars($emailValue) ?>"
            required
            autofocus
          >
        </div>
      </div>
      <div class="mb-3">
        <label class="form-label small">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock"></i></span>
          <input type="password" name="password" class="form-control" required minlength="6">
        </div>
      </div>
      <div class="d-flex justify-content-between align-items-center mb-3">
        <div class="form-check small">
          <input class="form-check-input" type="checkbox" id="remember" name="remember">
          <label class="form-check-label" for="remember">
            Ingat saya
          </label>
        </div>
        <a href="forgot_password.php" class="small text-decoration-none text-success-75">Lupa password?</a>
      </div>
      <button class="btn btn-success w-100 py-2">Masuk</button>
    </form>

    <p class="auth-footer text-center mt-3 mb-0">
      Belum punya akun?
      <a href="register.php" class="text-success text-decoration-none fw-semibold">Daftar sekarang</a>
    </p>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

