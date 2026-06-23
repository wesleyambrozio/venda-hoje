<div class="flex gap-2 flex-wrap">
    <div class="card" style="flex: 1; min-width: 300px;">
        <div class="card-header"><span class="card-title">Novo Usuário</span></div>
        <form method="POST" action="<?= url('/config/usuarios') ?>">
            <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
            <div class="form-group">
                <label class="form-label">Nome</label>
                <input type="text" name="nome" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Senha</label>
                <input type="password" name="senha" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Perfil</label>
                <select name="perfil" class="form-control">
                    <option value="sdr">SDR</option>
                    <option value="closer">Closer</option>
                    <option value="gestor">Gestor</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Criar Usuário</button>
        </form>
    </div>

    <div class="card" style="flex: 2;">
        <div class="card-header"><span class="card-title">Usuários</span></div>
        <table>
            <thead><tr><th>Nome</th><th>Email</th><th>Perfil</th><th>Status</th></tr></thead>
            <tbody>
            <?php foreach ($usuarios as $u): ?>
                <tr>
                    <td><?= e($u['nome']) ?></td>
                    <td><?= e($u['email']) ?></td>
                    <td><?= perfilLabel($u['perfil']) ?></td>
                    <td><?= $u['ativo'] ? 'Ativo' : 'Inativo' ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<a href="<?= url('/config') ?>" class="btn btn-outline mt-2">← Voltar</a>
