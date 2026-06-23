<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Atendimento;
use App\Models\Lead;

class HistoricoController extends Controller
{
    public function index(): void
    {
        $user = Auth::user();
        $atendimentoModel = new Atendimento();
        $leadModel = new Lead();

        $filters = [
            'canal' => $this->input('canal'),
            'resultado' => $this->input('resultado'),
            'data_inicio' => $this->input('data_inicio'),
            'data_fim' => $this->input('data_fim'),
            'lead_id' => $this->input('lead_id'),
        ];

        $historico = $atendimentoModel->getHistorico(
            (int) $user['empresa_id'],
            array_filter($filters),
            (int) $user['id'],
            $user['perfil']
        );

        $leads = $leadModel->getFiltered(
            (int) $user['empresa_id'],
            [],
            $user['perfil'] === 'sdr' ? (int) $user['id'] : null,
            $user['perfil']
        );

        $this->view('pages/historico/index', [
            'title' => 'Histórico de Atendimentos',
            'historico' => $historico,
            'leads' => $leads,
            'filters' => $filters,
        ]);
    }
}
