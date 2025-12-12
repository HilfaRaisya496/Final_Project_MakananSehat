<?php
session_start();
require_once "../config/config.php";

if (isset($_POST['login'])) {

    $username = $_POST['username'];
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE username='$username'";
    $result = mysqli_query($koneksi, $query);

    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);

        // Cek password dengan password_verify
        if (password_verify($password, $user['password'])) {


            $_SESSION['user_id']  = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role']     = $user['role'];


        if ($user['role'] == 'admin') {
            header("Location: ../dashboard/admin/admin.php");
            exit;
        } else {
            header("Location: ../dashboard/user/user.php");
            exit;
        }

        } else {
            $error = "Password salah!";
        }

    } else {
        $error = "Username tidak ditemukan!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="../public/css/auth.css">
</head>
<body>

<div class="box">
    <img src="../public/img/logo.jpg" class="logo">
    <h2>Login</h2>

    <?php if (isset($error)) { ?>
        <div class="error"><?= $error ?></div>
    <?php } ?>

    <form action="" method="POST">
        <input type="text" name="username" placeholder="Masukkan Username" required>
        <div class="password-wrapper">
            <input type="password" id="password" name="password" placeholder="Masukkan Password" required>
            <span id="togglePassword" class="toggle-eye">&#128065;</span>
        </div>

        <button type="submit" name="login">Login</button>
    </form>

    <p style="text-align:center; margin-top:10px;">
        Belum punya akun? <a href="register.php">Register</a>
    </p>
</div>
<script src="../public/js/script.js"></script>
</body>
</html>