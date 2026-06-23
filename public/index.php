<?php

declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));

require BASE_PATH . '/vendor/autoload.php';

$dotenv = Dotenv\Dotenv::createImmutable(BASE_PATH);
if (file_exists(BASE_PATH . '/.env')) {
    $dotenv->load();
}

date_default_timezone_set(config('app.timezone'));

$sessionConfig = require BASE_PATH . '/config/auth.php';
session_name($sessionConfig['session_name']);
session_save_path(BASE_PATH . '/storage/sessions');
session_start();

$router = new App\Core\Router();
require BASE_PATH . '/config/routes.php';

$uri = $_SERVER['REQUEST_URI'] ?? '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';
$router->dispatch($uri, $method);
