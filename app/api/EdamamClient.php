<?php
// app/api/EdamamClient.php

class EdamamClient
{
    private string $appId;
    private string $appKey;
    private string $baseUrl = 'https://api.edamam.com';

    public function __construct()
    {
        $this->appId  = $_ENV['EDAMAM_APP_ID']  ?? '';
        $this->appKey = $_ENV['EDAMAM_APP_KEY'] ?? '';
    }

    private function request(string $endpoint, array $params = [], int $ttlSeconds = 3600): array
    {
        if (empty($this->appId) || empty($this->appKey)) {
            return [
                'status'  => 'failure',
                'code'    => 401,
                'message' => 'Missing EDAMAM_APP_ID / EDAMAM_APP_KEY',
            ];
        }

        // auth Edamam pakai app_id & app_key
        $params['app_id']  = $this->appId;
        $params['app_key'] = $this->appKey;

        $query = http_build_query($params);
        $url   = $this->baseUrl . $endpoint . '?' . $query;

        $cacheKey = md5($url);
        $conn = db();

        // cache
        $stmt = $conn->prepare("SELECT response FROM api_cache WHERE cache_key=? AND expired_at>NOW()");
        $stmt->execute([$cacheKey]);
        if ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $cached = json_decode($row['response'], true);
            return is_array($cached) ? $cached : [];
        }

        // tambahkan header Edamam-Account-User di sini
        $headers = [
            'Edamam-Account-User: user-' . ($params['user_id'] ?? 'demo'),
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT        => 15,
            CURLOPT_HTTPHEADER     => $headers,
        ]);
        $res = curl_exec($ch);
        $curlErr = curl_error($ch);
        curl_close($ch);

        if ($res === false) {
            return [
                'status'  => 'failure',
                'code'    => 0,
                'message' => 'cURL error: ' . $curlErr,
            ];
        }

        $data = json_decode($res, true);
        if (!is_array($data)) {
            $data = [];
        }

        $stmt = $conn->prepare("
        INSERT INTO api_cache (cache_key, response, expired_at)
        VALUES (?, ?, DATE_ADD(NOW(), INTERVAL ? SECOND))
        ON DUPLICATE KEY UPDATE
            response = VALUES(response),
            expired_at = VALUES(expired_at)
        ");
        $stmt->execute([$cacheKey, json_encode($data), $ttlSeconds]);

        return $data;
    }

    public function searchRecipes(string $query, array $extraParams = []): array
    {
        $params = array_merge($extraParams, [
            'q' => $query,
        ]);

        return $this->request('/api/recipes/v2', $params);
    }

    public function getRecipeByUri(string $uri): array
    {
        return $this->request('/api/recipes/v2/by-uri', [
            'type' => 'public',
            'uri'  => $uri,
        ], 86400);
    }
}
