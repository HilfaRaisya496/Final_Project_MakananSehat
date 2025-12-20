<?php
require __DIR__ . '/../../app/core/bootstrap.php';
require_once __DIR__ . '/../../app/core/helpers.php';
require_once __DIR__ . '/../../app/models/UserProfile.php';

auth_required();
role_user_only();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = $_SESSION['user_id'] ?? null;

$pdo = db();
$userProfileModel = new UserProfile($pdo);
$profile = $userProfileModel->getByUserId((int)$userId);

// nilai default jika belum ada profil
$targetCalories = $profile['target_calories'] ?? 2000;
$diet           = $profile['diet'] ?? '';
$intolerances   = $profile['intolerances'] ?? '';
$successMsg     = '';
$errorMsg       = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $targetCalories = (int)($_POST['target_calories'] ?? 2000);
    $diet           = trim($_POST['diet'] ?? '');
    $intolerances   = trim($_POST['intolerances'] ?? '');

    if ($targetCalories <= 0) {
        $errorMsg = 'Target kalori harus lebih dari 0.';
    } else {
        $ok = $userProfileModel->save(
            (int)$userId,
            $targetCalories,
            $diet,
            $intolerances
        );
        if ($ok) {
            $successMsg = 'Profil gizi berhasil disimpan.';
        } else {
            $errorMsg = 'Gagal menyimpan profil gizi.';
        }
    }
}
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Edit Profil Gizi | TriHealth</title>
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
    .profile-card {
      border-radius: 18px;
    }
  </style>
</head>
<body>
<?php $active = 'profile'; include __DIR__ . '/_navbar.php'; ?>

<div class="container py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="mb-1 page-title">Edit Profil Gizi</h3>
      <p class="text-muted small mb-0">
        Atur target kalori harian, jenis diet, dan bahan yang ingin dihindari untuk rekomendasi menu yang lebih tepat.
      </p>
    </div>
    <a href="user.php" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-1"></i> Kembali
    </a>
  </div>

  <?php if ($successMsg): ?>
    <div class="alert alert-success small">
      <?= htmlspecialchars($successMsg) ?>
    </div>
  <?php endif; ?>
  <?php if ($errorMsg): ?>
    <div class="alert alert-danger small">
      <?= htmlspecialchars($errorMsg) ?>
    </div>
  <?php endif; ?>

  <div class="card profile-card shadow-sm border-0">
    <div class="card-body">
      <form method="post" class="small" novalidate>
        <div class="mb-3">
          <label for="target_calories" class="form-label small fw-semibold">
            Target kalori harian (kkal)
          </label>
          <input
            type="number"
            class="form-control form-control-sm"
            id="target_calories"
            name="target_calories"
            min="800"
            max="5000"
            value="<?= htmlspecialchars((string)$targetCalories) ?>"
            required
          >
          <div class="form-text">
            Contoh: 2000 kkal per hari (sesuaikan dengan kebutuhanmu).
          </div>
        </div>

        <div class="mb-3">
          <label for="diet" class="form-label small fw-semibold">Diet (opsional)</label>
          <select id="diet" name="diet" class="form-select form-select-sm">
            <option value="" <?= $diet === '' ? 'selected' : '' ?>>Tidak ada / umum</option>
            <option value="balanced" <?= $diet === 'balanced' ? 'selected' : '' ?>>Balanced</option>
            <option value="high-protein" <?= $diet === 'high-protein' ? 'selected' : '' ?>>High protein</option>
            <option value="low-carb" <?= $diet === 'low-carb' ? 'selected' : '' ?>>Low carb</option>
            <option value="low-fat" <?= $diet === 'low-fat' ? 'selected' : '' ?>>Low fat</option>
          </select>
          <div class="form-text">
            Dipakai sebagai filter diet saat mengambil menu dari Edamam (balanced, high-protein, low-carb, dll.).
          </div>
        </div>

        <div class="mb-3">
          <label for="intolerances" class="form-label small fw-semibold">
            Bahan yang ingin dihindari
          </label>
          <textarea
            id="intolerances"
            name="intolerances"
            class="form-control form-control-sm"
            rows="3"
            placeholder="Contoh: peanuts, milk, shrimp"
          ><?= htmlspecialchars($intolerances) ?></textarea>
          <div class="form-text">
            Pisahkan dengan koma. 
          </div>
        </div>

        <div class="mt-3">
          <button type="submit" class="btn btn-success btn-sm">
            <i class="bi bi-check-circle me-1"></i> Simpan Profil
          </button>
          <a href="user.php" class="btn btn-outline-secondary btn-sm ms-2">
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
