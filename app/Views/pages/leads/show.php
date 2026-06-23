<?php
$etapas = config('app.funil_etapas');
$etapaAtual = array_search($lead['etapa_funil'], $etapas);
?>

<div class="funil-bar">
    <?php foreach ($etapas as $i => $etapa): ?>
        <?php if (in_array($etapa, ['fechado', 'perdido']) && $i > 6) continue; ?>
        <div class="funil-step <?= $etapa === $lead['etapa_funil'] ? 'active' : ($i < $etapaAtual ? 'done' : '') ?>">
            <?= funilLabel($etapa) ?>
        </div>
    <?php endforeach; ?>
</div>

<div class="flex justify-between items-center mb-2 flex-wrap gap-1">
    <div class="flex gap-1 flex-wrap">
        <a href="<?= url('/leads/' . $lead['id'] . '/editar') ?>" class="btn btn-outline btn-sm">Editar</a>
        <a href="<?= url('/leads/' . $lead['id'] . '/atendimento') ?>" class="btn btn-primary btn-sm">+ Atendimento</a>
        <?php if (in_array($user['perfil'], ['closer', 'gestor', 'admin_saas'])): ?>
        <a href="<?= url('/leads/' . $lead['id'] . '/diagnostico') ?>" class="btn btn-secondary btn-sm">Diagnóstico</a>
        <?php endif; ?>
    </div>
</div>

<div class="flex gap-2 flex-wrap">
    <div class="card" style="flex: 1; min-width: 300px;">
        <div class="card-header"><span class="card-title">Dados do Lead</span></div>
        <p><strong>Empresa:</strong> <?= e($lead['empresa_nome'] ?? '-') ?></p>
        <p><strong>Email:</strong> <?= e($lead['email'] ?? '-') ?></p>
        <p><strong>Telefone:</strong> <?= e($lead['telefone'] ?? '-') ?></p>
        <p><strong>Origem:</strong> <?= e($lead['origem'] ?? '-') ?></p>
        <p><strong>Valor Estimado:</strong> <?= $lead['valor_estimado'] ? 'R$ ' . number_format((float)$lead['valor_estimado'], 2, ',', '.') : '-' ?></p>
        <p><strong>SDR:</strong> <?= e($lead['sdr_nome'] ?? '-') ?></p>
        <p><strong>Closer:</strong> <?= e($lead['closer_nome'] ?? '-') ?></p>
        <p><strong>Etapa:</strong> <span class="badge badge-primary"><?= funilLabel($lead['etapa_funil']) ?></span></p>
        <?php if ($lead['observacoes']): ?>
        <p class="mt-1"><strong>Obs:</strong> <?= e($lead['observacoes']) ?></p>
        <?php endif; ?>
    </div>

    <div class="card" style="flex: 2; min-width: 300px;">
        <div class="card-header"><span class="card-title">Atendimentos Recentes</span></div>
        <?php
        $atendimentoModel = new \App\Models\Atendimento();
        $atendimentos = $atendimentoModel->getByLead((int)$lead['id']);
        ?>
        <?php if (empty($atendimentos)): ?>
            <p class="text-muted">Nenhum atendimento registrado.</p>
        <?php else: ?>
            <table>
                <thead><tr><th>Data</th><th>Canal</th><th>Resultado</th><th>Resumo</th><th>Por</th></tr></thead>
                <tbody>
                <?php foreach (array_slice($atendimentos, 0, 5) as $a): ?>
                    <tr>
                        <td><?= formatDateTime($a['data_atendimento']) ?></td>
                        <td><?= e($a['canal']) ?></td>
                        <td><span class="badge badge-<?= $a['resultado'] === 'follow_up' ? 'warning' : 'success' ?>"><?= e($a['resultado']) ?></span></td>
                        <td><?= e(mb_substr($a['resumo'] ?? '', 0, 50)) ?></td>
                        <td><?= e($a['usuario_nome']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<div class="card">
    <div class="card-header"><span class="card-title">Diagnósticos</span></div>
    <?php
    $diagModel = new \App\Models\Diagnostico();
    $diagnosticos = $diagModel->getByLead((int)$lead['id']);
    ?>
    <?php if (empty($diagnosticos)): ?>
        <p class="text-muted">Nenhum diagnóstico realizado.</p>
    <?php else: ?>
        <table>
            <thead><tr><th>Data</th><th>Score</th><th>Classificação</th><th>Por</th><th></th></tr></thead>
            <tbody>
            <?php foreach ($diagnosticos as $d): ?>
                <tr>
                    <td><?= formatDateTime($d['created_at']) ?></td>
                    <td><span class="badge badge-<?= $d['classificacao'] ?>"><?= number_format((float)$d['score_final'], 1) ?></span></td>
                    <td><?= ucfirst($d['classificacao']) ?></td>
                    <td><?= e($d['usuario_nome']) ?></td>
                    <td><a href="<?= url('/diagnosticos/' . $d['id']) ?>" class="btn btn-sm btn-outline">Detalhes</a></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>
