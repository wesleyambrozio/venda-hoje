<div class="card">
    <p class="mb-2"><strong>Lead:</strong> <?= e($lead['nome']) ?> — <span class="badge badge-primary"><?= funilLabel($lead['etapa_funil']) ?></span></p>

    <form method="POST" action="<?= url('/leads/' . $lead['id'] . '/atendimento') ?>">
        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Canal *</label>
                <select name="canal" class="form-control" required>
                    <?php foreach (config('app.canais_atendimento') as $canal): ?>
                        <option value="<?= $canal ?>"><?= ucfirst($canal) ?></option>
                    <?php endforeach; ?>
                </select>
                <?php
                $mensagemWhatsapp = sprintf(
                    'Olá, %s! Tudo bem? Aqui é %s. Estou entrando em contato para dar continuidade ao nosso atendimento.',
                    $lead['nome'],
                    $user['nome'] ?? 'da equipe Venda Hoje'
                );
                $whatsappBaseUrl = whatsappUrl($lead['telefone'] ?? null);
                $whatsappUrl = $whatsappBaseUrl
                    ? $whatsappBaseUrl . '?text=' . rawurlencode($mensagemWhatsapp)
                    : null;
                ?>
                <?php if ($whatsappUrl): ?>
                    <a href="<?= e($whatsappUrl) ?>" target="_blank" rel="noopener noreferrer"
                       class="btn btn-success btn-sm mt-1" style="background:var(--success);color:#fff;"
                       id="whatsapp-link"
                       data-whatsapp-base-url="<?= e($whatsappBaseUrl) ?>"
                       data-whatsapp-default-message="<?= e($mensagemWhatsapp) ?>">Abrir WhatsApp</a>
                    <small class="text-muted" id="whatsapp-message-hint">Mensagem padrão selecionada.</small>
                <?php else: ?>
                    <small class="text-muted">Cadastre um telefone para abrir o WhatsApp.</small>
                <?php endif; ?>
            </div>
            <div class="form-group">
                <label class="form-label">Resultado *</label>
                <select name="resultado" id="resultado" class="form-control" required>
                    <option value="concluido">Concluído</option>
                    <option value="follow_up">Follow-up</option>
                    <option value="nao_atendeu">Não Atendeu</option>
                    <option value="cancelado">Cancelado</option>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Nova Etapa do Funil</label>
                <select name="etapa_nova" class="form-control">
                    <option value="">Manter atual</option>
                    <?php foreach (config('app.funil_etapas') as $etapa): ?>
                        <option value="<?= $etapa ?>" <?= $lead['etapa_funil'] === $etapa ? 'selected' : '' ?>><?= funilLabel($etapa) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Resumo do Atendimento</label>
            <textarea name="resumo" class="form-control" rows="4" placeholder="Descreva o que foi conversado..."></textarea>
        </div>

        <div id="followup-fields" style="display: none;">
            <div class="alert alert-info">Follow-up: o atendimento ficará aberto até ser encerrado.</div>
            <div class="form-row">
                <div class="form-group">
                    <label class="form-label">Data de Retorno *</label>
                    <input type="datetime-local" name="data_retorno" class="form-control" value="<?= date('Y-m-d\TH:i', strtotime('+1 day')) ?>">
                </div>
                <div class="form-group">
                    <label class="form-label">Observação do Follow-up</label>
                    <input type="text" name="followup_obs" class="form-control" placeholder="Motivo do retorno...">
                </div>
            </div>
        </div>

        <?php if (!empty($etapas)): ?>
        <div class="form-group">
            <label class="form-label">Etapas do Caminho Concluídas</label>
            <div class="flex flex-wrap gap-1">
                <?php foreach ($etapas as $etapa): ?>
                    <label style="display: flex; align-items: center; gap: 0.25rem; font-size: 0.9rem;">
                        <input type="checkbox" name="etapas_caminho[]" value="<?= $etapa['id'] ?>">
                        <?= e($etapa['titulo']) ?>
                    </label>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <?php if (!empty($gatilhos)): ?>
        <div class="form-group">
            <label class="form-label">Gatilhos Utilizados</label>
            <div class="gatilhos-grid">
                <?php foreach ($gatilhos as $g): ?>
                <div class="gatilho-card">
                    <label style="cursor: pointer;">
                        <input type="checkbox" name="gatilhos[]" value="<?= $g['id'] ?>"
                               data-gatilho-checkbox
                               data-gatilho-title="<?= e($g['titulo']) ?>"
                               data-gatilho-text="<?= e($g['texto']) ?>">
                        <strong><?= e($g['titulo']) ?></strong>
                        <span class="badge badge-muted"><?= e($g['categoria']) ?></span>
                    </label>
                    <p class="mt-1"><?= e(mb_substr($g['texto'], 0, 120)) ?>...</p>
                    <select name="gatilho_funcionou_<?= $g['id'] ?>" class="form-control mt-1" style="font-size: 0.8rem;" disabled data-gatilho-resultado>
                        <option value="">Funcionou?</option>
                        <option value="1">Sim ✓</option>
                        <option value="0">Não ✗</option>
                    </select>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>

        <div class="flex gap-1 mt-2">
            <button type="submit" class="btn btn-primary">Registrar Atendimento</button>
            <a href="<?= url('/leads/' . $lead['id']) ?>" class="btn btn-outline">Cancelar</a>
        </div>
    </form>
</div>
