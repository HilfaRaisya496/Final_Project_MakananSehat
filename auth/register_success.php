<?php
// Jika user masuk ke halaman ini TANPA proses register, redirect ke register
if (!isset($_GET['status']) || $_GET['status'] !== "success") {
    header("Location: register.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Registrasi Berhasil</title>
    <link rel="stylesheet" href="../public/css/success_regist.css">
</head>
<body>

<div class="success-box">
    <h2>Registrasi Berhasil!</h2>

    <a href="login.php">Pergi ke Login Sekarang</a>
</div>

</body>
</html>
