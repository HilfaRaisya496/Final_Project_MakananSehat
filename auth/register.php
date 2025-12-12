<?php
require_once "../config/config.php";

if (isset($_POST['register'])) {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $tb = $_POST['tb'];
    $bb = $_POST['bb'];

    // hash password
    $hashed = password_hash($password, PASSWORD_DEFAULT);

    // insert
    $sql = "INSERT INTO users (username, password, tb, bb) VALUES ('$username', '$hashed', $tb, $bb)";
    if (mysqli_query($koneksi, $sql)) {
        header("Location: register_success.php?status=success");
        exit;
    } else {
        $error = "Terjadi kesalahan: " . mysqli_error($koneksi);
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Register</title>
    <link rel="stylesheet" href="../public/css/auth.css">
</head>
<body>

<div class="box">
    <img src="../public/img/logo.jpg" class="logo">
    <h2>Register</h2>

    <?php if(isset($error)) { ?>
        <div class="error"><?= $error ?></div>
    <?php } ?>

    <?php if(isset($success)) { ?>
        <div class="error" style="background:#ddffdd; color:#2b662e;"><?= $success ?></div>
    <?php } ?>

    <form method="POST">
        <input type="text" name="username" placeholder="Masukkan Username" required>
        <input type="email" name="email" placeholder="Masukkan Email" required>
    <div class="password-wrapper">
        <input type="password" id="password" name="password" placeholder="Masukkan Password" required>
        <span id="togglePassword" class="toggle-eye">&#128065;</span>
    </div>
        <input type="number" step="0.01" name="tb" placeholder="Tinggi Badan (cm)" required>
        <input type="number" step="0.01" name="bb" placeholder="Berat Badan (kg)" required>
        <button name="register">Daftar</button>
    </form>

    <p>Sudah punya akun? <a href="login.php">Login</a></p>
</div>
<script src="../public/js/script.js"></script>
</body>
</html>