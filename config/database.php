<?php
$host = 'postgres';
$db   = 'calculator';
$user = 'root';
$pass = 'root';
$port = 5432;

$dsn = "pgsql:host=$host;port=$port;dbname=$db";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
];

try {
    $pdo = new PDO($dsn, $user, $pass, $options);
    return $pdo;
} catch (\PDOException $e) {
    die("Ошибка подключения: " . $e->getMessage());
}
