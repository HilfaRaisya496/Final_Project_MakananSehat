<?php
require_once __DIR__ . '/../app/core/bootstrap.php';
require_once __DIR__ . '/../app/core/helpers.php';

$uri  = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$base = ($_ENV['APP_PATH'] ?? '') . '/public';

$path = str_replace($base, '', $uri);
$path = rtrim($path, '/') ?: '/';

if ($path === '/index.php') {
    $path = '/';
}

switch ($path) {
    case '/':
        header('Location: ' . base_url('/auth/login.php'));
        exit;

    case '/dashboard/user':
        require __DIR__ . '/../dashboard/user/user.php';
        break;

    case '/dashboard/admin':
        require __DIR__ . '/../dashboard/admin/admin.php';
        break;

    case '/rekomendasi':
        require __DIR__ . '/../dashboard/user/rekomendasi.php';
        break;

    default:
        http_response_code(404);
        echo '404 Not Found';
}
?>
