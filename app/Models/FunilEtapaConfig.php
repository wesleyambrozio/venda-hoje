<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class FunilEtapaConfig extends Model
{
    protected string $table = 'funil_etapas_config';

    public function getByEmpresa(int $empresaId): array
    {
        return $this->query(
            'SELECT etapa_chave, rotulo, ordem FROM funil_etapas_config WHERE empresa_id = ? ORDER BY ordem, id',
            [$empresaId]
        );
    }

    public function salvar(int $empresaId, string $chave, string $rotulo, int $ordem): void
    {
        $stmt = $this->db->prepare(
            'INSERT INTO funil_etapas_config (empresa_id, etapa_chave, rotulo, ordem)
             VALUES (?, ?, ?, ?)
             ON DUPLICATE KEY UPDATE rotulo = VALUES(rotulo), ordem = VALUES(ordem)'
        );
        $stmt->execute([$empresaId, $chave, $rotulo, $ordem]);
    }
}
