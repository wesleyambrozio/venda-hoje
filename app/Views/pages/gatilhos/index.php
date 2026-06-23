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
    <div class="card-header"><span class="card-title">⭐ Seus Favoritos</span></div>
    <p class="text-muted">Marque gatilhos com ☆ para favoritar.</p>
</div>
<?php endif; ?>

<div class="card">
    <div class="card-header"><span class="card-title">Biblioteca (<?= count($gatilhos) ?>)</span></div>
    <?php if (empty($gatilhos)): ?>
        <div class="empty-state">Nenhum gatilho encontrado.</div>
    <?php else: ?>
    <div class="gatilhos-grid">
        <?php foreach ($gatilhos as $g): ?>
            <div class="gatilho-card">
                <div class="flex justify-between items-center">
                    <h3><?= e($g['titulo']) ?></h3>
                    <form method="POST" action="<?= url('/gatilhos/' . $g['id'] . '/favorito') ?>" style="display:inline;">
                        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                        <button type="submit" class="btn btn-sm btn-outline"><?= in_array($g['id'], $favoritoIds) ? '★' : '☆' ?></button>
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
    <?php endif; ?>
</div>
