<?php
require_once __DIR__ . '/../../app/controllers/RecommendationController.php';

$controller = new FoodRecommendationController();
$controller->saveToLog();

header('Location: food_logs_index.php');
exit;
