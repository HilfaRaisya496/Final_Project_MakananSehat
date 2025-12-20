<?php
require __DIR__ . '/../app/core/bootstrap.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name      = trim($_POST['name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $password2 = $_POST['password_confirmation'] ?? '';

    if ($name === '' || $email === '' || $password === '') {
        $errors[] = 'Semua field wajib diisi.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }
    if ($password !== $password2) {
        $errors[] = 'Konfirmasi password tidak sama.';
    }
    if (strlen($password) < 6) {
      $errors[] = 'Password minimal 6 karakter.';
    }

    if (!$errors) {
        $pdo = db();
        // cek email sudah dipakai
        $check = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $check->execute([$email]);
        if ($check->fetch()) {
          $errors[] = 'Email sudah terdaftar.';
        } else {
          $checkName = $pdo->prepare("SELECT id FROM users WHERE name = ?");
          $checkName->execute([$name]);
          if ($checkName->fetch()) {
            $errors[] = 'Nama pengguna sudah terpakai.';
          } else {
            $statuss = !empty($_POST['allow_notification']) ? 'active' : 'inactive';
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare(
              "INSERT INTO users (name, email, password_hash, role, statuss) VALUES (?, ?, ?, 'user', ?)"
            );
            try {
              $stmt->execute([$name, $email, $hash, $statuss]);
              header('Location: register_success.php');
              exit;
            } catch (PDOException $ex) {
              if ($ex->getCode() === '23000') {
                $msg = $ex->getMessage();
                if (stripos($msg, 'email') !== false) {
                  $errors[] = 'Email sudah terdaftar.';
                } else {
                  $errors[] = 'Nama pengguna sudah terpakai.';
                }
              } else {
                $errors[] = 'Terjadi kesalahan saat membuat akun. Silakan coba lagi.';
              }
            }
          }
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Register | TriHealth</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <style>
    body {
      min-height: 100vh;
      background: linear-gradient(135deg, #1b4332, #2d6a4f);
      display: flex;
      align-items: center;
      justify-content: center;
      font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
    }
    .auth-card {
      max-width: 460px;
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
      width: 80px;
      height: 90px;
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
    <h4>Buat Akun TriHealth</h4>
    <p>Mulai pantau asupan kalori dan nutrisi harianmu.</p>
  </div>
  <div class="auth-body">

    <?php if ($errors): ?>
      <div class="alert alert-danger py-2 small mb-3">
        <ul class="mb-0 ps-3">
          <?php foreach ($errors as $e): ?>
            <li><?= htmlspecialchars($e) ?></li>
          <?php endforeach; ?>
        </ul>
      </div>
    <?php endif; ?>

    <form method="post" novalidate>
      <div class="mb-3">
        <label class="form-label small">Nama lengkap</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-person"></i></span>
          <input type="text" name="name" class="form-control"
                 value="<?= htmlspecialchars($name ?? '') ?>" required>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label small">Email</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-envelope"></i></span>
          <input type="email" name="email" class="form-control"
                 value="<?= htmlspecialchars($email ?? '') ?>" required>
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label small">Password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-lock"></i></span>
          <input type="password" name="password" class="form-control" required minlength="6">
        </div>
      </div>

      <div class="mb-3">
        <label class="form-label small">Konfirmasi password</label>
        <div class="input-group">
          <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
          <input type="password" name="password_confirmation" class="form-control" required minlength="6">
        </div>
      </div>

      <div class="mb-3 form-check">
        <input type="checkbox" name="allow_notification" value="1" class="form-check-input" id="notifCheck"
              <?= !empty($_POST['allow_notification']) ? 'checked' : '' ?>>
        <label class="form-check-label small" for="notifCheck">
          Izinkan notifikasi email (menu harian, dsb)
        </label>
      </div>

      <button class="btn btn-success w-100 py-2">Daftar</button>
    </form>

    <p class="auth-footer text-center mt-3 mb-0">
      Sudah punya akun?
      <a href="login.php" class="text-success text-decoration-none fw-semibold">Masuk di sini</a>
    </p>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>