<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Followup extends Model
{
    protected string $table = 'followups';

    public function rawQuery(string $sql, array $params = []): array
    {
        return $this->query($sql, $params);
    }

    public function getFiltered(int $empresaId, array $filters = [], ?int $usuarioId = null, string $perfil = 'sdr'): array
    {
        $sql = "SELECT f.*, l.nome AS lead_nome, l.etapa_funil, g.titulo AS gatilho_titulo, u.nome AS usuario_nome
                FROM followups f
                JOIN leads l ON l.id = f.lead_id
                JOIN usuarios u ON u.id = f.usuario_id
                LEFT JOIN gatilhos g ON g.id = f.gatilho_sugerido_id
                WHERE l.empresa_id = ?";
        $params = [$empresaId];

        if ($perfil === 'sdr' && $usuarioId) {
            $sql .= " AND f.usuario_id = ?";
            $params[] = $usuarioId;
        }

        if (!empty($filters['status'])) {
            $sql .= " AND f.status = ?";
            $params[] = $filters['status'];
        }

        $sql .= " ORDER BY f.data_retorno ASC";
        return $this->query($sql, $params);
    }

    public function getPendentesByAtendimento(int $atendimentoId): array
    {
        return $this->query(
            "SELECT id FROM followups WHERE atendimento_id = ? AND status = 'pendente'",
            [$atendimentoId]
        );
    }

    public function countPendentes(int $empresaId, ?int $usuarioId = null): int
    {
        $sql = "SELECT COUNT(*) AS total FROM followups f
                JOIN leads l ON l.id = f.lead_id
                WHERE l.empresa_id = ? AND f.status = 'pendente'";
        $params = [$empresaId];

        if ($usuarioId) {
            $sql .= " AND f.usuario_id = ?";
            $params[] = $usuarioId;
        }

        $result = $this->queryOne($sql, $params);
        return (int) ($result['total'] ?? 0);
    }
}
