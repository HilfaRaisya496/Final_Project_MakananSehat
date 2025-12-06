<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit;
}
?>

<h2>Dashboard Admin</h2>
<p>Halo, <?php echo $_SESSION['user']; ?></p>
<a href="../auth/logout.php">Logout</a>
