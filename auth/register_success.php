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
    <link rel="stylesheet" href="../public/css/style.css">

    <style>
        .success-box {
            width: 350px;
            margin: 80px auto;
            padding: 25px;
            text-align: center;
            border-radius: 10px;
            background: #e8fff1;
            border: 2px solid #2ecc71;
            font-family: Arial;
        }
        .success-box h2 {
            color: #2ecc71;
        }
        .success-box p {
            margin-top: 10px;
            font-size: 14px;
        }
        .success-box a {
            display: inline-block;
            margin-top: 15px;
            padding: 10px 20px;
            text-decoration: none;
            color: white;
            background: #2ecc71;
            border-radius: 5px;
        }
    </style>

</head>
<body>

<div class="success-box">
    <h2>Registrasi Berhasil!</h2>

    <a href="login.php">Pergi ke Login Sekarang</a>
</div>

</body>
</html>
