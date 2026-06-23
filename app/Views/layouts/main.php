<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Venda Hoje') ?> — <?= e(config('app.name')) ?></title>
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body>
<div class="app-layout">
    <aside class="sidebar">
        <div class="sidebar-brand">
            🔥 <span>Venda</span> Hoje
        </div>
        <nav class="sidebar-nav">
            <a href="<?= url('/dashboard') ?>" class="nav-item <?= str_contains($_SERVER['REQUEST_URI'] ?? '', 'dashboard') || ($_SERVER['REQUEST_URI'] ?? '') === parse_url(config('app.url'), PHP_URL_PATH) . '/' ? 'active' : '' ?>">📊 Dashboard</a>
            <a href="<?= url('/leads') ?>" class="nav-item <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/leads') ? 'active' : '' ?>">👥 Leads</a>
            <a href="<?= url('/historico') ?>" class="nav-item <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/historico') ? 'active' : '' ?>">📋 Histórico</a>
            <a href="<?= url('/followups') ?>" class="nav-item <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/followups') ? 'active' : '' ?>">⏰ Follow-ups</a>
            <a href="<?= url('/gatilhos') ?>" class="nav-item <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/gatilhos') ? 'active' : '' ?>">💬 Gatilhos</a>
            <?php if ($user && in_array($user['perfil'], ['gestor', 'admin_saas'])): ?>
            <a href="<?= url('/config') ?>" class="nav-item <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/config') ? 'active' : '' ?>">⚙️ Config</a>
            <?php endif; ?>
            <?php if ($user && $user['perfil'] === 'admin_saas'): ?>
            <a href="<?= url('/admin/empresas') ?>" class="nav-item <?= str_contains($_SERVER['REQUEST_URI'] ?? '', '/admin') ? 'active' : '' ?>">🏢 Empresas</a>
            <?php endif; ?>
        </nav>
        <div class="sidebar-footer">
            <?= e($user['nome'] ?? '') ?><br>
            <small><?= perfilLabel($user['perfil'] ?? '') ?> · <?= e($user['empresa_nome'] ?? '') ?></small><br>
            <a href="<?= url('/logout') ?>" style="color: var(--secondary-light); margin-top: 0.5rem; display: inline-block;">Sair</a>
        </div>
    </aside>
    <div class="main-content">
        <header class="topbar">
            <h1><?= e($title ?? '') ?></h1>
            <div class="flex gap-1 items-center">
                <span class="badge badge-primary"><?= perfilLabel($user['perfil'] ?? '') ?></span>
            </div>
        </header>
        <main class="content">
            <?php foreach ($flash as $type => $message): ?>
                <div class="alert alert-<?= $type === 'error' ? 'error' : e($type) ?>"><?= e($message) ?></div>
            <?php endforeach; ?>
            <?= $content ?>
        </main>
    </div>
</div>
<script src="<?= asset('js/app.js') ?>"></script>
</body>
</html>
