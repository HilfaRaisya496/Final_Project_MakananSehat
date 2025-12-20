<?php
// app/controllers/FoodRecommendationController.php

require_once __DIR__ . '/../core/bootstrap.php';
require_once __DIR__ . '/../api/EdamamClient.php';
require_once __DIR__ . '/../services/RecommendationService.php';

class FoodRecommendationController
{
    private RecommendationService $service;

    public function __construct()
    {
        $pdo    = db();                
        $client = new EdamamClient();  
        $this->service = new RecommendationService($pdo, $client);
    }

    public function generate(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            throw new RuntimeException('User belum login.');
        }

        $diet    = $_POST['diet']    ?? null;
        $exclude = $_POST['exclude'] ?? null;

        $plan = $this->service->generateForUser($userId, $diet, $exclude);

        $_SESSION['meal_plan'] = $plan;

        return $plan;
    }

    public function saveToLog(): bool
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            throw new RuntimeException('User belum login.');
        }

        $recipeUri = $_POST['recipe_uri'] ?? '';
        $title     = $_POST['title']      ?? '';
        $mealType  = $_POST['meal_type']  ?? 'lunch';

        if ($recipeUri === '' || $title === '') {
            return false;
        }

        return $this->service->saveRecipeToLog($userId, $recipeUri, $mealType, $title);
    }

    public function search(): array
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $userId = $_SESSION['user_id'] ?? null;
        if (!$userId) {
            throw new RuntimeException('User belum login.');
        }

        $q = $_GET['q'] ?? '';
        $result = $this->service->searchRecipesRaw($userId, $q);

        $_SESSION['search_results'] = $result;
        $_SESSION['search_query']   = $q;

        return $result;
    }

}
