<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Usuario;
use App\Models\Gatilho;
use App\Models\CriterioDiagnostico;
use App\Models\FunilEtapaConfig;
use App\Services\FunilService;

class ConfigController extends Controller
{
    public function index(): void
    {
        $user = Auth::user();
        $usuarioModel = new Usuario();
        $usuarios = $usuarioModel->getByEmpresa((int) $user['empresa_id']);

        $this->view('pages/config/index', [
            'title' => 'Configurações',
            'usuarios' => $usuarios,
        ]);
    }

    public function usuarios(): void
    {
        $user = Auth::user();
        $usuarioModel = new Usuario();
        $usuarios = $usuarioModel->getByEmpresa((int) $user['empresa_id']);

        $this->view('pages/config/usuarios', [
            'title' => 'Gerenciar Usuários',
            'usuarios' => $usuarios,
        ]);
    }

    public function storeUsuario(): void
    {
        $this->validateCsrf();
        $user = Auth::user();
        $usuarioModel = new Usuario();

        $email = trim((string) $this->input('email'));
        $senha = (string) $this->input('senha');
        $perfil = (string) $this->input('perfil');

        if (empty($email) || empty($senha)) {
            flash('error', 'Email e senha são obrigatórios.');
            $this->redirect(url('/config/usuarios'));
        }

        if (!in_array($perfil, config('app.perfis'), true) || $perfil === 'admin_saas') {
            $perfil = 'sdr';
        }

        $exists = $usuarioModel->findByEmail($email, (int) $user['empresa_id']);
        if ($exists) {
            flash('error', 'Email já cadastrado nesta empresa.');
            $this->redirect(url('/config/usuarios'));
        }

        $usuarioModel->create([
            'empresa_id' => (int) $user['empresa_id'],
            'nome' => trim((string) $this->input('nome')),
            'email' => $email,
            'senha' => password_hash($senha, PASSWORD_DEFAULT),
            'perfil' => $perfil,
            'ativo' => 1,
        ]);

        flash('success', 'Usuário criado!');
        $this->redirect(url('/config/usuarios'));
    }

    public function gatilhos(): void
    {
        $user = Auth::user();
        $gatilhoModel = new Gatilho();
        $gatilhos = $gatilhoModel->getByEmpresa((int) $user['empresa_id']);

        $this->view('pages/config/gatilhos', [
            'title' => 'Gerenciar Gatilhos',
            'gatilhos' => $gatilhos,
        ]);
    }

    public function funil(): void
    {
        $user = Auth::user();
        $this->view('pages/config/funil', [
            'title' => 'Configurar Funil',
            'etapas' => (new FunilService())->getEtapas((int) $user['empresa_id']),
        ]);
    }

    public function saveFunil(): void
    {
        $this->validateCsrf();
        $user = Auth::user();
        $model = new FunilEtapaConfig();
        $permitidas = config('app.funil_etapas');
        $rotulos = $this->input('rotulos', []);
        $ordens = $this->input('ordens', []);

        foreach ($permitidas as $indice => $chave) {
            $rotulo = trim((string) ($rotulos[$chave] ?? ''));
            if ($rotulo === '') {
                $rotulo = config('app.funil_labels')[$chave] ?? $chave;
            }
            $ordem = (int) ($ordens[$chave] ?? ($indice + 1));
            $model->salvar((int) $user['empresa_id'], $chave, $rotulo, max(1, min(99, $ordem)));
        }

        flash('success', 'Etapas do funil atualizadas!');
        $this->redirect(url('/config/funil'));
    }

    public function storeGatilho(): void
    {
        $this->validateCsrf();
        $user = Auth::user();
        $gatilhoModel = new Gatilho();

        $gatilhoModel->create([
            'empresa_id' => (int) $user['empresa_id'],
            'categoria' => trim((string) $this->input('categoria')),
            'etapa_funil' => $this->input('etapa_funil') ?: null,
            'titulo' => trim((string) $this->input('titulo')),
            'texto' => trim((string) $this->input('texto')),
            'ativo' => 1,
        ]);

        flash('success', 'Gatilho criado!');
        $this->redirect(url('/config/gatilhos'));
    }

    public function criterios(): void
    {
        $user = Auth::user();
        $criterioModel = new CriterioDiagnostico();
        $criterios = $criterioModel->getComOpcoes((int) $user['empresa_id']);

        $this->view('pages/config/criterios', [
            'title' => 'Critérios de Diagnóstico',
            'criterios' => $criterios,
        ]);
    }

    public function updateCriterio(int $id): void
    {
        $this->validateCsrf();
        $user = Auth::user();
        $criterioModel = new CriterioDiagnostico();
        $criterio = $criterioModel->find($id);

        if (!$criterio) {
            flash('error', 'Critério não encontrado.');
            $this->redirect(url('/config/criterios'));
        }

        if ($criterio['empresa_id'] && (int) $criterio['empresa_id'] !== (int) $user['empresa_id']) {
            flash('error', 'Sem permissão.');
            $this->redirect(url('/config/criterios'));
        }

        $peso = (int) $this->input('peso');
        if ($peso < 1 || $peso > 10) {
            $peso = (int) $criterio['peso'];
        }

        if (!$criterio['empresa_id']) {
            $criterioModel->create([
                'empresa_id' => (int) $user['empresa_id'],
                'bloco' => $criterio['bloco'],
                'nome' => trim((string) $this->input('nome', $criterio['nome'])),
                'descricao' => trim((string) $this->input('descricao', $criterio['descricao'])),
                'peso' => $peso,
                'ordem' => (int) $criterio['ordem'],
                'ativo' => 1,
            ]);
        } else {
            $criterioModel->update($id, [
                'nome' => trim((string) $this->input('nome')),
                'descricao' => trim((string) $this->input('descricao')) ?: null,
                'peso' => $peso,
            ]);
        }

        flash('success', 'Critério atualizado!');
        $this->redirect(url('/config/criterios'));
    }

    public function aparencia(): void
    {
        $user = Auth::user();
        $this->view('pages/config/aparencia', [
            'title' => 'Aparência',
            'themeMode' => $user['theme_mode'] ?? 'dark',
            'colorPalette' => $user['color_palette'] ?? 'rose',
            'palettes' => \App\Services\ThemeService::PALETTES,
            'paletteLabels' => \App\Services\ThemeService::paletteLabels(),
            'paletteColors' => \App\Services\ThemeService::paletteColors(),
        ]);
    }

    public function saveTheme(): void
    {
        $this->validateCsrf();
        $user = Auth::user();
        $mode = (string) $this->input('theme_mode', 'dark');
        $palette = (string) $this->input('color_palette', 'rose');

        $empresaModel = new \App\Models\Empresa();
        if (!$empresaModel->updateTheme((int) $user['empresa_id'], $mode, $palette)) {
            if ($this->wantsJson()) {
                $this->json(['ok' => false, 'error' => 'Tema inválido'], 422);
            }
            flash('error', 'Tema inválido.');
            $this->redirect(url('/config/aparencia'));
        }

        Auth::refreshUserTheme($mode, $palette);

        if ($this->wantsJson()) {
            $this->json(['ok' => true, 'theme_mode' => $mode, 'color_palette' => $palette]);
        }

        flash('success', 'Aparência atualizada!');
        $this->redirect(url('/config/aparencia'));
    }
}
