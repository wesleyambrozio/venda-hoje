<div class="card">
    <div class="card-header"><span class="card-title">Modo e paleta RQCODE</span></div>
    <p class="text-muted mb-2">Escolha o modo claro/escuro e uma das paletas pré-definidas do padrão RQCODE.</p>

    <form method="POST" action="<?= url('/config/tema') ?>" class="form-row" style="align-items:end;">
        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
        <div class="form-group">
            <label class="form-label">Modo</label>
            <select name="theme_mode" class="form-control">
                <option value="dark" <?= $themeMode === 'dark' ? 'selected' : '' ?>>Escuro</option>
                <option value="light" <?= $themeMode === 'light' ? 'selected' : '' ?>>Claro</option>
            </select>
        </div>
        <div class="form-group">
            <label class="form-label">Paleta</label>
            <select name="color_palette" class="form-control">
                <?php foreach ($paletteLabels as $key => $label): ?>
                    <option value="<?= e($key) ?>" <?= $colorPalette === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="form-group">
            <button type="submit" class="btn btn-primary">Salvar aparência</button>
        </div>
    </form>

    <div class="palette-swatches mt-2">
        <?php foreach ($paletteColors as $key => $color): ?>
            <form method="POST" action="<?= url('/config/tema') ?>" style="display:inline;">
                <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
                <input type="hidden" name="theme_mode" value="<?= e($themeMode) ?>">
                <input type="hidden" name="color_palette" value="<?= e($key) ?>">
                <button type="submit" class="palette-swatch <?= $colorPalette === $key ? 'is-active' : '' ?>"
                        style="--swatch: <?= e($color) ?>"
                        title="<?= e($paletteLabels[$key] ?? $key) ?>">
                    <?= e($paletteLabels[$key] ?? $key) ?>
                </button>
            </form>
        <?php endforeach; ?>
    </div>
</div>
