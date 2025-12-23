<?php

date_default_timezone_set('Asia/Jakarta');

// cron_send_menu_reminder.php
require __DIR__ . '/app/core/bootstrap.php';
require_once __DIR__ . '/app/api/EdamamClient.php';
require_once __DIR__ . '/app/services/RecommendationService.php';
require_once __DIR__ . '/app/services/NotificationService.php';

$pdo    = db();
$client = new EdamamClient();
$recommendationService = new RecommendationService($pdo, $client);
$notifService          = new NotificationService($pdo, $recommendationService);

// ambil semua user aktif yang mau dikirimi email
$stmt = $pdo->query("SELECT id FROM users WHERE statuss = 'active'");
$userIds = $stmt->fetchAll(PDO::FETCH_COLUMN);

foreach ($userIds as $userId) {
    $notifService->sendDailyMenuReminder((int)$userId);
}

// 0 7 * * * /usr/bin/php /path/to/cron_send_menu_reminder.php >/dev/null 2>&1
