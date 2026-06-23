<div class="flex justify-between items-center mb-2">
    <div></div>
    <a href="<?= url('/leads/novo') ?>" class="btn btn-primary">+ Novo Lead</a>
</div>

<form method="GET" class="filters-bar">
    <div class="form-group">
        <label class="form-label">Busca</label>
        <input type="text" name="busca" class="form-control" value="<?= e($filters['busca'] ?? '') ?>" placeholder="Nome, email...">
    </div>
    <div class="form-group">
        <label class="form-label">Etapa</label>
        <select name="etapa" class="form-control">
            <option value="">Todas</option>
            <?php foreach (config('app.funil_etapas') as $etapa): ?>
                <option value="<?= $etapa ?>" <?= ($filters['etapa'] ?? '') === $etapa ? 'selected' : '' ?>><?= funilLabel($etapa) ?></option>
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
                    <td><span class="badge badge-primary"><?= funilLabel($lead['etapa_funil']) ?></span></td>
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
