<?php
function base_url(string $path = ''): string {
    $base = $_ENV['APP_PATH'] ?? '';
    return $base . '/' . ltrim($path, '/');
}

function auth_required(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: ' . base_url('/auth/login.php'));
        exit;
    }
}

function role_user_only(): void {
    if (($_SESSION['role'] ?? '') !== 'user') {
        header('Location: ' . base_url('/dashboard/admin/admin.php'));
        exit;
    }
}

function role_admin_only(): void {
    if (($_SESSION['role'] ?? '') !== 'admin') {
        header('Location: ' . base_url('/dashboard/user/user.php'));
        exit;
    }
}
