<?php
function auth_required(): void {
    if (empty($_SESSION['user_id'])) {
        header('Location: /auth/login.php');
        exit;
    }
}

function role_user_only(): void {
    if (($_SESSION['role'] ?? '') !== 'user') {
        header('Location: /tes2/dashboard/admin/admin.php');
        exit;
    }
}

function role_admin_only(): void {
    if (($_SESSION['role'] ?? '') !== 'admin') {
        header('Location: /tes2/dashboard/user/user.php');
        exit;
    }
}
