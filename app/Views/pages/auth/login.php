<form method="POST" action="<?= url('/login') ?>">
    <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
    <div class="form-group">
        <label class="form-label" for="email">Email</label>
        <input type="email" id="email" name="email" class="form-control" value="<?= e(old('email')) ?>" required autofocus>
    </div>
    <div class="form-group">
        <label class="form-label" for="password">Senha</label>
        <input type="password" id="password" name="password" class="form-control" required>
    </div>
    <button type="submit" class="btn btn-primary" style="width: 100%; margin-top: 0.5rem;">Entrar</button>
</form>
<p class="text-muted text-center mt-2" style="font-size: 0.8rem;">
    Demo: sdr@vendahoje.com / password123
</p>
