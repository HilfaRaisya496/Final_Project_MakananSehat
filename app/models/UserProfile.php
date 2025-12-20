<?php

class UserProfile
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function getByUserId(int $userId): ?array
    {
        $stmt = $this->db->prepare("
            SELECT id, user_id, target_calories, diet, intolerances
            FROM user_profiles
            WHERE user_id = ?
            LIMIT 1
        ");
        $stmt->execute([$userId]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return $row ?: null;
    }

    /**
     * Insert / update profil user.
     */
    public function save(int $userId, int $targetCalories, string $diet, string $intolerances): bool
    {
        $existing = $this->getByUserId($userId);

        if ($existing) {
            $stmt = $this->db->prepare("
                UPDATE user_profiles
                SET target_calories = ?, diet = ?, intolerances = ?
                WHERE user_id = ?
            ");
            return $stmt->execute([$targetCalories, $diet, $intolerances, $userId]);
        } else {
            $stmt = $this->db->prepare("
                INSERT INTO user_profiles (user_id, target_calories, diet, intolerances)
                VALUES (?, ?, ?, ?)
            ");
            return $stmt->execute([$userId, $targetCalories, $diet, $intolerances]);
        }
    }
}
