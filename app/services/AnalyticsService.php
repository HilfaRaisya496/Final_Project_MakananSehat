<?php
class AnalyticsService
{
    private PDO $db;

    public function __construct(PDO $db)
    {
        $this->db = $db;
    }

    public function analyzeForUser(int $userId): array
    {
        // ambil profil user untuk target kalori
        $stmt = $this->db->prepare("
            SELECT target_calories
            FROM user_profiles
            WHERE user_id = ?
        ");
        $stmt->execute([$userId]);
        $profile = $stmt->fetch(PDO::FETCH_ASSOC) ?: ['target_calories' => 2000];
        $targetCal = (int)($profile['target_calories'] ?: 2000);

        // ambil data 30 hari terakhir dari food_logs
        $stmt = $this->db->prepare("
            SELECT 
                log_date,
                meal_type,
                SUM(calories) AS calories,
                SUM(protein)  AS protein,
                SUM(carbs)    AS carbs,
                SUM(fat)      AS fat
            FROM food_logs
            WHERE user_id = ?
              AND log_date >= DATE_SUB(CURDATE(), INTERVAL 29 DAY)
            GROUP BY log_date, meal_type
            ORDER BY log_date ASC
        ");
        $stmt->execute([$userId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$rows) {
            return [
                'summary' => [
                    'days_tracked'     => 0,
                    'avg_calories'     => 0,
                    'target_calories'  => $targetCal,
                    'over_target_days' => 0,
                    'under_target_days'=> 0,
                    'within_range_days'=> 0,
                ],
                'macros' => [
                    'total_protein' => 0,
                    'total_carbs'   => 0,
                    'total_fat'     => 0,
                    'ratio'         => ['protein' => 0, 'carbs' => 0, 'fat' => 0],
                ],
                'by_meal' => [],
                'recommendations' => [
                    'Belum ada data log makan, mulai catat makanan harian untuk mendapatkan analisis pola makan.',
                ],
            ];
        }

        $perDay = [];     
        $byMeal = [        
            'breakfast' => 0,
            'lunch'     => 0,
            'dinner'    => 0,
            'snack'     => 0,
        ];
        $totalProtein = 0;
        $totalCarbs   = 0;
        $totalFat     = 0;

        foreach ($rows as $row) {
            $date  = $row['log_date'];
            $meal  = $row['meal_type'] ?? 'other';
            $cals  = (float)$row['calories'];
            $prot  = (float)$row['protein'];
            $carb  = (float)$row['carbs'];
            $fat   = (float)$row['fat'];

            if (!isset($perDay[$date])) {
                $perDay[$date] = 0;
            }
            $perDay[$date] += $cals;

            if (!isset($byMeal[$meal])) {
                $byMeal[$meal] = 0;
            }
            $byMeal[$meal] += $cals;

            $totalProtein += $prot;
            $totalCarbs   += $carb;
            $totalFat     += $fat;
        }

        $daysTracked = count($perDay);
        $totalCal    = array_sum($perDay);
        $avgCal      = $daysTracked ? round($totalCal / $daysTracked) : 0;

        $over = $under = $within = 0;
        $lowerBound = $targetCal * 0.9;
        $upperBound = $targetCal * 1.1;

        foreach ($perDay as $dayCal) {
            if ($dayCal > $upperBound) {
                $over++;
            } elseif ($dayCal < $lowerBound) {
                $under++;
            } else {
                $within++;
            }
        }

        $kcalFromProtein = $totalProtein * 4;
        $kcalFromCarbs   = $totalCarbs * 4;
        $kcalFromFat     = $totalFat * 9;
        $macroTotalKcal  = $kcalFromProtein + $kcalFromCarbs + $kcalFromFat;

        $ratioProtein = $macroTotalKcal ? round($kcalFromProtein / $macroTotalKcal * 100) : 0;
        $ratioCarbs   = $macroTotalKcal ? round($kcalFromCarbs   / $macroTotalKcal * 100) : 0;
        $ratioFat     = $macroTotalKcal ? round($kcalFromFat     / $macroTotalKcal * 100) : 0;

        // susun rekomendasi sederhana
        $recs = [];

        if ($over > $under && $over >= 3) {
            $recs[] = 'Kalori harian kamu cukup sering di atas target, pertimbangkan mengurangi porsi atau pilih menu lebih rendah kalori di beberapa hari.';
        } elseif ($under > $over && $under >= 3) {
            $recs[] = 'Kalori harian kamu sering di bawah target, pastikan asupan energi cukup agar tidak mudah lelah.';
        } else {
            $recs[] = 'Secara umum, kalori harian kamu sudah cukup dekat dengan target. Pertahankan kebiasaan ini.';
        }

        // bandingkan rasio makro dengan pola seimbang sederhana: 20% protein, 50% karbo, 30% lemak
        if ($ratioProtein < 15) {
            $recs[] = 'Proporsi protein tampak rendah, coba tambahkan sumber protein (telur, ayam, tempe) terutama di sarapan atau makan siang.';
        }
        if ($ratioCarbs > 55) {
            $recs[] = 'Karbohidrat mendominasi pola makan kamu, pertimbangkan mengurangi makanan tinggi gula/tepung dan tambahkan sayur/protein.';
        }
        if ($ratioFat > 35) {
            $recs[] = 'Asupan lemak cukup tinggi, usahakan kurangi gorengan atau makanan tinggi minyak dan pilih metode masak panggang/rebus.';
        }

        // periksa meal type yang paling besar kalorinya
        arsort($byMeal);
        $topMealType = array_key_first($byMeal);
        if ($topMealType === 'dinner') {
            $recs[] = 'Sebagian besar kalori datang dari makan malam, coba geser porsi kalori ke sarapan atau makan siang agar tidak terlalu berat di malam hari.';
        } elseif ($topMealType === 'snack') {
            $recs[] = 'Snack menyumbang banyak kalori, hati-hati dengan camilan tinggi gula/lemak di luar jam makan utama.';
        }

        return [
            'summary' => [
                'days_tracked'      => $daysTracked,
                'avg_calories'      => $avgCal,
                'target_calories'   => $targetCal,
                'over_target_days'  => $over,
                'under_target_days' => $under,
                'within_range_days' => $within,
            ],
            'macros' => [
                'total_protein' => round($totalProtein),
                'total_carbs'   => round($totalCarbs),
                'total_fat'     => round($totalFat),
                'ratio'         => [
                    'protein' => $ratioProtein,
                    'carbs'   => $ratioCarbs,
                    'fat'     => $ratioFat,
                ],
            ],
            'by_meal' => $byMeal,
            'recommendations' => $recs,
        ];
    }

    public function getAvgCaloriesPerUser(string $start, string $end): array
    {
        $stmt = $this->db->prepare("\n            SELECT t.user_id, AVG(t.daily_total) AS avg_cal
            FROM (
                SELECT user_id, log_date, SUM(calories) AS daily_total
                FROM food_logs
                WHERE log_date BETWEEN ? AND ?
                GROUP BY user_id, log_date
            ) t
            GROUP BY t.user_id
        ");
        $stmt->execute([$start, $end]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getOverTargetUsers(string $start, string $end): array
    {
        $stmt = $this->db->prepare("\n            SELECT t.user_id, AVG(t.daily_total) AS avg_cal, COALESCE(up.target_calories, 2000) AS target_calories
            FROM (
                SELECT user_id, log_date, SUM(calories) AS daily_total
                FROM food_logs
                WHERE log_date BETWEEN ? AND ?
                GROUP BY user_id, log_date
            ) t
            LEFT JOIN user_profiles up ON up.user_id = t.user_id
            GROUP BY t.user_id, target_calories
            HAVING AVG(t.daily_total) > COALESCE(up.target_calories, 2000)
        ");
        $stmt->execute([$start, $end]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        // return empty array if none
        return $rows ?: [];
    }
}
