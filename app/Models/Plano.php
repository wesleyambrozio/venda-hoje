<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Plano extends Model
{
    protected string $table = 'planos';

    public function getAtivos(): array
    {
        return $this->query('SELECT * FROM planos WHERE ativo = 1 ORDER BY preco_mensal');
    }
}
