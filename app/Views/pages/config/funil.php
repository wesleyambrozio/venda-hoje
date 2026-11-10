<div class="card">
    <div class="card-header"><span class="card-title">Etapas do Funil</span></div>
    <p class="text-muted mb-2">Personalize os nomes e a sequência exibidos para sua equipe. Os registros existentes permanecem preservados.</p>
    <form method="POST" action="<?= url('/config/funil') ?>">
        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
        <div class="table-wrap">
            <table>
                <thead><tr><th>Etapa interna</th><th>Nome exibido</th><th>Ordem</th></tr></thead>
                <tbody>
                <?php foreach ($etapas as $etapa): ?>
                    <tr>
                        <td><code><?= e($etapa['chave']) ?></code></td>
                        <td><input class="form-control" name="rotulos[<?= e($etapa['chave']) ?>]" value="<?= e($etapa['rotulo']) ?>" required maxlength="100"></td>
                        <td><input class="form-control" type="number" name="ordens[<?= e($etapa['chave']) ?>]" value="<?= (int) $etapa['ordem'] ?>" min="1" max="99" required style="max-width:100px"></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-2"><button type="submit" class="btn btn-primary">Salvar etapas</button></div>
    </form>
</div>
