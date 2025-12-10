<?php
session_start();
include "../config/config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
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
    <title>Profil Saya</title>
</head>
<body>

<div class="profile-card">
    <img src="../public/img/logo.jpg" class="logo-small">

    <h2>Profil Saya</h2>

    <div class="profile-item">
        <span>Username</span>
        <p><?= $user['username'] ?></p>
    </div>

    <div class="profile-item">
        <span>Email</span>
        <p><?= $user['email'] ?></p>
    </div>

    <div class="profile-item">
        <span>Tinggi Badan</span>
        <p><?= $user['tb'] ?> cm</p>
    </div>

    <div class="profile-item">
        <span>Berat Badan</span>
        <p><?= $user['bb'] ?> kg</p>
    </div>

    <a href="edit_profile.php" class="btn-edit">Edit Profil</a>
</div>

</body>
</html>
