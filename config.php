<?php
// config.php
// Central configuration file for database connection.
// This file is included by most PHP scripts that need DB access.

// NOTE FOR VIVA:
// Keeping configuration in one file avoids duplication and makes
// it easy to change database credentials in one place.

$DB_HOST = 'localhost';
$DB_NAME = 'work_reminder_db'; // Change if you named your DB differently
$DB_USER = 'root';             // Default XAMPP user
$DB_PASS = '';                 // Default XAMPP password (empty)

// Create a PDO connection in a try/catch block for safety.
try {
    $dsn = "mysql:host=$DB_HOST;dbname=$DB_NAME;charset=utf8mb4";
    $pdo = new PDO($dsn, $DB_USER, $DB_PASS, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    // In a production system we would log the error instead of showing it.
    die("Database connection failed: " . htmlspecialchars($e->getMessage()));
}

