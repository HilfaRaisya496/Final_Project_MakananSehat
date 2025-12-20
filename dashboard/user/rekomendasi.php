<?php
require_once __DIR__ . '/../../app/core/bootstrap.php';
require_once __DIR__ . '/../../app/controllers/RecommendationController.php';
require_once __DIR__ . '/../../app/core/helpers.php';

auth_required();
role_user_only();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$userId = $_SESSION['user_id'];
$pdo    = db();

// ambil profil user
$stmt = $pdo->prepare("
    SELECT target_calories, diet, intolerances
    FROM user_profiles
    WHERE user_id = ?
");
$stmt->execute([$userId]);
$profile = $stmt->fetch(PDO::FETCH_ASSOC) ?: [
    'target_calories' => '',
    'diet'            => '',
    'intolerances'    => '',
];

$controller = new FoodRecommendationController();

// generate meal plan jika form disubmit
// kalau form rekomendasi di‑POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'generate') {
    $controller = new FoodRecommendationController();
    $plan = $controller->generate();
    header('Location: rekomendasi.php'); exit;
}

// ambil plan terbaru dari session untuk ditampilkan
$plan = $_SESSION['meal_plan'] ?? null;
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Rekomendasi Makanan | TriHealth</title>
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
    .meal-image {
      width: 96px;
      height: 96px;
      border-radius: 12px;
      object-fit: cover;
      background: #ecf0f1;
    }
    .pill-badge {
      font-size: .75rem;
      border-radius: 999px;
    }
  </style>
</head>
<body>
<?php $active = 'rekomendasi'; include __DIR__ . '/_navbar.php'; ?>

<div class="container page-wrapper py-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div>
      <h3 class="mb-1">Rekomendasi Makanan</h3>
      <p class="text-muted small mb-0">
        Generate 3 ide makan (pagi, siang, malam) sesuai preferensi diet dan bahan yang ingin dihindari.
      </p>
    </div>
    <a href="user.php" class="btn btn-outline-secondary btn-sm">
      <i class="bi bi-arrow-left me-1"></i> Kembali ke dashboard
    </a>
  </div>

  <!-- Form generate meal plan -->
  <div class="card shadow-sm border-0 mb-3">
    <div class="card-body">
      <form method="post" class="row g-3 align-items-center">
        <input type="hidden" name="action" value="generate">

        <div class="col-md-5">
          <label for="diet" class="form-label small fw-semibold">
            Diet (opsional)
          </label>
          <input
            id="diet"
            type="text"
            name="diet"
            class="form-control form-control-sm"
            placeholder="Contoh: high-protein, low-carb"
            value="<?= htmlspecialchars($_POST['diet'] ?? $profile['diet'] ?? '') ?>"
          >
          
        </div>

        <div class="col-md-5">
          <label for="exclude" class="form-label small fw-semibold">
            Tidak suka / alergi (pisahkan dengan koma)
          </label>
          <input
            id="exclude"
            type="text"
            name="exclude"
            class="form-control form-control-sm"
            placeholder="Contoh: peanuts, milk"
            value="<?= htmlspecialchars($_POST['exclude'] ?? $profile['intolerances'] ?? '') ?>"
          >
          
        </div>

        <div class="col-md-2 d-flex align-items-stretch justify-content-md-end">
          <button type="submit" class="btn btn-success w-100" style="margin-top:1.8rem;">
            <i class="bi bi-magic me-1"></i> Generate
          </button>
        </div>
      </form>
    </div>
  </div>

  <!-- Hasil rekomendasi -->
  <?php if ($plan): ?>
    <?php if (($plan['status'] ?? 'success') === 'failure'): ?>
      <div class="alert alert-danger small">
        <?= htmlspecialchars($plan['message'] ?? 'Terjadi kesalahan saat mengambil rekomendasi.') ?>
      </div>
    <?php else: ?>
      <div class="row g-3">
        <div class="col-lg-8">
          <?php
          $labels = ['Breakfast', 'Lunch', 'Dinner'];
          ?>
          <?php if (empty($plan['meals'])): ?>
            <p class="text-muted small">
              Belum ada rekomendasi. Coba generate meal plan di atas.
            </p>
          <?php else: ?>
            <div class="vstack gap-2">
              <?php foreach ($plan['meals'] as $index => $meal): ?>
                <div class="card shadow-sm border-0">
                  <div class="card-body">
                    <div class="row g-3 align-items-center">
                      <div class="col-auto">
                        <?php if (!empty($meal['image'])): ?>
                          <img src="<?= htmlspecialchars($meal['image']) ?>" alt="" class="meal-image">
                        <?php else: ?>
                          <img src="https://via.placeholder.com/96x96?text=No+Image" alt="" class="meal-image">
                        <?php endif; ?>
                      </div>
                      <div class="col">
                        <h6 class="mb-1">
                          <?= htmlspecialchars($labels[$index] ?? 'Meal') ?>:
                          <?= htmlspecialchars($meal['title'] ?? '') ?>
                        </h6>
                        <div class="mb-2">
                          <span class="badge bg-warning-subtle text-warning-emphasis border pill-badge">
                            <?= (int)($meal['calories'] ?? 0) ?> kkal / porsi
                          </span>
                          <span class="badge bg-light text-dark border pill-badge">
                            <?= (int)($meal['servings'] ?? 1) ?> porsi
                          </span>
                          <span class="badge bg-info-subtle text-info-emphasis border pill-badge">
                            <?= (int)($meal['readyInMinutes'] ?? 0) ?> menit
                          </span>
                        </div>
                        <form method="post" action="simpan_log_makan.php" class="d-flex flex-wrap gap-2">
                          <input type="hidden" name="recipe_uri" value="<?= htmlspecialchars($meal['uri'] ?? '') ?>">
                          <input type="hidden" name="title" value="<?= htmlspecialchars($meal['title'] ?? '') ?>">
                          <input type="hidden" name="meal_type" value="<?= strtolower($labels[$index] ?? 'meal') ?>">
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
        </div>

        <?php if (!empty($plan['nutrients'])): ?>
          <div class="col-lg-4">
            <div class="card shadow-sm border-0">
              <div class="card-body">
                <h6 class="mb-1">Ringkasan nutrisi</h6>
                <p class="text-muted small mb-2">
                  Total estimasi untuk 3 meal yang ditampilkan.
                </p>
                <dl class="row mb-0 small">
                  <dt class="col-6 text-muted">Kalori</dt>
                  <dd class="col-6 text-end fw-semibold">
                    <?= (int)($plan['nutrients']['calories'] ?? 0) ?> kkal
                  </dd>

                  <dt class="col-6 text-muted">Protein</dt>
                  <dd class="col-6 text-end fw-semibold">
                    <?= (int)($plan['nutrients']['protein'] ?? 0) ?> g
                  </dd>

                  <dt class="col-6 text-muted">Karbohidrat</dt>
                  <dd class="col-6 text-end fw-semibold">
                    <?= (int)($plan['nutrients']['carbohydrates'] ?? 0) ?> g
                  </dd>

                  <dt class="col-6 text-muted">Lemak</dt>
                  <dd class="col-6 text-end fw-semibold">
                    <?= (int)($plan['nutrients']['fat'] ?? 0) ?> g
                  </dd>
                </dl>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    <?php endif; ?>
  <?php else: ?>
    <p class="text-muted small">
      Belum ada meal plan yang di-generate. Isi preferensi di atas lalu klik “Generate”.
    </p>
  <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

