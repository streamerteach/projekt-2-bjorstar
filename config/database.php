<?php
// Database connection settings
$host = "mysql.arcada.fi";
$dbname = "bjorstar";
$user = "bjorstar";
$pass = "9N77G8U9Ax";

try {
    // Create PDO connection
    $conn = new PDO(
        "mysql:host=$host;dbname=$dbname;charset=utf8mb4",
        $user,
        $pass
    );
    // Show errors as exceptions
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}