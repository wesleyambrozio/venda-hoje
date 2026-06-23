<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Login') ?> — <?= e(config('app.name')) ?></title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body>
<div class="login-page">
    <div class="login-card">
        <div class="login-brand">
            <h1>🔥 Venda <span style="color: var(--secondary)">Hoje</span></h1>
            <p>CRM de Atendimento Comercial</p>
        </div>
        <?php foreach ($flash as $type => $message): ?>
            <div class="alert alert-<?= $type === 'error' ? 'error' : e($type) ?>"><?= e($message) ?></div>
        <?php endforeach; ?>
        <?= $content ?>
    </div>
</div>
</body>
</html>
