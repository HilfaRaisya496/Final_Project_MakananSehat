<?php
require __DIR__ . '/../../app/core/bootstrap.php';
require __DIR__ . '/../../app/services/AnalyticsService.php';
require_once __DIR__ . '/../../app/core/helpers.php';

auth_required();
role_admin_only();

$pdo = db();

// ringkasan global
$sum = $pdo->query("
  SELECT COUNT(DISTINCT user_id) AS total_user_aktif,
         COUNT(*) AS total_log_makan
  FROM food_logs
")->fetch();

// rata-rata kalori per hari (7 hari)
$rows = $pdo->query("
  SELECT log_date, AVG(total_cal) avg_cal
  FROM (
    SELECT user_id, log_date, SUM(calories) total_cal
    FROM food_logs
    WHERE log_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
    GROUP BY user_id, log_date
  ) t
  GROUP BY log_date
  ORDER BY log_date
")->fetchAll();

$labels = [];
$dataAvg = [];
$map = [];
foreach ($rows as $r) {
    $map[$r['log_date']] = $r['avg_cal'];
}

for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-$i day"));
    $labels[]  = $d;
    $dataAvg[] = isset($map[$d]) ? (float)$map[$d] : 0;
}

$service = new AnalyticsService($pdo);
$start   = date('Y-m-d', strtotime('-6 day'));
$end     = date('Y-m-d');

$avgPerUser = $service->getAvgCaloriesPerUser($start, $end);
$overTarget = $service->getOverTargetUsers($start, $end);
?>
<!doctype html>
<html lang="id">
<head>
  <meta charset="utf-8">
  <title>Dashboard Admin</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">
<?php $active = 'users'; include __DIR__ . '/_navbar.php'; ?>

<div class="container py-4">
  <h3 class="mb-4">Dashboard Admin</h3>

  <div class="row g-3 mb-4">
    <div class="col-md-3">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="text-muted small">User aktif (punya log)</div>
          <div class="fs-4 fw-semibold"><?= $sum['total_user_aktif'] ?></div>
        </div>
      </div>
    </div>
    <div class="col-md-3">
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <div class="text-muted small">Total log makan</div>
          <div class="fs-4 fw-semibold"><?= $sum['total_log_makan'] ?></div>
        </div>
      </div>
    </div>
  </div>

  <div class="card shadow-sm border-0 mb-4">
    <div class="card-body">
      <h6 class="card-title mb-3">Rata-rata Kalori Harian (7 hari)</h6>
      <canvas id="chartAvgKalori" height="120"></canvas>
    </div>
  </div>
  <div class="row g-3 mb-4">
    <!-- card existing user/log -->
    <!-- ... -->
  <div class="col-md-6">
    <div class="card shadow-sm border-0 h-100">
      <div class="card-body">
        <h6 class="mb-2">Rekomendasi Sistem</h6>
        <p class="text-muted small mb-2">
          Periode analisis: <?= htmlspecialchars($start) ?> s/d <?= htmlspecialchars($end) ?>
        </p>
        <?php if ($overTarget): ?>
          <p class="small mb-1">
            Beberapa user sering melebihi target kalori. Pertimbangkan
            untuk mengirim notifikasi edukasi menu rendah kalori.
          </p>
        <?php else: ?>
          <p class="small mb-1">
            Mayoritas user masih dalam batas target kalori.
          </p>
        <?php endif; ?>

        <a href="export_foodlogs.php" class="btn btn-outline-secondary btn-sm mt-2">
          Export CSV Food Logs
        </a>
      </div>
    </div>
  </div>

<script>
const labelsAvg = <?= json_encode($labels) ?>;
const dataAvg   = <?= json_encode($dataAvg) ?>;

new Chart(document.getElementById('chartAvgKalori'), {
  type: 'line',
  data: {
    labels: labelsAvg,
    datasets: [{
      label: 'Rata-rata kalori',
      data: dataAvg,
      borderColor: 'rgba(43,147,72,1)',
      backgroundColor: 'rgba(43,147,72,0.12)',
      fill: true,
      tension: 0.35,
      pointRadius: 3,
      pointBackgroundColor: '#fff',
      pointBorderColor: 'rgba(43,147,72,1)',
      pointHoverRadius: 5
    }]
  },
  options: {
    plugins: { legend: { display: false } },
    scales: {
      y: { beginAtZero: true, ticks: { stepSize: 100 } }
    }
  }
});
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
