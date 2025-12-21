<?php
// app/services/RecommendationService.php

require_once __DIR__ . '/../api/EdamamClient.php';
require_once __DIR__ . '/../models/FoodRecommendation.php';

class RecommendationService
{
    private PDO $db;
    private EdamamClient $client;
    private FoodRecommendation $foodRec;

    public function __construct(PDO $db, EdamamClient $client)
    {
        $this->db      = $db;
        $this->client  = $client;
        $this->foodRec = new FoodRecommendation($db);
    }

    public function generateForUser(int $userId, ?string $diet = null, ?string $exclude = null): array
    {
        // ambil profil user
        $stmt = $this->db->prepare("
            SELECT target_calories, diet, intolerances
            FROM user_profiles
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'target_calories' => 2000,
            'diet'            => '',
            'intolerances'    => '',
        ];

        $targetCalories = (int)($profile['target_calories'] ?: 2000);
        $dietFinal      = $diet    !== null && $diet    !== '' ? $diet    : $profile['diet'];
        $exclFinal      = $exclude !== null && $exclude !== '' ? $exclude : $profile['intolerances'];

       
        $mealsPerDay = 3;
        $calPerMeal  = (int) round($targetCalories / $mealsPerDay);

        
        $margin = 150;
        $lower  = max(100, $calPerMeal - $margin);
        $upper  = $calPerMeal + $margin;

        $params = [
            'type'       => 'public',
            'q'          => 'healthy',
            'random'     => 'true',
            'imageSize'  => 'REGULAR',
            'calories'   => "{$lower}-{$upper}", 
            'field'      => ['uri','label','calories','yield','totalNutrients','totalTime','image'],
            'limit'      => 10,
        ];

        if ($dietFinal) {
            $params['diet'] = $dietFinal; 
        }
        if ($exclFinal) {
            $params['excluded'] = $exclFinal; 
        }

        $response = $this->client->searchRecipes($params['q'], $params, 'user-'.$userId);

        if (!is_array($response) || isset($response['error'])) {
            return [
                'status'  => 'failure',
                'message' => $response['error'] ?? 'Failed to call Edamam API',
            ];
        }

        $hits = $response['hits'] ?? [];
        if (empty($hits)) {
            return [
                'status'  => 'failure',
                'message' => 'Tidak ada resep yang cocok.',
            ];
        }

        // 3 meal: sarapan, lunch, dinner
        $selected = array_slice($hits, 0, $mealsPerDay);

        $meals        = [];
        $totalCal     = 0;
        $totalProtein = 0;
        $totalCarbs   = 0;
        $totalFat     = 0;

        foreach ($selected as $hit) {
            $recipe = $hit['recipe'];

            $servings      = max(1, (int)($recipe['yield'] ?? 1));
            $calPerServing = ($recipe['calories'] ?? 0) / $servings;

            $nut = $recipe['totalNutrients'] ?? [];

            $protein = $this->getNutrientQuantity($nut, 'PROCNT'); 
            $carbs   = $this->getNutrientQuantity($nut, 'CHOCDF');
            $fat     = $this->getNutrientQuantity($nut, 'FAT');

            $totalCal     += $calPerServing;
            $totalProtein += $protein / $servings;
            $totalCarbs   += $carbs   / $servings;
            $totalFat     += $fat     / $servings;

            $meals[] = [
                'uri'            => $recipe['uri'],
                'title'          => $recipe['label'],
                'image'          => $recipe['image'] ?? null,
                'readyInMinutes' => (int)($recipe['totalTime'] ?? 0),
                'servings'       => $servings,
                'calories'       => round($calPerServing),
            ];
        }

        $calDiff = $totalCal - $targetCalories;

        return [
            'status'    => 'success',
            'meals'     => $meals,
            'nutrients' => [
                'targetCalories' => $targetCalories,
                'calories'       => round($totalCal),
                'calDiff'        => round($calDiff),  
                'protein'        => round($totalProtein),
                'carbohydrates'  => round($totalCarbs),
                'fat'            => round($totalFat),
            ],
        ];
    }

    public function searchRecipesRaw(int $userId, string $query): array
    {
        $query = trim($query);
        if ($query === '') {
            return [
                'status'  => 'empty',
                'message' => 'Kata kunci pencarian kosong.',
                'results' => [],
            ];
        }

        $params = [
            'type'       => 'public',
            'q'          => $query,
            'imageSize'  => 'REGULAR',
            'calories'   => '50-1200',
            'field'      => ['uri','label','calories','yield','totalNutrients','totalTime','image'],
            'limit'      => 20,
        ];

        $response = $this->client->searchRecipes($params['q'], $params, 'user-'.$userId);

        if (!is_array($response) || isset($response['error'])) {
            return [
                'status'  => 'failure',
                'message' => $response['error'] ?? 'Gagal memanggil Edamam untuk pencarian.',
                'results' => [],
            ];
        }

        $hits = $response['hits'] ?? [];
        $results = [];

        foreach ($hits as $hit) {
            $recipe = $hit['recipe'] ?? null;
            if (!$recipe) continue;

            $servings = max(1, (int)($recipe['yield'] ?? 1));
            $calPerServing = ($recipe['calories'] ?? 0) / $servings;

            $results[] = [
                'uri'            => $recipe['uri'],
                'title'          => $recipe['label'],
                'image'          => $recipe['image'] ?? null,
                'readyInMinutes' => (int)($recipe['totalTime'] ?? 0),
                'servings'       => $servings,
                'calories'       => round($calPerServing),
            ];
        }

        return [
                'status'  => 'success',
                'message' => 'OK',
                'results' => $results,
        ];
    }


    public function saveRecipeToLog(int $userId, string $recipeUri, string $mealType, string $title): bool
    {
        $info = $this->client->getRecipeByUri($recipeUri);

        if (!is_array($info) || empty($info['hits'][0]['recipe'])) {
            return false;
        }

        $recipe   = $info['hits'][0]['recipe'];
        $nut      = $recipe['totalNutrients'] ?? [];
        $servings = max(1, (int)($recipe['yield'] ?? 1));

        $calPerServing = ($recipe['calories'] ?? 0) / $servings;
        $protein       = $this->getNutrientQuantity($nut, 'PROCNT') / $servings;
        $carbs         = $this->getNutrientQuantity($nut, 'CHOCDF') / $servings;
        $fat           = $this->getNutrientQuantity($nut, 'FAT') / $servings;

        return $this->foodRec->insertLog(
            $userId,
            date('Y-m-d'),
            $mealType,
            0,                  
            $title,
            $calPerServing,
            $protein,
            $carbs,
            $fat
        );
    }

    private function getNutrientQuantity(array $totalNutrients, string $code): float
    {
        if (isset($totalNutrients[$code]['quantity'])) {
            return (float)$totalNutrients[$code]['quantity'];
        }
        return 0.0;
    }
}
