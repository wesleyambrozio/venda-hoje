<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Lead extends Model
{
    protected string $table = 'leads';

    public function getFiltered(int $empresaId, array $filters = [], ?int $usuarioId = null, string $perfil = 'sdr'): array
    {
        $sql = "SELECT l.*, sdr.nome AS sdr_nome, closer.nome AS closer_nome,
                       (SELECT score_final FROM diagnosticos WHERE lead_id = l.id ORDER BY created_at DESC LIMIT 1) AS ultimo_score
                FROM leads l
                LEFT JOIN usuarios sdr ON sdr.id = l.sdr_id
                LEFT JOIN usuarios closer ON closer.id = l.closer_id
                WHERE l.empresa_id = ?";
        $params = [$empresaId];

        if ($perfil === 'sdr' && $usuarioId) {
            $sql .= " AND l.sdr_id = ?";
            $params[] = $usuarioId;
        } elseif ($perfil === 'closer' && $usuarioId) {
            $sql .= " AND (l.closer_id = ? OR l.sdr_id = ?)";
            $params[] = $usuarioId;
            $params[] = $usuarioId;
        }

        if (!empty($filters['etapa'])) {
            $sql .= " AND l.etapa_funil = ?";
            $params[] = $filters['etapa'];
        }
        if (!empty($filters['origem'])) {
            $sql .= " AND l.origem LIKE ?";
            $params[] = '%' . $filters['origem'] . '%';
        }
        if (!empty($filters['responsavel'])) {
            $sql .= " AND (l.sdr_id = ? OR l.closer_id = ?)";
            $params[] = $filters['responsavel'];
            $params[] = $filters['responsavel'];
        }
        if (!empty($filters['busca'])) {
            $sql .= " AND (l.nome LIKE ? OR l.email LIKE ? OR l.empresa_nome LIKE ?)";
            $term = '%' . $filters['busca'] . '%';
            $params[] = $term;
            $params[] = $term;
            $params[] = $term;
        }

        $sql .= " ORDER BY l.updated_at DESC";
        return $this->query($sql, $params);
    }

    public function findWithDetails(int $id, int $empresaId): ?array
    {
        return $this->queryOne(
            "SELECT l.*, sdr.nome AS sdr_nome, closer.nome AS closer_nome
             FROM leads l
             LEFT JOIN usuarios sdr ON sdr.id = l.sdr_id
             LEFT JOIN usuarios closer ON closer.id = l.closer_id
             WHERE l.id = ? AND l.empresa_id = ?",
            [$id, $empresaId]
        );
    }

    public function countByEtapa(int $empresaId, ?int $usuarioId = null): array
    {
        $sql = "SELECT etapa_funil, COUNT(*) AS total FROM leads WHERE empresa_id = ?";
        $params = [$empresaId];

        if ($usuarioId) {
            $sql .= " AND sdr_id = ?";
            $params[] = $usuarioId;
        }

        $sql .= " GROUP BY etapa_funil";
        return $this->query($sql, $params);
    }

    public function canAccess(array $lead, int $usuarioId, string $perfil): bool
    {
        if (in_array($perfil, ['gestor', 'admin_saas'], true)) {
            return true;
        }
        if ($perfil === 'closer') {
            return (int) $lead['closer_id'] === $usuarioId || (int) $lead['sdr_id'] === $usuarioId;
        }
        return (int) $lead['sdr_id'] === $usuarioId;
    }
}
