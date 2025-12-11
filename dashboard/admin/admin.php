<?php
session_start();
include("../../config/config.php");

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit;
}

$id = $_SESSION['user_id'];
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id=$id");
$user = mysqli_fetch_assoc($query);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Admin Dashboard - TriHealth</title>
    <link rel="stylesheet" href="../../public/css/admin.css">
</head>
<body>

<div class="sidebar">
    <div class="logo">
        <img src="../../public/img/logo.jpg">
        <h2>TriHealth</h2>
    </div>

    <ul>
        <li class="active"><a href="admin.php">Dashboard</a></li>
        <li><a href="edit_profile.php">Kelola User</a></li>
        <li><a href="#">Kelola Makanan</a></li>
        <li><a href="../../auth/logout.php" class="logout">Logout</a></li>
    </ul>
</div>

<div class="main">
    <div class="header">
        <h1>Halo, <?= $user['role'] ?> 👋</h1>
        <p>Selamat datang di Dashboard Admin</p>
    </div>

    <div class="cards">

        <div class="card">
            <h3>Total User</h3>
                <div class="value">
                    <?php
                    $userCount = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM users"));
                    echo $userCount['total'];
                    ?>
                </div>
        </div>

        <div class="card">
            <h3>Admin Aktif</h3>
                <div class="value">
                    <?php
                    $adminCount = mysqli_fetch_assoc(mysqli_query($koneksi, "SELECT COUNT(*) AS total FROM users WHERE role='admin'"));
                    echo $adminCount['total'];
                    ?>
                </div>
        </div>
    </div>
</div>

</body>
</html>

