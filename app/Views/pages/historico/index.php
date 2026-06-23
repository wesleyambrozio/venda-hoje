<form method="GET" class="filters-bar">
    <div class="form-group">
        <label class="form-label">Canal</label>
        <select name="canal" class="form-control">
            <option value="">Todos</option>
            <?php foreach (config('app.canais_atendimento') as $canal): ?>
                <option value="<?= $canal ?>" <?= ($filters['canal'] ?? '') === $canal ? 'selected' : '' ?>><?= ucfirst($canal) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label">Resultado</label>
        <select name="resultado" class="form-control">
            <option value="">Todos</option>
            <option value="concluido" <?= ($filters['resultado'] ?? '') === 'concluido' ? 'selected' : '' ?>>Concluído</option>
            <option value="follow_up" <?= ($filters['resultado'] ?? '') === 'follow_up' ? 'selected' : '' ?>>Follow-up</option>
            <option value="nao_atendeu" <?= ($filters['resultado'] ?? '') === 'nao_atendeu' ? 'selected' : '' ?>>Não Atendeu</option>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label">Lead</label>
        <select name="lead_id" class="form-control">
            <option value="">Todos</option>
            <?php foreach ($leads as $l): ?>
                <option value="<?= $l['id'] ?>" <?= ($filters['lead_id'] ?? '') == $l['id'] ? 'selected' : '' ?>><?= e($l['nome']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label">De</label>
        <input type="date" name="data_inicio" class="form-control" value="<?= e($filters['data_inicio'] ?? '') ?>">
    </div>
    <div class="form-group">
        <label class="form-label">Até</label>
        <input type="date" name="data_fim" class="form-control" value="<?= e($filters['data_fim'] ?? '') ?>">
    </div>
    <button type="submit" class="btn btn-secondary">Filtrar</button>
</form>

<div class="card">
    <?php if (empty($historico)): ?>
        <div class="empty-state">Nenhum atendimento encontrado.</div>
    <?php else: ?>
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Lead</th>
                    <th>Canal</th>
                    <th>Resultado</th>
                    <th>Etapa</th>
                    <th>Resumo</th>
                    <th>Atendente</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($historico as $h): ?>
                <tr>
                    <td><?= formatDateTime($h['data_atendimento']) ?></td>
                    <td><a href="<?= url('/leads/' . $h['lead_id']) ?>"><?= e($h['lead_nome']) ?></a></td>
                    <td><?= ucfirst(e($h['canal'])) ?></td>
                    <td><span class="badge badge-<?= $h['resultado'] === 'follow_up' ? 'warning' : 'success' ?>"><?= e($h['resultado']) ?></span></td>
                    <td><?= funilLabel($h['etapa_funil']) ?></td>
                    <td><?= e(mb_substr($h['resumo'] ?? '-', 0, 60)) ?></td>
                    <td><?= e($h['usuario_nome']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <?php endif; ?>
</div>
