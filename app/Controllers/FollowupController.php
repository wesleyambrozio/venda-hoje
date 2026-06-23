<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Followup;
use App\Services\FollowupService;

class FollowupController extends Controller
{
    private Followup $followupModel;

    public function __construct()
    {
        $this->followupModel = new Followup();
    }

    public function index(): void
    {
        $user = Auth::user();
        $status = $this->input('status', 'pendente');

        $followups = $this->followupModel->getFiltered(
            (int) $user['empresa_id'],
            ['status' => $status !== 'todos' ? $status : null],
            (int) $user['id'],
            $user['perfil']
        );

        $followupService = new FollowupService();
        $alertas = $followupService->getAlertasPendentes(
            (int) $user['empresa_id'],
            $user['perfil'] === 'sdr' ? (int) $user['id'] : null
        );

        $this->view('pages/followups/index', [
            'title' => 'Follow-ups',
            'followups' => $followups,
            'alertas' => $alertas,
            'status' => $status,
        ]);
    }

    public function concluir(int $id): void
    {
        $this->validateCsrf();
        $user = Auth::user();
        $followup = $this->followupModel->find($id);

        if (!$followup) {
            flash('error', 'Follow-up não encontrado.');
            $this->redirect(url('/followups'));
        }

        $funcionou = $this->input('gatilho_funcionou');
        $data = ['status' => 'concluido'];
        if ($funcionou !== null && $funcionou !== '') {
            $data['gatilho_funcionou'] = (int) $funcionou;
        }

        $this->followupModel->update($id, $data);
        flash('success', 'Follow-up concluído!');
        $this->redirect(url('/followups'));
    }
}
