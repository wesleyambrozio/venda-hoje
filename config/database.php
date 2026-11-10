<?php

declare(strict_types=1);

// Nota: env() é definida em app/Helpers/functions.php (autoload PSR-4)
// A função existe após vendor/autoload.php ser carregado.

$host = $_ENV['DB_HOST'] ?? $_SERVER['DB_HOST'] ?? '127.0.0.1';
$port = $_ENV['DB_PORT'] ?? $_SERVER['DB_PORT'] ?? '3306';
$database = $_ENV['DB_DATABASE'] ?? $_SERVER['DB_DATABASE'] ?? 'venda_hoje';
$username = $_ENV['DB_USERNAME'] ?? $_SERVER['DB_USERNAME'] ?? 'root';
$password = $_ENV['DB_PASSWORD'] ?? $_SERVER['DB_PASSWORD'] ?? '';

return [
    'host' => $host,
    'port' => $port,
    'database' => $database,
    'username' => $username,
    'password' => $password,
];
