<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Lead;
use App\Models\Atendimento;
use App\Models\Diagnostico;
use App\Models\Gatilho;
use App\Models\Followup;

class AnalyticsService
{
    public function getDashboardMetrics(int $empresaId, ?int $usuarioId = null, string $perfil = 'sdr'): array
    {
        $leadModel = new Lead();
        $atendimentoModel = new Atendimento();
        $diagnosticoModel = new Diagnostico();
        $gatilhoModel = new Gatilho();
        $followupModel = new Followup();

        $filterUser = ($perfil === 'sdr') ? $usuarioId : null;

        $leadsPorEtapa = $leadModel->countByEtapa($empresaId, $filterUser);
        $totalLeads = array_sum(array_column($leadsPorEtapa, 'total'));
        $fechados = 0;
        $perdidos = 0;

        foreach ($leadsPorEtapa as $item) {
            if ($item['etapa_funil'] === 'fechado') {
                $fechados = (int) $item['total'];
            }
            if ($item['etapa_funil'] === 'perdido') {
                $perdidos = (int) $item['total'];
            }
        }

        $conversao = $totalLeads > 0 ? round(($fechados / $totalLeads) * 100, 1) : 0;
        $scoreMedio = $diagnosticoModel->scoreMedio($empresaId, $filterUser);
        $rankingGatilhos = $gatilhoModel->rankingEfetividade($empresaId);
        $followupsPendentes = $followupModel->countPendentes($empresaId, $filterUser);
        $atendimentosMes = $atendimentoModel->countMes($empresaId, $filterUser);

        return [
            'total_leads' => $totalLeads,
            'fechados' => $fechados,
            'perdidos' => $perdidos,
            'conversao' => $conversao,
            'score_medio' => $scoreMedio,
            'leads_por_etapa' => $leadsPorEtapa,
            'ranking_gatilhos' => $rankingGatilhos,
            'followups_pendentes' => $followupsPendentes,
            'atendimentos_mes' => $atendimentosMes,
        ];
    }
}
