<?php

/**
 * Setup do banco de dados — execute via CLI:
 * php database/setup.php
 */

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
require BASE_PATH . '/vendor/autoload.php';

if (file_exists(BASE_PATH . '/.env')) {
    Dotenv\Dotenv::createImmutable(BASE_PATH)->load();
}

$config = require BASE_PATH . '/config/database.php';

try {
    $pdo = new PDO(
        sprintf('mysql:host=%s;port=%s;charset=utf8mb4', $config['host'], $config['port']),
        $config['username'],
        $config['password'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    $migration = file_get_contents(BASE_PATH . '/database/migrations/001_create_schema.sql');
    $pdo->exec($migration);
    echo "✓ Migration executada\n";

    $seed = file_get_contents(BASE_PATH . '/database/seeds/001_seed_data.sql');
    $pdo->exec($seed);
    echo "✓ Seed executado\n";

    echo "\nBanco pronto! Login: sdr@vendahoje.com / password123\n";
} catch (PDOException $e) {
    echo "Erro: " . $e->getMessage() . "\n";
    exit(1);
}
