<?php

declare(strict_types=1);

/**
 * Aplica migrations SQL em ordem.
 * Uso: php database/migrate.php
 */

define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
if (file_exists(BASE_PATH . '/.env')) {
    $dotenv->load();
}

$config = require BASE_PATH . '/config/database.php';
$dsn = sprintf(
    'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
    $config['host'],
    $config['port'],
    $config['database']
);

$pdo = new PDO($dsn, $config['username'], $config['password'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

$pdo->exec(
    'CREATE TABLE IF NOT EXISTS schema_migrations (
        id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
        filename VARCHAR(255) NOT NULL UNIQUE,
        applied_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    ) ENGINE=InnoDB'
);

$dir = BASE_PATH . '/database/migrations';
$files = glob($dir . '/*.sql');
sort($files);

foreach ($files as $file) {
    $name = basename($file);
    $stmt = $pdo->prepare('SELECT id FROM schema_migrations WHERE filename = ?');
    $stmt->execute([$name]);
    if ($stmt->fetch()) {
        echo "Skip: {$name}\n";
        continue;
    }

    $sql = file_get_contents($file);
    // Remove USE statements to respect configured database
    $sql = preg_replace('/^\s*USE\s+\w+\s*;/mi', '', $sql);
    $pdo->exec($sql);
    $ins = $pdo->prepare('INSERT INTO schema_migrations (filename) VALUES (?)');
    $ins->execute([$name]);
    echo "Applied: {$name}\n";
}

echo "Migrations OK\n";
