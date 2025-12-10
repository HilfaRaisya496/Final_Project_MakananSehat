<?php
session_start();
include "../config/config.php";

if (!isset($_SESSION['user_id'])) {
    header("Location: ../auth/login.php");
    exit;
}

$id = $_SESSION['user_id'];

// Ambil data user
$query = mysqli_query($koneksi, "SELECT * FROM users WHERE id = $id");
$user  = mysqli_fetch_assoc($query);

// Proses update data
if (isset($_POST['update'])) {
    $username = $_POST['username'];
    $email    = $_POST['email'];
    $password = $_POST['password'];
    $tb       = $_POST['tb'];
    $bb       = $_POST['bb'];

    // Kalau password tidak diubah
    if (empty($password)) {
        $password_update = $user['password'];
    } else {
        // Hash password baru
        $password_update = password_hash($password, PASSWORD_DEFAULT);
    }

    // Query update
    $update = mysqli_query($koneksi, "
        UPDATE users SET
            username = '$username',
            email    = '$email',
            password = '$password_update',
            tb       = '$tb',
            bb       = '$bb'
        WHERE id = $id
    ");

    if ($update) {
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
    <title>Edit Profile</title>
    <link rel="stylesheet" href="../public/css/style.css">
</head>
<body>

<div class="box">
    <h2>Edit Profile</h2>

    <?php if (isset($error)) { ?>
        <div class="error"><?= $error ?></div>
    <?php } ?>

    <form method="POST">

        <label>Username</label>
        <input type="text" name="username" value="<?= $user['username'] ?>" required>

        <label>Email</label>
        <input type="email" name="email" value="<?= $user['email'] ?>" required>

        <label>Password Baru (kosongkan jika tidak ingin ganti)</label>
        <input type="password" name="password" placeholder="Password baru">

        <label>Tinggi Badan (cm)</label>
        <input type="number" step="0.01" name="tb" value="<?= $user['tb'] ?>" required>

        <label>Berat Badan (kg)</label>
        <input type="number" step="0.01" name="bb" value="<?= $user['bb'] ?>" required>

        <button type="submit" name="update">Update Profile</button>
    </form>

    <p style="text-align:center; margin-top:10px;">
        <a href="../dashboard/user_dashboard.php">← Kembali ke Dashboard</a>
    </p>
</div>

</body>
</html>
