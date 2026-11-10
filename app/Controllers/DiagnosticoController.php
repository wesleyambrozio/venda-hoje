<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Lead;
use App\Models\CriterioDiagnostico;
use App\Models\Diagnostico;
use App\Services\DiagnosticoService;

class DiagnosticoController extends Controller
{
    public function create(int $leadId): void
    {
        $user = Auth::user();
        $leadModel = new Lead();
        $lead = $leadModel->findWithDetails($leadId, (int) $user['empresa_id']);

        if (!$lead || !$leadModel->canAccess($lead, (int) $user['id'], $user['perfil'])) {
            flash('error', 'Lead não encontrado.');
            $this->redirect(url('/leads'));
        }

        if (!Auth::hasPerfil(['closer', 'gestor', 'admin_saas'])) {
            flash('error', 'Apenas Closers podem realizar diagnósticos.');
            $this->redirect(url('/leads/' . $leadId));
        }

        $criterioModel = new CriterioDiagnostico();
        $criterios = $criterioModel->getComOpcoes((int) $user['empresa_id']);

        $this->view('pages/diagnosticos/form', [
            'title' => 'Diagnóstico - ' . $lead['nome'],
            'lead' => $lead,
            'criterios' => $criterios,
        ]);
    }

    public function store(int $leadId): void
    {
        $this->validateCsrf();
        $user = Auth::user();
        $leadModel = new Lead();
        $lead = $leadModel->findWithDetails($leadId, (int) $user['empresa_id']);

        if (
            !$lead
            || !Auth::hasPerfil(['closer', 'gestor', 'admin_saas'])
            || !$leadModel->canAccess($lead, (int) $user['id'], $user['perfil'])
        ) {
            flash('error', 'Sem permissão.');
            $this->redirect(url('/leads'));
        }

        $criterioModel = new CriterioDiagnostico();
        $criterios = $criterioModel->getComOpcoes((int) $user['empresa_id']);
        $respostas = $this->input('respostas') ?? [];

        if (!is_array($respostas) || count($respostas) < 13) {
            flash('error', 'Preencha todos os 13 critérios.');
            $this->redirect(url('/leads/' . $leadId . '/diagnostico'));
        }

        $service = new DiagnosticoService();
        $resultado = $service->calcularScore($respostas, $criterios);

        $diagModel = new Diagnostico();
        $diagId = $diagModel->create([
            'lead_id' => $leadId,
            'usuario_id' => (int) $user['id'],
            'score_final' => $resultado['score'],
            'classificacao' => $resultado['classificacao'],
            'observacoes' => trim((string) $this->input('observacoes')) ?: null,
        ]);

        $diagModel->saveRespostas($diagId, $respostas);

        flash('success', sprintf('Diagnóstico salvo! Score: %.1f (%s)', $resultado['score'], scoreClassificacaoLabel($resultado['score'])));
        $this->redirect(url('/leads/' . $leadId));
    }

    public function show(int $id): void
    {
        $user = Auth::user();
        $diagModel = new Diagnostico();
        $diagnostico = $diagModel->find($id);

        if (!$diagnostico) {
            flash('error', 'Diagnóstico não encontrado.');
            $this->redirect(url('/leads'));
        }

        $leadModel = new Lead();
        $lead = $leadModel->findWithDetails((int) $diagnostico['lead_id'], (int) $user['empresa_id']);

        if (!$lead || !$leadModel->canAccess($lead, (int) $user['id'], $user['perfil'])) {
            flash('error', 'Lead não encontrado ou sem permissão.');
            $this->redirect(url('/leads'));
        }

        $respostas = $diagModel->getRespostas($id);

        $this->view('pages/diagnosticos/show', [
            'title' => 'Diagnóstico #' . $id,
            'diagnostico' => $diagnostico,
            'lead' => $lead,
            'respostas' => $respostas,
        ]);
    }
}
