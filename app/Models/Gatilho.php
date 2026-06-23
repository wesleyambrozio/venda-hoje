<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Gatilho extends Model
{
    protected string $table = 'gatilhos';

    public function getByEmpresa(int $empresaId, ?string $etapaFunil = null, ?string $categoria = null): array
    {
        $sql = "SELECT g.*, (SELECT COUNT(*) FROM gatilho_favoritos gf WHERE gf.gatilho_id = g.id) AS favoritos_count
                FROM gatilhos g
                WHERE (g.empresa_id = ? OR g.empresa_id IS NULL) AND g.ativo = 1";
        $params = [$empresaId];

        if ($etapaFunil) {
            $sql .= " AND (g.etapa_funil = ? OR g.etapa_funil IS NULL)";
            $params[] = $etapaFunil;
        }
        if ($categoria) {
            $sql .= " AND g.categoria = ?";
            $params[] = $categoria;
        }

        $sql .= " ORDER BY g.categoria, g.titulo";
        return $this->query($sql, $params);
    }

    public function getCategorias(int $empresaId): array
    {
        return $this->query(
            "SELECT DISTINCT categoria FROM gatilhos WHERE (empresa_id = ? OR empresa_id IS NULL) AND ativo = 1 ORDER BY categoria",
            [$empresaId]
        );
    }

    public function toggleFavorito(int $usuarioId, int $gatilhoId): bool
    {
        $exists = $this->queryOne(
            'SELECT id FROM gatilho_favoritos WHERE usuario_id = ? AND gatilho_id = ?',
            [$usuarioId, $gatilhoId]
        );

        if ($exists) {
            $stmt = $this->db->prepare('DELETE FROM gatilho_favoritos WHERE usuario_id = ? AND gatilho_id = ?');
            $stmt->execute([$usuarioId, $gatilhoId]);
            return false;
        }

        $stmt = $this->db->prepare('INSERT INTO gatilho_favoritos (usuario_id, gatilho_id) VALUES (?, ?)');
        $stmt->execute([$usuarioId, $gatilhoId]);
        return true;
    }

    public function getFavoritos(int $usuarioId, int $empresaId): array
    {
        return $this->query(
            "SELECT g.* FROM gatilhos g
             JOIN gatilho_favoritos gf ON gf.gatilho_id = g.id
             WHERE gf.usuario_id = ? AND (g.empresa_id = ? OR g.empresa_id IS NULL) AND g.ativo = 1
             ORDER BY g.titulo",
            [$usuarioId, $empresaId]
        );
    }

    public function rankingEfetividade(int $empresaId): array
    {
        return $this->query(
            "SELECT g.id, g.titulo, g.categoria,
                    COUNT(ag.id) AS total_usos,
                    SUM(CASE WHEN ag.funcionou = 1 THEN 1 ELSE 0 END) AS funcionou_sim,
                    SUM(CASE WHEN ag.funcionou = 0 THEN 1 ELSE 0 END) AS funcionou_nao,
                    ROUND(SUM(CASE WHEN ag.funcionou = 1 THEN 1 ELSE 0 END) / NULLIF(COUNT(ag.id), 0) * 100, 1) AS taxa_sucesso
             FROM gatilhos g
             LEFT JOIN atendimento_gatilhos ag ON ag.gatilho_id = g.id
             LEFT JOIN atendimentos a ON a.id = ag.atendimento_id
             LEFT JOIN leads l ON l.id = a.lead_id AND l.empresa_id = ?
             WHERE (g.empresa_id = ? OR g.empresa_id IS NULL)
             GROUP BY g.id, g.titulo, g.categoria
             HAVING total_usos > 0
             ORDER BY taxa_sucesso DESC, total_usos DESC
             LIMIT 10",
            [$empresaId, $empresaId]
        );
    }
}
