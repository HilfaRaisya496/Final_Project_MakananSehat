<?php
use Dotenv\Dotenv;

if (class_exists(Dotenv::class)) {
    $dotenv = Dotenv::createImmutable(__DIR__ . '/..'); // .. = folder TES2
    $dotenv->load();
}
