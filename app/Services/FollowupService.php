<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Gatilho;

class FollowupService
{
    public function sugerirGatilho(int $empresaId, ?string $etapaFunil = null): ?array
    {
        $gatilhoModel = new Gatilho();
        $gatilhos = $gatilhoModel->getByEmpresa($empresaId, $etapaFunil);

        if (empty($gatilhos)) {
            return null;
        }

        return $gatilhos[array_rand($gatilhos)];
    }

    public function getAlertasPendentes(int $empresaId, ?int $usuarioId = null): array
    {
        $sql = "SELECT f.*, l.nome AS lead_nome, l.etapa_funil, g.titulo AS gatilho_titulo, g.texto AS gatilho_texto,
                       u.nome AS usuario_nome
                FROM followups f
                JOIN leads l ON l.id = f.lead_id
                JOIN usuarios u ON u.id = f.usuario_id
                LEFT JOIN gatilhos g ON g.id = f.gatilho_sugerido_id
                WHERE l.empresa_id = ? AND f.status = 'pendente' AND f.data_retorno <= NOW()";

        $params = [$empresaId];

        if ($usuarioId) {
            $sql .= " AND f.usuario_id = ?";
            $params[] = $usuarioId;
        }

        $sql .= " ORDER BY f.data_retorno ASC";

        $model = new \App\Models\Followup();
        return $model->rawQuery($sql, $params);
    }
}
