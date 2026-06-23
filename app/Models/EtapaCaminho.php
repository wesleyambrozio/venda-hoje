<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class EtapaCaminho extends Model
{
    protected string $table = 'etapas_caminho';

    public function getByEtapaFunil(int $empresaId, string $etapaFunil): array
    {
        return $this->query(
            "SELECT * FROM etapas_caminho
             WHERE (empresa_id = ? OR empresa_id IS NULL) AND etapa_funil = ? AND ativo = 1
             ORDER BY ordem",
            [$empresaId, $etapaFunil]
        );
    }
}
