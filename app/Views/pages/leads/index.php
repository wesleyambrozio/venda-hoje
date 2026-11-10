<?php
$query = array_filter([
    'busca' => $filters['busca'] ?? '',
    'etapa' => $filters['etapa'] ?? '',
    'origem' => $filters['origem'] ?? '',
    'responsavel' => $filters['responsavel'] ?? '',
]);
$queryLista = http_build_query(array_merge($query, ['view' => 'lista']));
$queryFunil = http_build_query(array_merge($query, ['view' => 'funil']));
?>
<div class="flex justify-between items-center mb-2 flex-wrap gap-1">
    <div class="view-toggle flex gap-1">
        <a href="<?= url('/leads?' . $queryLista) ?>" class="btn btn-sm <?= $viewMode === 'lista' ? 'btn-primary' : 'btn-outline' ?>">Listagem</a>
        <a href="<?= url('/leads?' . $queryFunil) ?>" class="btn btn-sm <?= $viewMode === 'funil' ? 'btn-primary' : 'btn-outline' ?>">Funil</a>
    </div>
    <?php if ($viewMode === 'funil' && in_array($user['perfil'], ['gestor', 'admin_saas'], true)): ?>
    <a href="<?= url('/config/funil') ?>" class="btn btn-sm btn-outline" title="Configurar etapas do funil">⚙️ Etapas</a>
    <?php endif; ?>
    <a href="<?= url('/leads/novo') ?>" class="btn btn-primary">+ Novo Lead</a>
</div>

<form method="GET" class="filters-bar">
    <input type="hidden" name="view" value="<?= e($viewMode) ?>">
    <div class="form-group">
        <label class="form-label">Busca</label>
        <input type="text" name="busca" class="form-control" value="<?= e($filters['busca'] ?? '') ?>" placeholder="Nome, email...">
    </div>
    <div class="form-group">
        <label class="form-label">Etapa</label>
        <select name="etapa" class="form-control">
            <option value="">Todas</option>
            <?php foreach ($etapasFunil as $etapa): ?>
                <option value="<?= e($etapa['chave']) ?>" <?= ($filters['etapa'] ?? '') === $etapa['chave'] ? 'selected' : '' ?>><?= e($etapa['rotulo']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label">Origem</label>
        <input type="text" name="origem" class="form-control" value="<?= e($filters['origem'] ?? '') ?>">
    </div>
    <?php if (!empty($comerciais)): ?>
    <div class="form-group">
        <label class="form-label">Responsável</label>
        <select name="responsavel" class="form-control">
            <option value="">Todos</option>
            <?php foreach ($comerciais as $c): ?>
                <option value="<?= $c['id'] ?>" <?= ($filters['responsavel'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['nome']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php endif; ?>
    <button type="submit" class="btn btn-secondary">Filtrar</button>
</form>

<?php if ($viewMode === 'funil'): ?>
<div class="kanban-board" id="kanban-board" data-csrf="<?= e($csrf) ?>" data-etapa-url="<?= e(url('/leads')) ?>">
    <?php foreach ($etapasFunil as $etapa): ?>
        <?php $coluna = $leadsPorEtapa[$etapa['chave']] ?? []; ?>
        <section class="kanban-column" data-etapa="<?= e($etapa['chave']) ?>">
            <header class="kanban-column__head">
                <strong><?= e($etapa['rotulo']) ?></strong>
                <span class="badge badge-muted"><?= count($coluna) ?></span>
            </header>
            <div class="kanban-column__body" data-dropzone="<?= e($etapa['chave']) ?>">
                <?php foreach ($coluna as $lead): ?>
                    <article class="kanban-card"
                             draggable="true"
                             data-lead-id="<?= (int) $lead['id'] ?>"
                             data-etapa="<?= e($lead['etapa_funil']) ?>">
                        <a href="<?= url('/leads/' . $lead['id']) ?>" class="kanban-card__title"><?= e($lead['nome']) ?></a>
                        <p class="text-muted"><?= e($lead['empresa_nome'] ?? $lead['origem'] ?? '—') ?></p>
                        <div class="flex justify-between items-center mt-1">
                            <small><?= e($lead['sdr_nome'] ?? '') ?></small>
                            <?php if ($lead['ultimo_score']): ?>
                                <span class="badge badge-<?= scoreClassificacao((float)$lead['ultimo_score']) ?>"><?= number_format((float)$lead['ultimo_score'], 0) ?></span>
                            <?php endif; ?>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endforeach; ?>
</div>
<?php else: ?>
<div class="card">
    <?php if (empty($leads)): ?>
        <div class="empty-state">Nenhum lead encontrado.</div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Nome</th>
                    <th>Empresa</th>
                    <th>Etapa</th>
                    <th>Origem</th>
                    <th>SDR</th>
                    <th>Closer</th>
                    <th>Score</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($leads as $lead): ?>
                <tr>
                    <td><strong><?= e($lead['nome']) ?></strong></td>
                    <td><?= e($lead['empresa_nome'] ?? '-') ?></td>
                    <td><span class="badge badge-primary"><?= e($funilLabels[$lead['etapa_funil']] ?? funilLabel($lead['etapa_funil'])) ?></span></td>
                    <td><?= e($lead['origem'] ?? '-') ?></td>
                    <td><?= e($lead['sdr_nome'] ?? '-') ?></td>
                    <td><?= e($lead['closer_nome'] ?? '-') ?></td>
                    <td>
                        <?php if ($lead['ultimo_score']): ?>
                            <span class="badge badge-<?= scoreClassificacao((float)$lead['ultimo_score']) ?>"><?= number_format((float)$lead['ultimo_score'], 0) ?></span>
                        <?php else: ?>-<?php endif; ?>
                    </td>
                    <td><a href="<?= url('/leads/' . $lead['id']) ?>" class="btn btn-sm btn-outline">Ver</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
<?php endif; ?>
