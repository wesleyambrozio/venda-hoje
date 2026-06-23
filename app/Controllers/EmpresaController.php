<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Empresa;
use App\Models\Plano;
use App\Models\Usuario;

class EmpresaController extends Controller
{
    public function index(): void
    {
        $empresaModel = new Empresa();
        $empresas = $empresaModel->getAllWithPlano();

        $this->view('pages/empresas/index', [
            'title' => 'Empresas - Admin SaaS',
            'empresas' => $empresas,
        ]);
    }

    public function create(): void
    {
        $planoModel = new Plano();
        $this->view('pages/empresas/form', [
            'title' => 'Nova Empresa',
            'empresa' => null,
            'planos' => $planoModel->getAtivos(),
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();
        $empresaModel = new Empresa();

        $planoId = (int) $this->input('plano_id');
        $empresaId = $empresaModel->create([
            'nome' => trim((string) $this->input('nome')),
            'cnpj' => trim((string) $this->input('cnpj')) ?: null,
            'email' => trim((string) $this->input('email')) ?: null,
            'telefone' => trim((string) $this->input('telefone')) ?: null,
            'plano_id' => $planoId ?: null,
            'ativo' => 1,
        ]);

        $db = \App\Core\Database::getInstance();
        $db->prepare(
            'INSERT INTO assinaturas (empresa_id, plano_id, status, data_inicio) VALUES (?, ?, ?, CURDATE())'
        )->execute([$empresaId, $planoId ?: 1, 'trial']);

        flash('success', 'Empresa criada!');
        $this->redirect(url('/admin/empresas'));
    }
}
