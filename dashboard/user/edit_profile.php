<?php
session_start();
include "../../config/config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../../auth/login.php");
    exit;
}

$id = $_SESSION['user_id'];

// Ambil data user
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id=$id");
$user  = mysqli_fetch_assoc($query);

// Mode edit?
$edit_mode = isset($_GET['edit']);

// Proses update
if (isset($_POST['update'])) {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $tb       = $_POST['tb'];
    $bb       = $_POST['bb'];

    // Kalau password diisi → update password
    if (!empty($_POST['password'])) {
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $sql = "UPDATE users SET 
                username='$username', 
                email='$email',
                password='$password',
                tb='$tb',
                bb='$bb'
                WHERE id=$id";
    } else {
        $sql = "UPDATE users SET 
                username='$username', 
                email='$email',
                tb='$tb',
                bb='$bb'
                WHERE id=$id";
    }

    if (mysqli_query($koneksi, $sql)) {
        header("Location: editprofile_succes.php");
        exit;
    } else {
        $error = "Gagal memperbarui profil!";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Profil Saya</title>
    <style>
        .profile-box {
            background: white;
            max-width: 400px;
            margin: 80px auto;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }

        .profile-box h2 {
            text-align: center;
        }

        .data {
            margin: 10px 0;
            font-size: 15px;
        }

        .data span {
            font-weight: bold;
        }

        .btn {
            width: 100%;
            padding: 12px;
            margin-top: 15px;
            border-radius: 6px;
            border: none;
            font-size: 14px;
            cursor: pointer;
        }

        .edit {
            background: #7cb342;
            color: white;
        }

        .save {
            background: #7cb342;
            color: white;
        }

        input {
            width: 95%;
            padding: 10px;
            margin-top: 6px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .error {
            background: #ffdada;
            padding: 10px;
            margin-bottom: 10px;
            border-left: 5px solid red;
        }

        label {
            margin-top: 10px;
            display: block;
            font-weight: bold;
        }

        a {
            text-decoration: none;
            color: #ef6c00;
        }
    </style>
</head>
<body>

<div class="profile-box">
    <h2>Profil Saya</h2>

    <?php if (isset($error)) { ?>
        <div class="error"><?= $error ?></div>
    <?php } ?>

    <?php if (!$edit_mode) { ?>
        <!-- MODE LIHAT -->
        <div class="data"><span>Username:</span> <?= $user['username'] ?></div>
        <div class="data"><span>Email:</span> <?= $user['email'] ?></div>
        <div class="data"><span>Tinggi Badan:</span> <?= $user['tb'] ?> cm</div>
        <div class="data"><span>Berat Badan:</span> <?= $user['bb'] ?> kg</div>

        <a href="?edit=true">
            <button class="btn edit">Edit Profil</button>
        </a>

    <?php } else { ?>
        <!-- MODE EDIT -->
        <form method="POST">
            <label>Username</label>
            <input type="text" name="username" value="<?= $user['username'] ?>" required>

            <label>Email</label>
            <input type="email" name="email" value="<?= $user['email'] ?>" required>

            <label>Password Baru (opsional)</label>
            <input type="password" name="password" placeholder="Kosongkan jika tidak ingin mengubah">

            <label>Tinggi Badan (cm)</label>
            <input type="number" step="0.01" name="tb" value="<?= $user['tb'] ?>" required>

            <label>Berat Badan (kg)</label>
            <input type="number" step="0.01" name="bb" value="<?= $user['bb'] ?>" required>

            <button type="submit" name="update" class="btn save">Simpan Perubahan</button>
        </form>
    <?php } ?>

    <p style="text-align:center; margin-top:10px;">
        <a href="user.php">← Kembali ke Dashboard</a>
    </p>
</div>

</body>
</html>
