<!DOCTYPE html>
<html lang="pt-BR" data-rq-theme="venda_hoje" data-mode="dark" data-palette="rose">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e($title ?? 'Login') ?> — <?= e(config('app.name')) ?></title>
    <script>
        (function () {
            try {
                var mode = localStorage.getItem('vh_mode') || 'dark';
                var palette = localStorage.getItem('vh_palette') || 'rose';
                document.documentElement.dataset.mode = mode;
                document.documentElement.dataset.palette = palette;
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
<body class="rq-app">
<div class="login-page">
    <div class="login-card rq-glass">
        <div class="login-brand">
            <div class="rq-brand" style="justify-content:center;margin-bottom:1rem;">
                <span class="rq-brand__mark">VH</span>
                <span>Venda <strong>Hoje</strong></span>
            </div>
            <p class="rq-muted">CRM de Atendimento Comercial</p>
        </div>
        <?php foreach ($flash as $type => $message): ?>
            <div class="alert alert-<?= $type === 'error' ? 'error' : e($type) ?>"><?= e($message) ?></div>
        <?php endforeach; ?>
        <?= $content ?>
    </div>
</div>
<script type="module">
import { applyTheme } from '<?= asset('rq/themes.js') ?>';
import { syncRqTheme } from '<?= asset('js/rq-theme.js') ?>';
applyTheme('venda_hoje');
syncRqTheme({
  mode: document.documentElement.dataset.mode || 'dark',
  palette: document.documentElement.dataset.palette || 'rose'
});
</script>
</body>
</html>
