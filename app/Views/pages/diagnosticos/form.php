<div class="card">
    <p class="mb-2"><strong>Lead:</strong> <?= e($lead['nome']) ?></p>

    <form method="POST" action="<?= url('/leads/' . $lead['id'] . '/diagnostico') ?>">
        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">

        <?php
        $blocoAtual = '';
        foreach ($criterios as $criterio):
            if ($criterio['bloco'] !== $blocoAtual):
                if ($blocoAtual !== '') echo '</div>';
                $blocoAtual = $criterio['bloco'];
        ?>
        <div class="criterio-block">
            <h3><?= e($blocoAtual) ?></h3>
        <?php endif; ?>

            <div class="criterio-item">
                <label class="form-label">
                    <?= e($criterio['nome']) ?>
                    <span class="badge badge-muted">Peso: <?= $criterio['peso'] ?></span>
                </label>
                <?php if ($criterio['descricao']): ?>
                    <p class="text-muted" style="font-size: 0.85rem;"><?= e($criterio['descricao']) ?></p>
                <?php endif; ?>
                <div class="flex flex-wrap gap-1 mt-1">
                    <?php foreach ($criterio['opcoes'] as $opcao): ?>
                        <label style="display: flex; align-items: center; gap: 0.25rem; padding: 0.5rem; background: var(--surface); border-radius: var(--radius); cursor: pointer; border: 2px solid var(--border);">
                            <input type="radio" name="respostas[<?= $criterio['id'] ?>]" value="<?= $opcao['valor'] ?>" required>
                            <span><?= $opcao['valor'] ?> — <?= e($opcao['rotulo']) ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

        <?php endforeach; ?>
        </div>

        <div class="form-group mt-2">
            <label class="form-label">Observações</label>
            <textarea name="observacoes" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-primary">Calcular e Salvar Diagnóstico</button>
        <a href="<?= url('/leads/' . $lead['id']) ?>" class="btn btn-outline">Cancelar</a>
    </form>
</div>

<p class="text-muted mt-1" style="font-size: 0.85rem;">
    Score = Σ(voto × peso) / Σ(5 × peso) × 100 | Quente ≥75 | Morno 50-74 | Frio &lt;50
</p>
