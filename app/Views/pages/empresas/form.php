<div class="card">
    <form method="POST" action="<?= url('/admin/empresas') ?>">
        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Nome *</label>
                <input type="text" name="nome" class="form-control" required>
            </div>
            <div class="form-group">
                <label class="form-label">CNPJ</label>
                <input type="text" name="cnpj" class="form-control">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Telefone</label>
                <input type="text" name="telefone" class="form-control">
            </div>
            <div class="form-group">
                <label class="form-label">Plano</label>
                <select name="plano_id" class="form-control">
                    <?php foreach ($planos as $p): ?>
                        <option value="<?= $p['id'] ?>"><?= e($p['nome']) ?> — R$ <?= number_format((float)$p['preco_mensal'], 2, ',', '.') ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-primary">Criar Empresa</button>
        <a href="<?= url('/admin/empresas') ?>" class="btn btn-outline">Cancelar</a>
    </form>
</div>
