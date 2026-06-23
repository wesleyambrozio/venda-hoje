<?php if (!empty($alertas)): ?>
<div class="card" style="border-left: 4px solid var(--warning);">
    <div class="card-header">
        <span class="card-title">🔔 Alertas — Follow-ups Atrasados (<?= count($alertas) ?>)</span>
    </div>
    <table>
        <thead><tr><th>Lead</th><th>Retorno</th><th>Gatilho Sugerido</th><th>Observação</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($alertas as $a): ?>
            <tr>
                <td><a href="<?= url('/leads/' . $a['lead_id']) ?>"><strong><?= e($a['lead_nome']) ?></strong></a></td>
                <td><span class="badge badge-danger"><?= formatDateTime($a['data_retorno']) ?></span></td>
                <td>
                    <?php if ($a['gatilho_titulo']): ?>
                        <strong><?= e($a['gatilho_titulo']) ?></strong><br>
                        <small class="text-muted"><?= e(mb_substr($a['gatilho_texto'] ?? '', 0, 80)) ?></small>
                    <?php else: ?>-<?php endif; ?>
                </td>
                <td><?= e($a['observacao'] ?? '-') ?></td>
                <td>
                    <a href="<?= url('/leads/' . $a['lead_id'] . '/atendimento') ?>" class="btn btn-sm btn-primary">Atender</a>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>

<div class="flex gap-1 mb-2">
    <a href="<?= url('/followups?status=pendente') ?>" class="btn btn-sm <?= $status === 'pendente' ? 'btn-primary' : 'btn-outline' ?>">Pendentes</a>
    <a href="<?= url('/followups?status=concluido') ?>" class="btn btn-sm <?= $status === 'concluido' ? 'btn-primary' : 'btn-outline' ?>">Concluídos</a>
    <a href="<?= url('/followups?status=todos') ?>" class="btn btn-sm <?= $status === 'todos' ? 'btn-primary' : 'btn-outline' ?>">Todos</a>
</div>

<div class="card">
    <?php if (empty($followups)): ?>
        <div class="empty-state">Nenhum follow-up encontrado.</div>
    <?php else: ?>
    <table>
        <thead>
            <tr>
                <th>Lead</th>
                <th>Retorno</th>
                <th>Status</th>
                <th>Gatilho Sugerido</th>
                <th>Responsável</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($followups as $f): ?>
            <tr>
                <td><a href="<?= url('/leads/' . $f['lead_id']) ?>"><?= e($f['lead_nome']) ?></a></td>
                <td><?= formatDateTime($f['data_retorno']) ?></td>
                <td><span class="badge badge-<?= $f['status'] === 'pendente' ? 'warning' : 'success' ?>"><?= e($f['status']) ?></span></td>
                <td><?= e($f['gatilho_titulo'] ?? '-') ?></td>
                <td><?= e($f['usuario_nome']) ?></td>
                <td>
                    <?php if ($f['status'] === 'pendente'): ?>
                    <form method="POST" action="<?= url('/followups/' . $f['id'] . '/concluir') ?>" style="display:flex; gap:0.25rem; align-items:center;">
                        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                        <select name="gatilho_funcionou" class="form-control" style="width:auto; font-size:0.8rem;">
                            <option value="">Gatilho funcionou?</option>
                            <option value="1">Sim</option>
                            <option value="0">Não</option>
                        </select>
                        <button type="submit" class="btn btn-sm btn-success" style="background:var(--success);color:#fff;">Concluir</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
    <?php endif; ?>
</div>
