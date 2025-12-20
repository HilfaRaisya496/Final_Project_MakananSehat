<?php
require __DIR__ . '/../../app/core/bootstrap.php';
require_once __DIR__ . '/../../app/controllers/RecommendationController.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$results = $_SESSION['search_results'] ?? null;
$searchQuery = $_SESSION['search_query'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['do']) && $_GET['do'] === 'search') {
    $controller = new FoodRecommendationController();
    $results = $controller->search();
    $searchQuery = $_GET['q'] ?? '';
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Cari Makanan / Resep | TriHealth</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <style>
    body {
      background-color: #f5f7fa;
      font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
    }
    .page-wrapper {
      max-width: 1080px;
    }
    .result-card img {
      width: 96px;
      height: 96px;
      object-fit: cover;
      border-radius: 12px;
      background: #ecf0f1;
    }
    .pill-badge {
      font-size: .75rem;
      border-radius: 999px;
    }
  </style>
</head>
<body>
<?php $active = 'cari'; include __DIR__ . '/_navbar.php'; ?>

<div class="container page-wrapper py-4">
  <div class="d-flex justify-content-between align-items-center mb-2">
    <div>
      <h3 class="mb-1">Cari Makanan / Resep</h3>
      <p class="text-muted small mb-0">
        Ketik nama makanan atau resep, lalu tambahkan ke log makan kamu.
      </p>
    </div>
    <a href="user.php" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-1"></i> Kembali ke dashboard
    </a>
  </div>

  <!-- Form pencarian -->
  <div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
      <form method="get" class="row g-2 align-items-center">
        <input type="hidden" name="do" value="search">
        <div class="col-md-9">
          <div class="input-group input-group-lg">
            <span class="input-group-text bg-white border-end-0">
              <i class="bi bi-search text-muted"></i>
            </span>
            <input
              type="text"
              name="q"
              class="form-control border-start-0"
              placeholder="Contoh: chicken salad, nasi goreng..."
              value="<?= htmlspecialchars($searchQuery) ?>"
            >
          </div>
        </div>
        <div class="col-md-3 text-md-end">
          <button type="submit" class="btn btn-success w-100">
            Cari resep
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Hasil -->
  <?php if ($results): ?>
    <?php if (($results['status'] ?? 'success') === 'failure'): ?>
      <div class="alert alert-danger small">
        <?= htmlspecialchars($results['message'] ?? 'Terjadi kesalahan saat pencarian.') ?>
      </div>
    <?php elseif (($results['status'] ?? '') === 'empty'): ?>
      <p class="text-muted small">Masukkan kata kunci makanan di kotak pencarian di atas.</p>
    <?php else: ?>
      <?php if (empty($results['results'])): ?>
        <p class="text-muted small">Tidak ada hasil untuk kata kunci tersebut.</p>
      <?php else: ?>
        <div class="vstack gap-2">
          <?php foreach ($results['results'] as $meal): ?>
            <div class="card result-card shadow-sm border-0">
              <div class="card-body">
                <div class="row g-3 align-items-center">
                  <div class="col-auto">
                    <?php if (!empty($meal['image'])): ?>
                      <img src="<?= htmlspecialchars($meal['image']) ?>" alt="">
                    <?php else: ?>
                      <img src="https://via.placeholder.com/96x96?text=No+Image" alt="">
                    <?php endif; ?>
                  </div>
                  <div class="col">
                    <h6 class="mb-1">
                      <?= htmlspecialchars($meal['title'] ?? '') ?>
                    </h6>
                    <div class="mb-2">
                      <span class="badge bg-light text-dark pill-badge border">
                        <?= (int)($meal['calories'] ?? 0) ?> kkal / porsi
                      </span>
                      <span class="badge bg-light text-dark pill-badge border">
                        <?= (int)($meal['servings'] ?? 1) ?> porsi
                      </span>
                      <span class="badge bg-light text-dark pill-badge border">
                        <?= (int)($meal['readyInMinutes'] ?? 0) ?> menit
                      </span>
                    </div>
                    <form class="d-flex flex-wrap gap-2 align-items-center" method="post" action="simpan_log_makan.php">
                      <input type="hidden" name="recipe_uri" value="<?= htmlspecialchars($meal['uri'] ?? '') ?>">
                      <input type="hidden" name="title" value="<?= htmlspecialchars($meal['title'] ?? '') ?>">
                      <select name="meal_type" class="form-select form-select-sm" style="max-width: 160px;">
                        <option value="breakfast">Breakfast</option>
                        <option value="lunch">Lunch</option>
                        <option value="dinner">Dinner</option>
                        <option value="snack">Snack</option>
                      </select>
                      <button type="submit" class="btn btn-outline-success btn-sm">
                        <i class="bi bi-plus-circle me-1"></i> Tambahkan ke log
                      </button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    <?php endif; ?>
  <?php else: ?>
    <p class="text-muted small">Belum ada pencarian. Coba cari makanan di atas.</p>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
