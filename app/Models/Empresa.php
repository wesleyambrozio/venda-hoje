<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Empresa extends Model
{
    protected string $table = 'empresas';

    public function getAllWithPlano(): array
    {
        return $this->query(
            "SELECT e.*, p.nome AS plano_nome, p.preco_mensal,
                    (SELECT COUNT(*) FROM usuarios u WHERE u.empresa_id = e.id) AS total_usuarios,
                    (SELECT COUNT(*) FROM leads l WHERE l.empresa_id = e.id) AS total_leads
             FROM empresas e
             LEFT JOIN planos p ON p.id = e.plano_id
             ORDER BY e.nome"
        );
    }
}
