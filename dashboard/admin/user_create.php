<?php
require __DIR__ . '/../../app/core/bootstrap.php';
require_once __DIR__ . '/../../app/core/helpers.php';
auth_required();
role_admin_only();

$pdo    = db();
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name      = trim($_POST['name'] ?? '');
    $email     = trim($_POST['email'] ?? '');
    $password  = $_POST['password'] ?? '';
    $password2 = $_POST['password_confirmation'] ?? '';
    $role      = $_POST['role'] ?? 'user';

    if ($name === '' || $email === '' || $password === '') {
        $errors[] = 'Nama, email, dan password wajib diisi.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }
    if ($password !== $password2) {
        $errors[] = 'Konfirmasi password tidak sama.';
    }

    // cek email unik
    if (!$errors) {
        $check = $pdo->prepare("SELECT id FROM users WHERE email=?");
        $check->execute([$email]);
        if ($check->fetch()) {
            $errors[] = 'Email sudah terdaftar.';
        }
    }

    if (!$errors) {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("
            INSERT INTO users (name,email,password_hash,role)
            VALUES (?,?,?,?)
        ");
        $stmt->execute([$name, $email, $hash, $role]);

        header('Location: users.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Tambah User | TriHealth Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<?php $active = 'users'; include __DIR__ . '/_navbar.php'; ?>

<div class="container py-4">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
      <h3 class="mb-1">Tambah User</h3>
      <p class="text-muted small mb-0">
        Buat akun baru untuk admin atau user aplikasi rekomendasi makanan sehat.
      </p>
    </div>
    <a href="users.php" class="btn btn-outline-secondary btn-sm mt-2 mt-md-0">
      <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
  </div>

  <?php if ($errors): ?>
    <div class="alert alert-danger py-2 small">
      <ul class="mb-0 ps-3">
        <?php foreach ($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <div class="card shadow-sm border-0">
    <div class="card-body">
      <form method="post" class="small" novalidate>
        <div class="row g-3">
          <div class="col-md-6">
            <label class="form-label small">Nama lengkap</label>
            <div class="input-group input-group-sm">
              <span class="input-group-text"><i class="bi bi-person"></i></span>
              <input type="text" name="name" class="form-control"
                     value="<?= htmlspecialchars($name ?? '') ?>" required>
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label small">Email</label>
            <div class="input-group input-group-sm">
              <span class="input-group-text"><i class="bi bi-envelope"></i></span>
              <input type="email" name="email" class="form-control"
                     value="<?= htmlspecialchars($email ?? '') ?>" required>
            </div>
          </div>

          <div class="col-md-4">
            <label class="form-label small">Role</label>
            <select name="role" class="form-select form-select-sm">
              <option value="user" <?= ($role ?? '') === 'admin' ? '' : 'selected' ?>>User</option>
              <option value="admin" <?= ($role ?? '') === 'admin' ? 'selected' : '' ?>>Admin</option>
            </select>
          </div>

          <div class="col-md-4">
            <label class="form-label small">Password</label>
            <div class="input-group input-group-sm">
              <span class="input-group-text"><i class="bi bi-lock"></i></span>
              <input type="password" name="password" class="form-control" required>
            </div>
          </div>

          <div class="col-md-4">
            <label class="form-label small">Konfirmasi password</label>
            <div class="input-group input-group-sm">
              <span class="input-group-text"><i class="bi bi-shield-lock"></i></span>
              <input type="password" name="password_confirmation" class="form-control" required>
            </div>
          </div>
        </div>

        <div class="mt-3">
          <button class="btn btn-success btn-sm">
            <i class="bi bi-check-circle me-1"></i> Simpan
          </button>
          <a href="users.php" class="btn btn-outline-secondary btn-sm ms-2">
            Batal
          </a>
        </div>
      </form>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

