<?php if (!empty($alertas)): ?>
<div class="alert alert-warning">
    <strong>⚠️ <?= count($alertas) ?> follow-up(s) atrasado(s)!</strong>
    <a href="<?= url('/followups') ?>" style="margin-left: 1rem;">Ver alertas</a>
</div>
<?php endif; ?>

<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-value"><?= $metrics['total_leads'] ?></div>
        <div class="stat-label">Total Leads</div>
    </div>
    <div class="stat-card success">
        <div class="stat-value"><?= $metrics['fechados'] ?></div>
        <div class="stat-label">Fechados</div>
    </div>
    <div class="stat-card secondary">
        <div class="stat-value"><?= $metrics['conversao'] ?>%</div>
        <div class="stat-label">Conversão</div>
    </div>
    <div class="stat-card warning">
        <div class="stat-value"><?= $metrics['score_medio'] ?></div>
        <div class="stat-label">Score Médio</div>
    </div>
    <div class="stat-card">
        <div class="stat-value"><?= $metrics['followups_pendentes'] ?></div>
        <div class="stat-label">Follow-ups Pendentes</div>
    </div>
    <div class="stat-card secondary">
        <div class="stat-value"><?= $metrics['atendimentos_mes'] ?></div>
        <div class="stat-label">Atendimentos (Mês)</div>
    </div>
</div>

<div class="flex gap-2 flex-wrap">
    <div class="card" style="flex: 1; min-width: 300px;">
        <div class="card-header">
            <span class="card-title">Leads por Etapa</span>
        </div>
        <?php if (empty($metrics['leads_por_etapa'])): ?>
            <p class="text-muted">Nenhum lead cadastrado.</p>
        <?php else: ?>
            <table>
                <thead><tr><th>Etapa</th><th>Total</th></tr></thead>
                <tbody>
                <?php foreach ($metrics['leads_por_etapa'] as $item): ?>
                    <tr>
                        <td><?= funilLabel($item['etapa_funil']) ?></td>
                        <td><span class="badge badge-primary"><?= $item['total'] ?></span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>

    <div class="card" style="flex: 1; min-width: 300px;">
        <div class="card-header">
            <span class="card-title">Ranking de Gatilhos</span>
        </div>
        <?php if (empty($metrics['ranking_gatilhos'])): ?>
            <p class="text-muted">Sem dados de gatilhos ainda.</p>
        <?php else: ?>
            <table>
                <thead><tr><th>Gatilho</th><th>Usos</th><th>Taxa</th></tr></thead>
                <tbody>
                <?php foreach ($metrics['ranking_gatilhos'] as $g): ?>
                    <tr>
                        <td><?= e($g['titulo']) ?></td>
                        <td><?= $g['total_usos'] ?></td>
                        <td><span class="badge badge-success"><?= $g['taxa_sucesso'] ?? 0 ?>%</span></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php if (!empty($alertas)): ?>
<div class="card">
    <div class="card-header">
        <span class="card-title">🔔 Alertas de Follow-up</span>
    </div>
    <table>
        <thead><tr><th>Lead</th><th>Retorno</th><th>Gatilho Sugerido</th><th>Responsável</th></tr></thead>
        <tbody>
        <?php foreach ($alertas as $a): ?>
            <tr>
                <td><a href="<?= url('/leads/' . $a['lead_id']) ?>"><?= e($a['lead_nome']) ?></a></td>
                <td><?= formatDateTime($a['data_retorno']) ?></td>
                <td><?= e($a['gatilho_titulo'] ?? '-') ?></td>
                <td><?= e($a['usuario_nome']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php endif; ?>
