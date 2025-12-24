<?php
class FoodRecommendation
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function insertLog(
        int $userId,
        string $logDate,
        string $mealType,
        int $recipeId,
        string $title,
        float $calories,
        float $protein,
        float $carbs,
        float $fat
    ): bool {
        $stmt = $this->db->prepare("
            INSERT INTO food_logs
                (user_id, log_date, meal_type, recipe_id, title, calories, protein, carbs, fat)
            VALUES
                (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ");

        return $stmt->execute([
            $userId,
            $logDate,
            $mealType,
            $recipeId,
            $title,
            $calories,
            $protein,
            $carbs,
            $fat,
        ]);
    }

    /**
     * Ambil ringkasan nutrisi harian user.
     */
    public function getDailySummary(int $userId, string $date): array
    {
        $stmt = $this->db->prepare("
            SELECT
                IFNULL(SUM(calories),0) AS total_calories,
                IFNULL(SUM(protein),0)  AS total_protein,
                IFNULL(SUM(carbs),0)    AS total_carbs,
                IFNULL(SUM(fat),0)      AS total_fat
            FROM food_logs
            WHERE user_id = ? AND log_date = ?
        ");
        $stmt->execute([$userId, $date]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: [
            'total_calories' => 0,
            'total_protein'  => 0,
            'total_carbs'    => 0,
            'total_fat'      => 0,
        ];
    }

    /**
     * Data kalori 7 hari terakhir untuk grafik user.
     */
    public function getLast7DaysCalories(int $userId): array
    {
        $stmt = $this->db->prepare("
            SELECT log_date, SUM(calories) AS total_cal
            FROM food_logs
            WHERE user_id = ?
              AND log_date >= DATE_SUB(CURDATE(), INTERVAL 6 DAY)
            GROUP BY log_date
            ORDER BY log_date
        ");
        $stmt->execute([$userId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
