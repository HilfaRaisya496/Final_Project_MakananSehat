<?php
require __DIR__ . '/../../app/core/bootstrap.php';
require_once __DIR__ . '/../../app/core/helpers.php';
auth_required();
role_admin_only();

$pdo = db();

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM users WHERE id=? AND role!='admin'");
    $stmt->execute([$id]);
    header('Location: users.php');
    exit;
}

$users = $pdo->query("SELECT id,name,email,role,created_at FROM users ORDER BY created_at DESC")->fetchAll();
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Users | TriHealth Admin</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
</head>
<body class="bg-light">
<?php $active = 'users'; include __DIR__ . '/_navbar.php'; ?>

<div class="container py-4">
  <div class="d-flex flex-wrap justify-content-between align-items-center mb-3">
    <div>
      <h3 class="mb-1">Manajemen User</h3>
      <p class="text-muted small mb-0">
        Kelola akun admin dan user aplikasi rekomendasi makanan sehat.
      </p>
    </div>
    <a href="user_create.php" class="btn btn-success btn-sm mt-2 mt-md-0">
      <i class="bi bi-person-plus me-1"></i> Tambah User
    </a>
  </div>

  <div class="card shadow-sm border-0">
    <div class="card-body p-0">
      <div class="table-responsive">
        <table class="table table-striped table-hover mb-0 align-middle">
          <thead class="table-light small text-uppercase text-muted">
            <tr>
              <th style="width:60px;">#</th>
              <th>Nama</th>
              <th>Email</th>
              <th style="width:110px;">Role</th>
              <th style="width:170px;">Dibuat</th>
              <th style="width:150px;">Aksi</th>
            </tr>
          </thead>
          <tbody class="small">
          <?php foreach ($users as $i => $u): ?>
            <tr>
              <td><?= $i+1 ?></td>
              <td><?= htmlspecialchars($u['name']) ?></td>
              <td><?= htmlspecialchars($u['email']) ?></td>
              <td>
                <span class="badge bg-<?= $u['role']==='admin' ? 'danger' : 'secondary' ?>">
                  <?= htmlspecialchars($u['role']) ?>
                </span>
              </td>
              <td><?= htmlspecialchars($u['created_at']) ?></td>
              <td>
                <a href="user_edit.php?id=<?= (int)$u['id'] ?>"
                   class="btn btn-sm btn-outline-primary me-1">
                  <i class="bi bi-pencil-square"></i>
                </a>
                <?php if ($u['role'] !== 'admin'): ?>
                  <a href="users.php?delete=<?= (int)$u['id'] ?>"
                     class="btn btn-sm btn-outline-danger"
                     onclick="return confirm('Hapus user ini?');">
                    <i class="bi bi-trash"></i>
                  </a>
                <?php endif; ?>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

