<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Lead;
use App\Models\Usuario;

class LeadController extends Controller
{
    private Lead $leadModel;
    private Usuario $usuarioModel;

    public function __construct()
    {
        $this->leadModel = new Lead();
        $this->usuarioModel = new Usuario();
    }

    public function index(): void
    {
        $user = Auth::user();
        $filters = [
            'etapa' => $this->input('etapa'),
            'origem' => $this->input('origem'),
            'responsavel' => $this->input('responsavel'),
            'busca' => $this->input('busca'),
        ];

        $leads = $this->leadModel->getFiltered(
            (int) $user['empresa_id'],
            array_filter($filters),
            (int) $user['id'],
            $user['perfil']
        );

        $comerciais = $this->usuarioModel->getComerciais((int) $user['empresa_id']);

        $this->view('pages/leads/index', [
            'title' => 'Leads',
            'leads' => $leads,
            'filters' => $filters,
            'comerciais' => $comerciais,
        ]);
    }

    public function create(): void
    {
        $user = Auth::user();
        $comerciais = $this->usuarioModel->getComerciais((int) $user['empresa_id']);

        $this->view('pages/leads/form', [
            'title' => 'Novo Lead',
            'lead' => null,
            'comerciais' => $comerciais,
        ]);
    }

    public function store(): void
    {
        $this->validateCsrf();
        $user = Auth::user();

        $data = [
            'empresa_id' => (int) $user['empresa_id'],
            'nome' => trim((string) $this->input('nome')),
            'email' => trim((string) $this->input('email')) ?: null,
            'telefone' => trim((string) $this->input('telefone')) ?: null,
            'empresa_nome' => trim((string) $this->input('empresa_nome')) ?: null,
            'origem' => trim((string) $this->input('origem')) ?: null,
            'etapa_funil' => 'novo_lead',
            'sdr_id' => (int) ($this->input('sdr_id') ?: $user['id']),
            'closer_id' => $this->input('closer_id') ? (int) $this->input('closer_id') : null,
            'valor_estimado' => $this->input('valor_estimado') ? (float) $this->input('valor_estimado') : null,
            'observacoes' => trim((string) $this->input('observacoes')) ?: null,
        ];

        if (empty($data['nome'])) {
            flash('error', 'Nome é obrigatório.');
            $this->redirect(url('/leads/novo'));
        }

        $id = $this->leadModel->create($data);
        flash('success', 'Lead criado com sucesso!');
        $this->redirect(url('/leads/' . $id));
    }

    public function show(int $id): void
    {
        $user = Auth::user();
        $lead = $this->leadModel->findWithDetails($id, (int) $user['empresa_id']);

        if (!$lead) {
            flash('error', 'Lead não encontrado.');
            $this->redirect(url('/leads'));
        }

        if (!$this->leadModel->canAccess($lead, (int) $user['id'], $user['perfil'])) {
            flash('error', 'Sem permissão para ver este lead.');
            $this->redirect(url('/leads'));
        }

        $this->view('pages/leads/show', [
            'title' => $lead['nome'],
            'lead' => $lead,
        ]);
    }

    public function edit(int $id): void
    {
        $user = Auth::user();
        $lead = $this->leadModel->findWithDetails($id, (int) $user['empresa_id']);

        if (!$lead || !$this->leadModel->canAccess($lead, (int) $user['id'], $user['perfil'])) {
            flash('error', 'Lead não encontrado ou sem permissão.');
            $this->redirect(url('/leads'));
        }

        $comerciais = $this->usuarioModel->getComerciais((int) $user['empresa_id']);

        $this->view('pages/leads/form', [
            'title' => 'Editar Lead',
            'lead' => $lead,
            'comerciais' => $comerciais,
        ]);
    }

    public function update(int $id): void
    {
        $this->validateCsrf();
        $user = Auth::user();
        $lead = $this->leadModel->findWithDetails($id, (int) $user['empresa_id']);

        if (!$lead || !$this->leadModel->canAccess($lead, (int) $user['id'], $user['perfil'])) {
            flash('error', 'Lead não encontrado ou sem permissão.');
            $this->redirect(url('/leads'));
        }

        $data = [
            'nome' => trim((string) $this->input('nome')),
            'email' => trim((string) $this->input('email')) ?: null,
            'telefone' => trim((string) $this->input('telephone')) ?: trim((string) $this->input('telefone')) ?: null,
            'empresa_nome' => trim((string) $this->input('empresa_nome')) ?: null,
            'origem' => trim((string) $this->input('origem')) ?: null,
            'sdr_id' => (int) ($this->input('sdr_id') ?: $lead['sdr_id']),
            'closer_id' => $this->input('closer_id') ? (int) $this->input('closer_id') : null,
            'valor_estimado' => $this->input('valor_estimado') ? (float) $this->input('valor_estimado') : null,
            'observacoes' => trim((string) $this->input('observacoes')) ?: null,
        ];

        if (Auth::hasPerfil(['closer', 'gestor', 'admin_saas'])) {
            $etapa = $this->input('etapa_funil');
            if ($etapa && in_array($etapa, config('app.funil_etapas'), true)) {
                $data['etapa_funil'] = $etapa;
            }
        }

        $this->leadModel->update($id, $data);
        flash('success', 'Lead atualizado!');
        $this->redirect(url('/leads/' . $id));
    }

    public function delete(int $id): void
    {
        $this->validateCsrf();
        $user = Auth::user();

        if (!Auth::hasPerfil(['gestor', 'admin_saas'])) {
            flash('error', 'Sem permissão para excluir leads.');
            $this->redirect(url('/leads'));
        }

        $lead = $this->leadModel->find($id);
        if ($lead && (int) $lead['empresa_id'] === (int) $user['empresa_id']) {
            $this->leadModel->delete($id);
            flash('success', 'Lead excluído.');
        }

        $this->redirect(url('/leads'));
    }
}
