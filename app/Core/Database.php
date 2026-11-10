<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?PDO $instance = null;

    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $config = require BASE_PATH . '/config/database.php';

            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                $config['host'],
                $config['port'],
                $config['database']
            );

            try {
                self::$instance = new PDO($dsn, $config['username'], $config['password'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
            } catch (PDOException $e) {
                $debug = (bool) (($_ENV['APP_DEBUG'] ?? $_SERVER['APP_DEBUG'] ?? false) === true
                    || ($_ENV['APP_DEBUG'] ?? '') === 'true');
                $message = $debug
                    ? 'Erro de conexão com o banco: ' . $e->getMessage()
                    : 'Erro de conexão com o banco de dados.';
                throw new PDOException($message, (int) $e->getCode());
            }
        }

        return self::$instance;
    }
}
