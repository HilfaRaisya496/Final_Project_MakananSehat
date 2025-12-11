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
    <title>Dashboard | TriHealth</title>
    <link rel="stylesheet" href="../../public/css/dashboard.css">
    <script src="../../public/js/dashboard.js" defer></script>
</head>
<body>

<div class="sidebar">
    <div class="logo">
        <img src="../../public/img/logo.jpg">
        <h2>TriHealth</h2>
    </div>

    <ul>
        <li class="active"><a href="#">Dashboard</a></li>
        <li><a href="edit_profile.php">Profil Saya</a></li>
        <li><a href="#">Rekomendasi Makanan</a></li>
        <li><a href="#">Catatan Harian</a></li>
        <li><a href="#">Target Nutrisi</a></li>
        <li><a href="../../auth/logout.php" class="logout">Logout</a></li>
    </ul>
</div>

<div class="main">
    <div class="header">
        <h1>Halo, <?= $user['username'] ?> 👋</h1>
        <p>Selamat datang di Dashboard Kesehatanmu</p>
    </div>

    <div class="cards">

        <div class="card">
            <h3>Berat Badan</h3>
            <p class="value"><?= $user['bb'] ?> kg</p>
        </div>

        <div class="card">
            <h3>Tinggi Badan</h3>
            <p class="value"><?= $user['tb'] ?> cm</p>
        </div>

        <div class="card">
            <h3>Indeks BMI</h3>
            <?php
                $bmi = 0;
                if ($user['tb'] > 0) {
                    $bmi = $user['bb'] / pow(($user['tb']/100), 2);
                    $bmi = number_format($bmi, 1);
                }
            ?>
            <p class="value"><?= $bmi ?></p>
        </div>

    </div>
</div>

</body>
</html>
