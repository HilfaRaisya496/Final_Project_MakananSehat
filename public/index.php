<?php
session_start();

// Jika user sudah login, langsung masuk dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: ../dashboard/index.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>TriHealth - Selamat Datang</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #eaeaea;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .card {
            background: white;
            padding: 25px;
            width: 350px;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.15);
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
        }

        a button {
            width: 100%;
            padding: 12px;
            margin-top: 10px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }

        .login-btn { background: #4CAF50; color: white; }
        .login-btn:hover { background: #449d48; }

        .register-btn { background: #2196F3; color: white; }
        .register-btn:hover { background: #1976D2; }
    </style>
</head>
<body>

<div class="card">
    <h2>Selamat Datang di TriHealth</h2>

    <a href="../auth/login.php">
        <button class="login-btn">Login</button>
    </a>

    <a href="../auth/register.php">
        <button class="register-btn">Register</button>
    </a>
</div>

</body>
</html>
