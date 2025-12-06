<?php
include "../config/config.php";
// Buat database jika belum ada
$sql = "CREATE DATABASE IF NOT EXISTS $db";
if (mysqli_query($koneksi, $sql)) {

    // Pilih database
    mysqli_select_db($koneksi, $db);

    // Query membuat tabel users
    $create_table = "CREATE TABLE IF NOT EXISTS users (
        id INT(11) AUTO_INCREMENT PRIMARY KEY,
        username VARCHAR(50) NOT NULL UNIQUE,
        password VARCHAR(255) NOT NULL,
        tb FLOAT DEFAULT NULL,
        bb FLOAT DEFAULT NULL,
        role ENUM('admin', 'user') DEFAULT 'user',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    mysqli_query($koneksi, $create_table);
}
?>