<?php
require __DIR__ . '/../../app/core/bootstrap.php';
require_once __DIR__ . '/../../app/core/helpers.php';
auth_required();
role_admin_only();

$pdo = db();
$id  = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT id,name,email,role FROM users WHERE id=?");
$stmt->execute([$id]);
$user = $stmt->fetch();

if (!$user) {
    http_response_code(404);
    echo "User tidak ditemukan";
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $role  = $_POST['role'] ?? 'user';

    if ($name === '' || $email === '') {
        $errors[] = 'Nama dan email wajib diisi.';
    }
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'Format email tidak valid.';
    }

    // optional: ganti password
    $password = $_POST['password'] ?? '';
    $updatePassSql = '';
    $params = [$name, $email, $role, $id];

    if ($password !== '') {
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $updatePassSql = ", password_hash = ?";
        $params = [$name, $email, $role, $hash, $id];
    }

    if (!$errors) {
        $sql = "UPDATE users SET name=?, email=?, role=? {$updatePassSql} WHERE id=?";
        $st  = $pdo->prepare($sql);
        $st->execute($params);

        header('Location: users.php');
        exit;
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Edit User | TriHealth Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<?php $active = 'users'; include __DIR__ . '/_navbar.php'; ?>

<div class="container py-4">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
      <h3 class="mb-1">Edit User</h3>
      <p class="text-muted small mb-0">
        Perbarui informasi akun dan role user aplikasi rekomendasi makanan sehat.
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
                     value="<?= htmlspecialchars($user['name']) ?>" required>
            </div>
          </div>

          <div class="col-md-6">
            <label class="form-label small">Email</label>
            <div class="input-group input-group-sm">
              <span class="input-group-text"><i class="bi bi-envelope"></i></span>
              <input type="email" name="email" class="form-control"
                     value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>
          </div>

          <div class="col-md-4">
            <label class="form-label small">Role</label>
            <select name="role" class="form-select form-select-sm">
              <option value="user"  <?= $user['role']==='user'  ? 'selected' : '' ?>>User</option>
              <option value="admin" <?= $user['role']==='admin' ? 'selected' : '' ?>>Admin</option>
            </select>
          </div>

          <div class="col-md-8">
            <label class="form-label small">Password baru (opsional)</label>
            <div class="input-group input-group-sm">
              <span class="input-group-text"><i class="bi bi-lock"></i></span>
              <input type="password" name="password" class="form-control"
                     placeholder="Kosongkan jika tidak ingin mengubah">
            </div>
          </div>
        </div>

        <div class="mt-3">
          <button class="btn btn-success btn-sm">
            <i class="bi bi-check-circle me-1"></i> Simpan Perubahan
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

