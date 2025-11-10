<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'new_schema');
define('DB_USER', 'root');
define('DB_PASS', '');

// Create connection
try {
    $pdo = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

// Website configuration
define('SITE_NAME', 'Capstone 2025 Team 3');
define('SITE_URL', 'http://localhost/capstone');

?>
