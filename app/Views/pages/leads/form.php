<div class="card">
    <form method="POST" action="<?= $lead ? url('/leads/' . $lead['id']) : url('/leads') ?>">
        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Nome *</label>
                <input type="text" name="nome" class="form-control" value="<?= e($lead['nome'] ?? old('nome')) ?>" required>
            </div>
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= e($lead['email'] ?? old('email')) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Telefone</label>
                <input type="text" name="telefone" class="form-control" value="<?= e($lead['telefone'] ?? old('telefone')) ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Empresa do Lead</label>
                <input type="text" name="empresa_nome" class="form-control" value="<?= e($lead['empresa_nome'] ?? '') ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Origem</label>
                <input type="text" name="origem" class="form-control" value="<?= e($lead['origem'] ?? '') ?>" placeholder="LinkedIn, Site, Indicação...">
            </div>
            <div class="form-group">
                <label class="form-label">Valor Estimado (R$)</label>
                <input type="number" name="valor_estimado" class="form-control" step="0.01" value="<?= e($lead['valor_estimado'] ?? '') ?>">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">SDR Responsável</label>
                <select name="sdr_id" class="form-control">
                    <?php foreach ($comerciais as $c): ?>
                        <?php if ($c['perfil'] === 'sdr' || $c['perfil'] === 'closer'): ?>
                        <option value="<?= $c['id'] ?>" <?= ($lead['sdr_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['nome']) ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Closer</label>
                <select name="closer_id" class="form-control">
                    <option value="">Nenhum</option>
                    <?php foreach ($comerciais as $c): ?>
                        <?php if ($c['perfil'] === 'closer'): ?>
                        <option value="<?= $c['id'] ?>" <?= ($lead['closer_id'] ?? '') == $c['id'] ? 'selected' : '' ?>><?= e($c['nome']) ?></option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php if ($lead && in_array($user['perfil'], ['closer', 'gestor', 'admin_saas'])): ?>
            <div class="form-group">
                <label class="form-label">Etapa do Funil</label>
                <select name="etapa_funil" class="form-control">
                    <?php foreach (config('app.funil_etapas') as $etapa): ?>
                        <option value="<?= $etapa ?>" <?= ($lead['etapa_funil'] ?? '') === $etapa ? 'selected' : '' ?>><?= funilLabel($etapa) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>
        </div>

        <div class="form-group">
            <label class="form-label">Observações</label>
            <textarea name="observacoes" class="form-control"><?= e($lead['observacoes'] ?? '') ?></textarea>
        </div>

        <div class="flex gap-1">
            <button type="submit" class="btn btn-primary"><?= $lead ? 'Salvar' : 'Criar Lead' ?></button>
            <a href="<?= url('/leads') ?>" class="btn btn-outline">Cancelar</a>
        </div>
    </form>
</div>
