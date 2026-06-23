<div class="flex gap-2 flex-wrap">
    <div class="card" style="flex: 1;">
        <div class="score-display">
            <div class="score-circle <?= $diagnostico['classificacao'] ?>">
                <?= number_format((float)$diagnostico['score_final'], 0) ?>
            </div>
            <h2><?= ucfirst($diagnostico['classificacao']) ?></h2>
            <p class="text-muted">Lead: <?= e($lead['nome']) ?></p>
            <p class="text-muted"><?= formatDateTime($diagnostico['created_at']) ?></p>
        </div>
    </div>

    <div class="card" style="flex: 2;">
        <div class="card-header"><span class="card-title">Respostas por Critério</span></div>
        <table>
            <thead><tr><th>Bloco</th><th>Critério</th><th>Peso</th><th>Valor</th></tr></thead>
            <tbody>
            <?php foreach ($respostas as $r): ?>
                <tr>
                    <td><?= e($r['bloco']) ?></td>
                    <td><?= e($r['criterio_nome']) ?></td>
                    <td><?= $r['peso'] ?></td>
                    <td><span class="badge badge-primary"><?= $r['valor'] ?>/5</span></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
        <?php if ($diagnostico['observacoes']): ?>
            <p class="mt-2"><strong>Obs:</strong> <?= e($diagnostico['observacoes']) ?></p>
        <?php endif; ?>
    </div>
</div>

<a href="<?= url('/leads/' . $lead['id']) ?>" class="btn btn-outline mt-2">← Voltar ao Lead</a>
