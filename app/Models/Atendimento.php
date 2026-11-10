<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Atendimento extends Model
{
    protected string $table = 'atendimentos';

    public function getByLead(int $leadId): array
    {
        return $this->query(
            "SELECT a.*, u.nome AS usuario_nome
             FROM atendimentos a
             JOIN usuarios u ON u.id = a.usuario_id
             WHERE a.lead_id = ?
             ORDER BY a.data_atendimento DESC",
            [$leadId]
        );
    }

    public function getHistorico(int $empresaId, array $filters = [], ?int $usuarioId = null, string $perfil = 'sdr'): array
    {
        $sql = "SELECT a.*, l.nome AS lead_nome, l.etapa_funil, u.nome AS usuario_nome
                FROM atendimentos a
                JOIN leads l ON l.id = a.lead_id
                JOIN usuarios u ON u.id = a.usuario_id
                WHERE l.empresa_id = ?";
        $params = [$empresaId];

        if ($perfil === 'sdr' && $usuarioId) {
            $sql .= " AND a.usuario_id = ?";
            $params[] = $usuarioId;
        }

        if (!empty($filters['canal'])) {
            $sql .= " AND a.canal = ?";
            $params[] = $filters['canal'];
        }
        if (!empty($filters['resultado'])) {
            $sql .= " AND a.resultado = ?";
            $params[] = $filters['resultado'];
        }
        if (!empty($filters['data_inicio'])) {
            $sql .= " AND DATE(a.data_atendimento) >= ?";
            $params[] = $filters['data_inicio'];
        }
        if (!empty($filters['data_fim'])) {
            $sql .= " AND DATE(a.data_atendimento) <= ?";
            $params[] = $filters['data_fim'];
        }
        if (!empty($filters['lead_id'])) {
            $sql .= " AND a.lead_id = ?";
            $params[] = $filters['lead_id'];
        }

        $sql .= " ORDER BY a.data_atendimento DESC LIMIT 200";
        return $this->query($sql, $params);
    }

    public function getGatilhosUsados(int $atendimentoId): array
    {
        return $this->query(
            "SELECT ag.*, g.titulo, g.categoria, g.texto
             FROM atendimento_gatilhos ag
             JOIN gatilhos g ON g.id = ag.gatilho_id
             WHERE ag.atendimento_id = ?",
            [$atendimentoId]
        );
    }

    public function addGatilho(int $atendimentoId, int $gatilhoId, ?bool $funcionou, ?string $observacao): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO atendimento_gatilhos (atendimento_id, gatilho_id, funcionou, observacao) VALUES (?, ?, ?, ?)'
        );
        $stmt->execute([$atendimentoId, $gatilhoId, $funcionou, $observacao]);
    }

    public function countMes(int $empresaId, ?int $usuarioId = null): int
    {
        $sql = "SELECT COUNT(*) AS total FROM atendimentos a
                JOIN leads l ON l.id = a.lead_id
                WHERE l.empresa_id = ? AND MONTH(a.data_atendimento) = MONTH(NOW()) AND YEAR(a.data_atendimento) = YEAR(NOW())";
        $params = [$empresaId];

        if ($usuarioId) {
            $sql .= " AND a.usuario_id = ?";
            $params[] = $usuarioId;
        }

        $result = $this->queryOne($sql, $params);
        return (int) ($result['total'] ?? 0);
    }

    public function hasFollowUpAberto(int $leadId): bool
    {
        $result = $this->queryOne(
            "SELECT COUNT(*) AS total
             FROM followups f
             JOIN atendimentos a ON a.id = f.atendimento_id
             WHERE a.lead_id = ? AND f.status = 'pendente'",
            [$leadId]
        );
        return ((int) ($result['total'] ?? 0)) > 0;
    }
}
