<?php
require __DIR__ . '/vendor/autoload.php';
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();
define('BASE_PATH', __DIR__);

try {
    $pdo = App\Core\Database::getInstance();
    echo "DB OK\n";
    foreach ($pdo->query("SHOW COLUMNS FROM empresas")->fetchAll() as $c) {
        if (str_contains($c['Field'], 'theme') || str_contains($c['Field'], 'palette')) {
            echo "col: {$c['Field']}\n";
        }
    }
    $stmt = $pdo->prepare('SELECT u.email, u.ativo, e.ativo AS emp, e.theme_mode, e.color_palette, u.senha FROM usuarios u JOIN empresas e ON e.id=u.empresa_id WHERE u.email=?');
    $stmt->execute(['sdr@vendahoje.com']);
    $u = $stmt->fetch();
    if (!$u) {
        echo "USER NOT FOUND\n";
        exit(1);
    }
    echo "user={$u['email']} ativo={$u['ativo']} emp={$u['emp']} theme={$u['theme_mode']} palette={$u['color_palette']}\n";
    echo 'password_ok=' . (password_verify('password123', $u['senha']) ? 'yes' : 'no') . "\n";
} catch (Throwable $e) {
    echo 'ERR: ' . $e->getMessage() . "\n";
}
