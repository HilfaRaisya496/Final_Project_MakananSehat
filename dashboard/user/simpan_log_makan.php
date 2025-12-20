<?php
require_once __DIR__ . '/../../app/controllers/RecommendationController.php';

$controller = new FoodRecommendationController();
$controller->saveToLog();

header('Location: cari_makanan.php');
exit;
