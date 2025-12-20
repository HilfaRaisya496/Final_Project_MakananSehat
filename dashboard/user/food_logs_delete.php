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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = (int)($_POST['id'] ?? 0);

    if ($id > 0) {
        // pastikan hanya hapus log milik user ini
        $stmt = $pdo->prepare("DELETE FROM food_logs WHERE id = ? AND user_id = ?");
        $stmt->execute([$id, $userId]);
    }
}

// kembali ke list
$redirectDate = $_GET['date'] ?? date('Y-m-d');
header('Location: food_logs_index.php?date=' . urlencode($redirectDate));
exit;
