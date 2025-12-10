<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: ../auth/logout.php");
    exit;
}
?>

<a href="edit_profile.php">Lihat Profile</a>

<h2>Dashboard User</h2>
