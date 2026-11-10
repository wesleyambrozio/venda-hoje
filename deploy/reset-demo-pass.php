<?php
require __DIR__ . '/../vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(dirname(__DIR__));
$dotenv->load();
define('BASE_PATH', dirname(__DIR__));

$pdo = App\Core\Database::getInstance();
$hash = password_hash('password123', PASSWORD_DEFAULT);
$stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE email LIKE '%@vendahoje.com'");
$stmt->execute([$hash]);
echo 'updated=' . $stmt->rowCount() . PHP_EOL;
