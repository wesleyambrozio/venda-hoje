<div class="flex justify-between mb-2">
    <div></div>
    <a href="<?= url('/admin/empresas/nova') ?>" class="btn btn-primary">+ Nova Empresa</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Empresa</th>
                <th>CNPJ</th>
                <th>Plano</th>
                <th>Usuários</th>
                <th>Leads</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($empresas as $e): ?>
            <tr>
                <td><strong><?= e($e['nome']) ?></strong></td>
                <td><?= e($e['cnpj'] ?? '-') ?></td>
                <td><?= e($e['plano_nome'] ?? '-') ?> <?= $e['preco_mensal'] ? '(R$ ' . number_format((float)$e['preco_mensal'], 2, ',', '.') . ')' : '' ?></td>
                <td><?= $e['total_usuarios'] ?></td>
                <td><?= $e['total_leads'] ?></td>
                <td><span class="badge badge-<?= $e['ativo'] ? 'success' : 'danger' ?>"><?= $e['ativo'] ? 'Ativa' : 'Inativa' ?></span></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
