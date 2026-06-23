<div class="flex gap-2 flex-wrap">
    <div class="card" style="flex: 1; min-width: 300px;">
        <div class="card-header"><span class="card-title">Novo Gatilho</span></div>
        <form method="POST" action="<?= url('/config/gatilhos') ?>">
            <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
            <div class="form-group">
                <label class="form-label">Título</label>
                <input type="text" name="titulo" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">Categoria</label>
                <input type="text" name="categoria" class="form-control" required placeholder="Curiosidade, Urgência...">
            </div>
            <div class="form-group">
                <label class="form-label">Etapa do Funil</label>
                <select name="etapa_funil" class="form-control">
                    <option value="">Todas</option>
                    <?php foreach (config('app.funil_etapas') as $etapa): ?>
                        <option value="<?= $etapa ?>"><?= funilLabel($etapa) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="form-group">
                <label class="form-label">Texto</label>
                <textarea name="texto" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Criar Gatilho</button>
        </form>
    </div>

    <div class="card" style="flex: 2;">
        <div class="card-header"><span class="card-title">Gatilhos (<?= count($gatilhos) ?>)</span></div>
        <div class="gatilhos-grid">
            <?php foreach ($gatilhos as $g): ?>
                <div class="gatilho-card">
                    <h3><?= e($g['titulo']) ?></h3>
                    <span class="badge badge-secondary"><?= e($g['categoria']) ?></span>
                    <p class="mt-1"><?= e(mb_substr($g['texto'], 0, 150)) ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<a href="<?= url('/config') ?>" class="btn btn-outline mt-2">← Voltar</a>
