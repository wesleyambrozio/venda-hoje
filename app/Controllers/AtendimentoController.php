<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Atendimento;
use App\Models\Lead;
use App\Models\Gatilho;
use App\Models\EtapaCaminho;
use App\Models\Followup;
use App\Services\FollowupService;

class AtendimentoController extends Controller
{
    private Atendimento $atendimentoModel;
    private Lead $leadModel;
    private Gatilho $gatilhoModel;
    private EtapaCaminho $etapaModel;
    private Followup $followupModel;

    public function __construct()
    {
        $this->atendimentoModel = new Atendimento();
        $this->leadModel = new Lead();
        $this->gatilhoModel = new Gatilho();
        $this->etapaModel = new EtapaCaminho();
        $this->followupModel = new Followup();
    }

    public function create(int $leadId): void
    {
        $user = Auth::user();
        $lead = $this->leadModel->findWithDetails($leadId, (int) $user['empresa_id']);

        if (!$lead || !$this->leadModel->canAccess($lead, (int) $user['id'], $user['perfil'])) {
            flash('error', 'Lead não encontrado.');
            $this->redirect(url('/leads'));
        }

        if ($this->atendimentoModel->hasFollowUpAberto($leadId)) {
            flash('error', 'Existe um atendimento em follow-up aberto. Conclua-o antes de iniciar outro.');
            $this->redirect(url('/leads/' . $leadId));
        }

        $etapas = $this->etapaModel->getByEtapaFunil((int) $user['empresa_id'], $lead['etapa_funil']);
        $gatilhos = $this->gatilhoModel->getByEmpresa((int) $user['empresa_id'], $lead['etapa_funil']);

        $this->view('pages/atendimentos/form', [
            'title' => 'Novo Atendimento',
            'lead' => $lead,
            'etapas' => $etapas,
            'gatilhos' => $gatilhos,
        ]);
    }

    public function store(int $leadId): void
    {
        $this->validateCsrf();
        $user = Auth::user();
        $lead = $this->leadModel->findWithDetails($leadId, (int) $user['empresa_id']);

        if (!$lead || !$this->leadModel->canAccess($lead, (int) $user['id'], $user['perfil'])) {
            flash('error', 'Lead não encontrado.');
            $this->redirect(url('/leads'));
        }

        if ($this->atendimentoModel->hasFollowUpAberto($leadId)) {
            flash('error', 'Existe um atendimento em follow-up aberto.');
            $this->redirect(url('/leads/' . $leadId));
        }

        $resultado = (string) $this->input('resultado');
        $novaEtapa = $this->input('etapa_nova');

        $atendimentoId = $this->atendimentoModel->create([
            'lead_id' => $leadId,
            'usuario_id' => (int) $user['id'],
            'canal' => (string) $this->input('canal', 'telefone'),
            'resultado' => $resultado,
            'resumo' => trim((string) $this->input('resumo')) ?: null,
            'etapa_anterior' => $lead['etapa_funil'],
            'etapa_nova' => $novaEtapa ?: null,
            'encerrado' => $resultado === 'follow_up' ? 0 : 1,
            'data_atendimento' => date('Y-m-d H:i:s'),
        ]);

        if ($novaEtapa && in_array($novaEtapa, config('app.funil_etapas'), true)) {
            $this->leadModel->update($leadId, ['etapa_funil' => $novaEtapa]);
        }

        $etapasMarcadas = $this->input('etapas_caminho') ?? [];
        if (is_array($etapasMarcadas)) {
            $db = \App\Core\Database::getInstance();
            $stmtInsert = $db->prepare(
                'INSERT INTO atendimento_etapas (atendimento_id, etapa_caminho_id) VALUES (?, ?)'
            );
            foreach ($etapasMarcadas as $etapaId) {
                $stmtInsert->execute([$atendimentoId, (int) $etapaId]);
            }
        }

        $gatilhosUsados = $this->input('gatilhos') ?? [];
        if (is_array($gatilhosUsados)) {
            foreach ($gatilhosUsados as $gatilhoId) {
                $funcionou = $this->input('gatilho_funcionou_' . $gatilhoId);
                $funcionouVal = $funcionou === '' || $funcionou === null ? null : (bool) (int) $funcionou;
                $this->atendimentoModel->addGatilho(
                    $atendimentoId,
                    (int) $gatilhoId,
                    $funcionouVal,
                    null
                );
            }
        }

        if ($resultado === 'follow_up') {
            $followupService = new FollowupService();
            $gatilhoSugerido = $followupService->sugerirGatilho(
                (int) $user['empresa_id'],
                $novaEtapa ?: $lead['etapa_funil']
            );

            $this->followupModel->create([
                'lead_id' => $leadId,
                'atendimento_id' => $atendimentoId,
                'usuario_id' => (int) $user['id'],
                'data_retorno' => (string) $this->input('data_retorno', date('Y-m-d H:i:s', strtotime('+1 day'))),
                'gatilho_sugerido_id' => $gatilhoSugerido['id'] ?? null,
                'status' => 'pendente',
                'observacao' => trim((string) $this->input('followup_obs')) ?: null,
            ]);

            flash('success', 'Atendimento registrado com follow-up agendado!');
        } else {
            flash('success', 'Atendimento registrado com sucesso!');
        }

        $this->redirect(url('/leads/' . $leadId));
    }

    public function encerrarFollowUp(int $id): void
    {
        $this->validateCsrf();
        $user = Auth::user();
        $atendimento = $this->atendimentoModel->find($id);

        if (!$atendimento) {
            flash('error', 'Atendimento não encontrado.');
            $this->redirect(url('/followups'));
        }

        $lead = $this->leadModel->find($atendimento['lead_id']);
        if (!$lead || (int) $lead['empresa_id'] !== (int) $user['empresa_id']) {
            flash('error', 'Sem permissão.');
            $this->redirect(url('/followups'));
        }

        $this->atendimentoModel->update($id, ['encerrado' => 1, 'resultado' => 'concluido']);

        $followups = $this->followupModel->getPendentesByAtendimento($id);
        foreach ($followups as $f) {
            $this->followupModel->update((int) $f['id'], ['status' => 'concluido']);
        }

        flash('success', 'Follow-up encerrado!');
        $this->redirect(url('/leads/' . $atendimento['lead_id']));
    }
}
