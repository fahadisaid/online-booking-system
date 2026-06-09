<?php
$db_host = 'localhost';
$db_user = 'root';
// $port = 3307;
$db_pass = '';
$db_name = 'booking_db';

$dsn = "mysql:host=$db_host;dbname=$db_name;charset=utf8mb4";

try {
    $conn = new PDO($dsn, $db_user, $db_pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    die('Database connection failed: ' . $e->getMessage());
}
