<?php
require __DIR__ . '/../../app/core/bootstrap.php';
require_once __DIR__ . '/../../app/core/helpers.php';
require_once __DIR__ . '/../../app/services/AnalyticsService.php';

auth_required();
role_user_only();

$userId = $_SESSION['user_id'] ?? null;

$pdo = db();

// 7 hari terakhir: total kalori & makro per hari
$stmt = $pdo->prepare("
    SELECT 
        log_date,
        SUM(calories) AS total_calories,
        SUM(protein)  AS total_protein,
        SUM(carbs)    AS total_carbs,
        SUM(fat)      AS total_fat
    FROM food_logs
    WHERE user_id = ?
      AND log_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
    GROUP BY log_date
    ORDER BY log_date ASC
");
$stmt->execute([$userId]);
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$labels = [];
$dataCalories = [];
$totalProtein = 0;
$totalCarbs   = 0;
$totalFat     = 0;
$totalWeekCal = 0;

foreach ($rows as $row) {
    $labels[]       = $row['log_date'];
    $dataCalories[] = (float)$row['total_calories'];
    $totalProtein  += (float)$row['total_protein'];
    $totalCarbs    += (float)$row['total_carbs'];
    $totalFat      += (float)$row['total_fat'];
    $totalWeekCal  += (float)$row['total_calories'];
}

$avgDailyCal = count($rows) > 0 ? round($totalWeekCal / count($rows)) : 0;

// kirim ke JS
$labelsJson   = json_encode($labels);
$caloriesJson = json_encode($dataCalories);
$macrosJson   = json_encode([
    'protein' => $totalProtein,
    'carbs'   => $totalCarbs,
    'fat'     => $totalFat,
]);

$analyticsService = new AnalyticsService($pdo);
$analysis = $analyticsService->analyzeForUser($userId);

$summary = $analysis['summary'];
$recs    = $analysis['recommendations'] ?? [];

$avgCalFromService = (int)$summary['avg_calories'];
$targetCal         = (int)$summary['target_calories'];
$overDays          = (int)$summary['over_target_days'];
$underDays         = (int)$summary['under_target_days'];
$withinDays        = (int)$summary['within_range_days'];

$statusLabel   = 'Belum cukup data';
$statusColor   = 'secondary';
$statusMessage = 'Tambahkan lebih banyak log makan agar sistem bisa menganalisis pola makan.';

if ($summary['days_tracked'] > 0) {
    if ($overDays >= 3 && $overDays > $underDays) {
        $statusLabel   = 'Sering melebihi target';
        $statusColor   = 'danger';
        $statusMessage = "Rata-rata {$avgCalFromService} kcal, sering di atas target {$targetCal} kcal ({$overDays} hari).";
    } elseif ($underDays >= 3 && $underDays > $overDays) {
        $statusLabel   = 'Sering di bawah target';
        $statusColor   = 'warning';
        $statusMessage = "Rata-rata {$avgCalFromService} kcal, sering di bawah target {$targetCal} kcal ({$underDays} hari).";
    } else {
        $statusLabel   = 'Dalam rentang sehat';
        $statusColor   = 'success';
        $statusMessage = "Rata-rata {$avgCalFromService} kcal cukup dekat dengan target {$targetCal} kcal.";
    }
}

$mainRecommendation = $recs[0] ?? 'Tambahkan lebih banyak log makan agar sistem bisa memberi saran yang lebih akurat.';
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Dashboard | TriHealth</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
  <style>
    body {
      background-color: #f5f7fa;
      font-family: system-ui, -apple-system, "Segoe UI", sans-serif;
    }
    .page-title {
      font-weight: 600;
    }
    .stat-card {
      border-radius: 18px;
    }
    .stat-label {
      font-size: .8rem;
      text-transform: uppercase;
      letter-spacing: .04em;
    }
  </style>
</head>
<body>
<?php $active = 'dashboard'; include __DIR__ . '/_navbar.php'; ?>

<div class="container py-4">
  <div class="mb-3">
    <h3 class="mb-1 page-title">Dashboard</h3>
    <p class="text-muted small mb-0">
      Ringkasan asupan kalori dan nutrisi 7 hari terakhir.
    </p>
  </div>

  <!-- Ringkasan cepat -->
  <div class="row g-3 mb-4">
    <div class="col-md-4">
      <div class="card stat-card shadow-sm border-0 h-100">
        <div class="card-body">
          <div class="text-muted stat-label mb-1">Rata-rata kalori per hari</div>
          <div class="fs-2 fw-semibold"><?= (int)$avgDailyCal ?> kcal</div>
          <small class="text-muted">
            Dari log makan 7 hari terakhir.
          </small>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card stat-card shadow-sm border-0 h-100">
        <div class="card-body">
          <div class="text-muted stat-label mb-1">Status kalori</div>
          <span class="badge bg-<?= $statusColor ?> mb-1">
            <?= htmlspecialchars($statusLabel) ?>
          </span>
          <p class="small text-muted mb-0">
            <?= htmlspecialchars($statusMessage) ?>
          </p>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card stat-card shadow-sm border-0 h-100">
        <div class="card-body">
          <div class="text-muted stat-label mb-1">Saran pola makan</div>
          <p class="small mb-0">
            <?= htmlspecialchars($mainRecommendation) ?>
          </p>
        </div>
      </div>
    </div>
  </div>

  <!-- Grafik -->
  <div class="row g-3">
    <div class="col-lg-7">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body">
          <div class="d-flex justify-content-between align-items-center mb-2">
            <h6 class="card-title mb-0">Kalori per hari (7 hari)</h6>
            <span class="badge bg-light text-muted border small">
              <i class="bi bi-calendar-week me-1"></i> 7 hari terakhir
            </span>
          </div>
          <canvas id="chartKalori" height="140"></canvas>
        </div>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="card shadow-sm border-0 h-100">
        <div class="card-body">
          <h6 class="card-title mb-2">Total makronutrien (7 hari)</h6>
          <canvas id="chartMakro" height="160"></canvas>
          <p class="small text-muted mt-2 mb-0">
            Proporsi protein, karbohidrat, dan lemak dari semua makanan yang kamu catat.
          </p>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
const labelsKalori = <?= $labelsJson ?>;
const dataKalori   = <?= $caloriesJson ?>;
new Chart(document.getElementById('chartKalori'), {
  type: 'line',
  data: {
    labels: labelsKalori,
    datasets: [{
      label: 'Kalori',
      data: dataKalori,
      borderColor: 'rgba(43,147,72,1)',
      backgroundColor: 'rgba(43,147,72,0.12)',
      fill: true,
      tension: 0.35,
      pointRadius: 3,
      pointBackgroundColor: '#fff',
      pointBorderColor: 'rgba(43,147,72,1)'
    }]
  },
  options: {
    plugins: { legend: { display: false } },
    scales: { y: { beginAtZero: true } }
  }
});

const macros = <?= $macrosJson ?>;
new Chart(document.getElementById('chartMakro'), {
  type: 'doughnut',
  data: {
    labels: ['Protein','Karbohidrat','Lemak'],
    datasets: [{
      data: [macros.protein, macros.carbs, macros.fat],
      backgroundColor: ['#2b9348','#f9c74f','#f9844a']
    }]
  },
  options: {
    plugins: { legend: { position: 'bottom' } }
  }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

