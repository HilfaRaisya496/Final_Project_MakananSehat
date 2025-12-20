<?php
require __DIR__ . '/../../app/core/bootstrap.php';
require_once __DIR__ . '/../../app/core/helpers.php';
auth_required();
role_admin_only();

$pdo = db();

header('Content-Type: text/csv');
header('Content-Disposition: attachment; filename="food_logs_'.date('Ymd').'.csv"');

$out = fopen('php://output', 'w');

// header kolom
fputcsv($out, ['user_id','nama','tanggal','meal_type','title','calories','protein','carbs','fat']);

$stmt = $pdo->query("
  SELECT u.id AS user_id, u.name, f.log_date, f.meal_type, f.title,
         f.calories, f.protein, f.carbs, f.fat
  FROM food_logs f
  JOIN users u ON u.id = f.user_id
  ORDER BY f.log_date DESC
");

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    // format angka sebelum ditulis ke CSV
    $formatted = [
        'user_id'   => $row['user_id'],
        'nama'      => $row['name'],
        'tanggal'   => $row['log_date'],
        'meal_type' => $row['meal_type'],
        'title'     => $row['title'],
        // bulatkan kalori ke integer
        'calories'  => round((float)$row['calories']),
        // 1 angka di belakang koma untuk makro
        'protein'   => round((float)$row['protein'], 1),
        'carbs'     => round((float)$row['carbs'], 1),
        'fat'       => round((float)$row['fat'], 1),
    ];

    fputcsv($out, $formatted);
}

fclose($out);
exit;
?>