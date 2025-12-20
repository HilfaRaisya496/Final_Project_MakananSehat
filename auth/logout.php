<?php
require __DIR__ . '/../app/core/bootstrap.php';

session_destroy();
header('Location: login.php');
exit;
?>