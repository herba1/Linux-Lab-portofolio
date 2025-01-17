<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

$autoloadPath = __DIR__ . '/vendor/autoload.php';

if (!file_exists($autoloadPath)) {
    die("Autoload file not found at: $autoloadPath");
}

try {
    require $autoloadPath;
    echo "Autoload file included successfully!";
} catch (Throwable $e) {
    echo "Error including autoload.php: " . $e->getMessage();
}

