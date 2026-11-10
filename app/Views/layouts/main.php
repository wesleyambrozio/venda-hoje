<?php
$themeMode = $user['theme_mode'] ?? 'dark';
$colorPalette = $user['color_palette'] ?? 'rose';
$uri = $_SERVER['REQUEST_URI'] ?? '';
$basePath = parse_url(config('app.url'), PHP_URL_PATH) ?: '';
$isActive = static function (string $needle) use ($uri, $basePath): bool {
    $path = $uri;
    if ($basePath && str_starts_with($path, $basePath)) {
        $path = substr($path, strlen($basePath)) ?: '/';
    }
    return str_contains($path, $needle) || ($needle === '/dashboard' && ($path === '/' || $path === ''));
};
?>
<!DOCTYPE html>
<html lang="pt-BR" data-rq-theme="venda_hoje" data-mode="<?= e($themeMode) ?>" data-palette="<?= e($colorPalette) ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Venda Hoje') ?> — <?= e(config('app.name')) ?></title>
    <script>
        (function () {
            try {
                var mode = localStorage.getItem('vh_mode') || document.documentElement.dataset.mode || 'dark';
                var palette = localStorage.getItem('vh_palette') || document.documentElement.dataset.palette || 'rose';
                document.documentElement.dataset.mode = mode;
                document.documentElement.dataset.palette = palette;
                document.documentElement.style.colorScheme = mode === 'light' ? 'light' : 'dark';
            } catch (e) {}
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="<?= asset('rq/rq.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/theme.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/app.css') ?>">
</head>
<body class="rq-app rq-mode-<?= e($themeMode) ?>"
      data-theme-save-url="<?= e(url('/config/tema')) ?>"
      data-csrf="<?= e($csrf) ?>"
      data-base-url="<?= e(rtrim(config('app.url'), '/')) ?>">
<div class="rq-shell app-layout">
    <aside class="rq-sidebar sidebar">
        <div class="rq-brand sidebar-brand">
            <span class="rq-brand__mark">VH</span>
            <span>Venda <strong>Hoje</strong></span>
        </div>
        <nav class="rq-nav sidebar-nav">
            <a href="<?= url('/dashboard') ?>" class="nav-item <?= $isActive('/dashboard') || $isActive('/') ? 'active' : '' ?>" <?= $isActive('/dashboard') ? 'aria-current="page"' : '' ?>><span>Dashboard</span></a>
            <a href="<?= url('/leads') ?>" class="nav-item <?= $isActive('/leads') ? 'active' : '' ?>" <?= $isActive('/leads') ? 'aria-current="page"' : '' ?>><span>Leads</span></a>
            <a href="<?= url('/historico') ?>" class="nav-item <?= $isActive('/historico') ? 'active' : '' ?>"><span>Histórico</span></a>
            <a href="<?= url('/followups') ?>" class="nav-item <?= $isActive('/followups') ? 'active' : '' ?>"><span>Follow-ups</span></a>
            <a href="<?= url('/gatilhos') ?>" class="nav-item <?= $isActive('/gatilhos') ? 'active' : '' ?>"><span>Gatilhos</span></a>
            <?php if ($user && in_array($user['perfil'], ['gestor', 'admin_saas'], true)): ?>
            <a href="<?= url('/config') ?>" class="nav-item <?= $isActive('/config') ? 'active' : '' ?>"><span>Config</span></a>
            <?php endif; ?>
            <?php if ($user && $user['perfil'] === 'admin_saas'): ?>
            <a href="<?= url('/admin/empresas') ?>" class="nav-item <?= $isActive('/admin') ? 'active' : '' ?>"><span>Empresas</span></a>
            <?php endif; ?>
        </nav>
        <div class="sidebar-footer">
            <strong><?= e($user['nome'] ?? '') ?></strong><br>
            <small><?= perfilLabel($user['perfil'] ?? '') ?> · <?= e($user['empresa_nome'] ?? '') ?></small>
            <form method="POST" action="<?= url('/logout') ?>" class="mt-1">
                <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                <button type="submit" class="btn btn-sm btn-outline" style="margin-top:.5rem;">Sair</button>
            </form>
        </div>
    </aside>
    <div class="rq-main main-content">
        <header class="rq-topbar topbar">
            <div>
                <p class="rq-kicker">CRM Comercial</p>
                <h1 class="page-title"><?= e($title ?? '') ?></h1>
            </div>
            <div class="flex gap-1 items-center flex-wrap topbar-actions">
                <div class="theme-controls" aria-label="Controles de tema">
                    <button type="button" class="rq-button btn btn-sm btn-outline" id="rq-theme-toggle" title="Alternar claro/escuro">Tema</button>
                    <select id="rq-palette-select" class="form-control form-control-sm" title="Paleta de cores" aria-label="Paleta">
                        <?php foreach (\App\Services\ThemeService::paletteLabels() as $key => $label): ?>
                            <option value="<?= e($key) ?>" <?= $colorPalette === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="button" class="rq-button btn btn-sm btn-outline" id="rq-voice-header-toggle" title="Comandos de voz">Voz</button>
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

<script type="application/json" id="rq-voice-commands"><?= json_encode([
    ['phrase' => 'abrir dashboard', 'url' => url('/dashboard')],
    ['phrase' => 'abrir leads', 'url' => url('/leads')],
    ['phrase' => 'abrir funil', 'url' => url('/leads?view=funil')],
    ['phrase' => 'abrir listagem', 'url' => url('/leads?view=lista')],
    ['phrase' => 'novo lead', 'url' => url('/leads/novo')],
    ['phrase' => 'abrir follow ups', 'url' => url('/followups')],
    ['phrase' => 'abrir gatilhos', 'url' => url('/gatilhos')],
    ['phrase' => 'abrir historico', 'url' => url('/historico')],
    ['phrase' => 'abrir configuracoes', 'url' => url('/config')],
    ['phrase' => 'alternar tema', 'action' => 'toggleTheme'],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>

<rq-voice-orb></rq-voice-orb>
<script type="module" src="<?= asset('js/rq-boot.js') ?>"></script>
<script src="<?= asset('js/app.js') ?>?v=<?= filemtime(BASE_PATH . '/public/assets/js/app.js') ?>"></script>
</body>
</html>
