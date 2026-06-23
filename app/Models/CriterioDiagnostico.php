<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class CriterioDiagnostico extends Model
{
    protected string $table = 'criterios_diagnostico';

    public function getComOpcoes(int $empresaId): array
    {
        $criterios = $this->query(
            "SELECT * FROM criterios_diagnostico
             WHERE (empresa_id = ? OR empresa_id IS NULL) AND ativo = 1
             ORDER BY ordem",
            [$empresaId]
        );

        foreach ($criterios as &$criterio) {
            $criterio['opcoes'] = $this->query(
                'SELECT * FROM criterio_opcoes WHERE criterio_id = ? ORDER BY valor',
                [$criterio['id']]
            );
        }

        return $criterios;
    }

    public function getBlocos(int $empresaId): array
    {
        return $this->query(
            "SELECT DISTINCT bloco FROM criterios_diagnostico
             WHERE (empresa_id = ? OR empresa_id IS NULL) AND ativo = 1
             ORDER BY MIN(ordem)",
            [$empresaId]
        );
    }
}
