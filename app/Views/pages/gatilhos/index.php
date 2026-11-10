<form method="GET" class="filters-bar">
    <div class="form-group">
        <label class="form-label">Categoria</label>
        <select name="categoria" class="form-control">
            <option value="">Todas</option>
            <?php foreach ($categorias as $cat): ?>
                <option value="<?= e($cat['categoria']) ?>" <?= ($filters['categoria'] ?? '') === $cat['categoria'] ? 'selected' : '' ?>><?= e($cat['categoria']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="form-group">
        <label class="form-label">Etapa do Funil</label>
        <select name="etapa" class="form-control">
            <option value="">Todas</option>
            <?php foreach (config('app.funil_etapas') as $etapa): ?>
                <option value="<?= $etapa ?>" <?= ($filters['etapa'] ?? '') === $etapa ? 'selected' : '' ?>><?= funilLabel($etapa) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <button type="submit" class="btn btn-secondary">Filtrar</button>
</form>

<?php if (!empty($favoritoIds)): ?>
<div class="card">
    <div class="card-header"><span class="card-title">Seus Favoritos</span></div>
    <p class="text-muted">Marque gatilhos com estrela para favoritar.</p>
</div>
<?php endif; ?>

<?php
$labelsGrupo = config('app.funil_labels');
$labelsGrupo['geral'] = 'Geral / Sem etapa';
$openFirst = true;
?>

<div class="gatilho-grupos">
<?php foreach ($grupos as $etapaKey => $lista): ?>
    <?php if (empty($lista)) continue; ?>
    <details class="gatilho-grupo card" <?= $openFirst ? 'open' : '' ?>>
        <?php $openFirst = false; ?>
        <summary class="gatilho-grupo__summary">
            <span class="gatilho-grupo__title"><?= e(mb_strtoupper($labelsGrupo[$etapaKey] ?? $etapaKey)) ?></span>
            <span class="badge badge-muted"><?= count($lista) ?></span>
        </summary>
        <div class="gatilhos-grid mt-2">
            <?php foreach ($lista as $g): ?>
                <div class="gatilho-card">
                    <div class="flex justify-between items-center">
                        <h3><?= e($g['titulo']) ?></h3>
                        <form method="POST" action="<?= url('/gatilhos/' . $g['id'] . '/favorito') ?>" style="display:inline;">
                            <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                            <button type="submit" class="btn btn-sm btn-outline" aria-label="Favoritar"><?= in_array($g['id'], $favoritoIds) ? '★' : '☆' ?></button>
                        </form>
                    </div>
                    <span class="badge badge-secondary"><?= e($g['categoria']) ?></span>
                    <?php if ($g['etapa_funil']): ?>
                        <span class="badge badge-muted"><?= funilLabel($g['etapa_funil']) ?></span>
                    <?php endif; ?>
                    <p class="mt-1"><?= e($g['texto']) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </details>
<?php endforeach; ?>
</div>

<?php if (empty($gatilhos)): ?>
<div class="card"><div class="empty-state">Nenhum gatilho encontrado.</div></div>
<?php endif; ?>
