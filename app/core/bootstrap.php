<?php
session_start();

require __DIR__ . '/../../vendor/autoload.php';
require __DIR__ . '/../../config/env.php';
require __DIR__ . '/../../config/database.php';

function db(): PDO {
    return Database::connection();
}


