<div class="stats-grid">
    <a href="<?= url('/config/usuarios') ?>" class="stat-card" style="text-decoration:none; color:inherit;">
        <div class="stat-value">👤</div>
        <div class="stat-label">Gerenciar Usuários</div>
    </a>
    <a href="<?= url('/config/gatilhos') ?>" class="stat-card secondary" style="text-decoration:none; color:inherit;">
        <div class="stat-value">💬</div>
        <div class="stat-label">Editar Gatilhos</div>
    </a>
    <a href="<?= url('/config/criterios') ?>" class="stat-card warning" style="text-decoration:none; color:inherit;">
        <div class="stat-value">📊</div>
        <div class="stat-label">Critérios Diagnóstico</div>
    </a>
    <a href="<?= url('/config/funil') ?>" class="stat-card" style="text-decoration:none; color:inherit;">
        <div class="stat-value">⚙️</div>
        <div class="stat-label">Configurar Funil</div>
    </a>
    <a href="<?= url('/config/aparencia') ?>" class="stat-card" style="text-decoration:none; color:inherit;">
        <div class="stat-value">🎨</div>
        <div class="stat-label">Aparência (tema/paleta)</div>
    </a>
</div>

<div class="card">
    <div class="card-header"><span class="card-title">Usuários da Empresa (<?= count($usuarios) ?>)</span></div>
    <table>
        <thead><tr><th>Nome</th><th>Email</th><th>Perfil</th><th>Status</th></tr></thead>
        <tbody>
        <?php foreach ($usuarios as $u): ?>
            <tr>
                <td><?= e($u['nome']) ?></td>
                <td><?= e($u['email']) ?></td>
                <td><span class="badge badge-primary"><?= perfilLabel($u['perfil']) ?></span></td>
                <td><span class="badge badge-<?= $u['ativo'] ? 'success' : 'danger' ?>"><?= $u['ativo'] ? 'Ativo' : 'Inativo' ?></span></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
