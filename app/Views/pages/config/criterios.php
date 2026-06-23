<?php
$blocoAtual = '';
foreach ($criterios as $criterio):
    if ($criterio['bloco'] !== $blocoAtual):
        if ($blocoAtual !== '') echo '</div>';
        $blocoAtual = $criterio['bloco'];
?>
<div class="card">
    <div class="card-header"><span class="card-title"><?= e($blocoAtual) ?></span></div>
<?php endif; ?>

    <form method="POST" action="<?= url('/config/criterios/' . $criterio['id']) ?>" class="criterio-item">
        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
        <div class="form-row">
            <div class="form-group" style="flex:2;">
                <label class="form-label"><?= e($criterio['nome']) ?></label>
                <input type="text" name="nome" class="form-control" value="<?= e($criterio['nome']) ?>">
            </div>
            <div class="form-group">
                <label class="form-label">Peso (1-10)</label>
                <input type="number" name="peso" class="form-control" min="1" max="10" value="<?= $criterio['peso'] ?>">
            </div>
            <div class="form-group" style="align-self: end;">
                <button type="submit" class="btn btn-sm btn-primary">Salvar</button>
            </div>
        </div>
        <div class="form-group">
            <input type="text" name="descricao" class="form-control" value="<?= e($criterio['descricao'] ?? '') ?>" placeholder="Descrição">
        </div>
    </form>

<?php endforeach; ?>
</div>

<a href="<?= url('/config') ?>" class="btn btn-outline mt-2">← Voltar</a>

<p class="text-muted mt-1" style="font-size: 0.85rem;">
    Critérios globais (empresa_id NULL) serão copiados para sua empresa ao editar.
</p>
