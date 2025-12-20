<?php
require __DIR__ . '/../../app/core/bootstrap.php';
require_once __DIR__ . '/../../app/core/helpers.php';

auth_required();
role_user_only();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = $_SESSION['user_id'] ?? null;

$pdo = db();

// optional filter tanggal
$selectedDate = $_GET['date'] ?? date('Y-m-d');

$stmt = $pdo->prepare("
    SELECT id, log_date, meal_type, title, calories, protein, carbs, fat
    FROM food_logs
    WHERE user_id = ?
      AND log_date = ?
    ORDER BY log_date DESC, meal_type ASC, id DESC
");
$stmt->execute([$userId, $selectedDate]);
$logs = $stmt->fetchAll(PDO::FETCH_ASSOC) ?: [];
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Log Makan Harian | TriHealth</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <style>
    body {
      background-color: #f5f7fa;
      font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
    }
    .page-title {
      font-weight: 600;
    }
  </style>
</head>
<body class="bg-light">
<?php $active = 'logs'; include __DIR__ . '/_navbar.php'; ?>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="mb-1 page-title">Log Makan Harian</h3>
      <p class="text-muted small mb-0">
        Lihat dan kelola makanan yang sudah kamu catat per tanggal.
      </p>
    </div>
    <a href="cari_makanan.php" class="btn btn-success btn-sm">
      <i class="bi bi-plus-circle me-1"></i> Tambah dari pencarian
    </a>
  </div>

  <form class="row g-2 align-items-end mb-3" method="get">
    <div class="col-auto">
      <label for="date" class="col-form-label small fw-semibold">Tanggal</label>
    </div>
    <div class="col-auto">
      <input type="date" id="date" name="date" class="form-control form-control-sm"
             value="<?= htmlspecialchars($selectedDate) ?>">
    </div>
    <div class="col-auto">
      <button type="submit" class="btn btn-outline-success btn-sm">
        <i class="bi bi-filter me-1"></i> Tampilkan
      </button>
    </div>
  </form>

  <div class="card shadow-sm border-0">
    <div class="card-body p-0">
      <?php if (empty($logs)): ?>
        <p class="p-3 mb-0 text-muted small">
          Belum ada log makan untuk tanggal ini. Tambahkan makanan dari halaman rekomendasi atau pencarian.
        </p>
      <?php else: ?>
        <div class="table-responsive">
          <table class="table table-sm mb-0 align-middle">
            <thead class="table-light small text-muted">
            <tr>
              <th style="width:120px;">Waktu</th>
              <th>Nama makanan</th>
              <th style="width:90px;">Kalori</th>
              <th style="width:90px;">Protein</th>
              <th style="width:90px;">Karbo</th>
              <th style="width:90px;">Lemak</th>
              <th style="width:70px;"></th>
            </tr>
            </thead>
            <tbody class="small">
            <?php foreach ($logs as $row): ?>
              <tr>
                <td class="text-capitalize"><?= htmlspecialchars($row['meal_type']) ?></td>
                <td><?= htmlspecialchars($row['title']) ?></td>
                <td><?= (int)$row['calories'] ?> kkal</td>
                <td><?= round((float)$row['protein'], 1) ?> g</td>
                <td><?= round((float)$row['carbs'], 1) ?> g</td>
                <td><?= round((float)$row['fat'], 1) ?> g</td>
                <td class="text-end">
                  <form method="post" action="food_logs_delete.php"
                        onsubmit="return confirm('Hapus log ini?');" class="d-inline">
                    <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
                    <button type="submit" class="btn btn-sm btn-outline-danger">
                      <i class="bi bi-trash"></i>
                    </button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
