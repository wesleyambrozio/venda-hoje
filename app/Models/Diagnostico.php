<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Diagnostico extends Model
{
    protected string $table = 'diagnosticos';

    public function getByLead(int $leadId): array
    {
        return $this->query(
            "SELECT d.*, u.nome AS usuario_nome
             FROM diagnosticos d
             JOIN usuarios u ON u.id = d.usuario_id
             WHERE d.lead_id = ?
             ORDER BY d.created_at DESC",
            [$leadId]
        );
    }

    public function getRespostas(int $diagnosticoId): array
    {
        return $this->query(
            "SELECT dr.*, c.nome AS criterio_nome, c.bloco, c.peso
             FROM diagnostico_respostas dr
             JOIN criterios_diagnostico c ON c.id = dr.criterio_id
             WHERE dr.diagnostico_id = ?
             ORDER BY c.ordem",
            [$diagnosticoId]
        );
    }

    public function saveRespostas(int $diagnosticoId, array $respostas): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO diagnostico_respostas (diagnostico_id, criterio_id, valor) VALUES (?, ?, ?)'
        );
        foreach ($respostas as $criterioId => $valor) {
            $stmt->execute([$diagnosticoId, $criterioId, $valor]);
        }
    }

    public function scoreMedio(int $empresaId, ?int $usuarioId = null): float
    {
        $sql = "SELECT AVG(d.score_final) AS media
                FROM diagnosticos d
                JOIN leads l ON l.id = d.lead_id
                WHERE l.empresa_id = ?";
        $params = [$empresaId];

        if ($usuarioId) {
            $sql .= " AND d.usuario_id = ?";
            $params[] = $usuarioId;
        }

        $result = $this->queryOne($sql, $params);
        return round((float) ($result['media'] ?? 0), 1);
    }
}
