<?php
session_start();

if (!isset($_SESSION['user'])) {
    header("Location: ../auth/logout.php");
    exit;
}
?>

<h2>Dashboard User</h2>
