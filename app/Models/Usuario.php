<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;

class Usuario extends Model
{
    protected string $table = 'usuarios';

    public function findByEmail(string $email, int $empresaId): ?array
    {
        return $this->queryOne(
            'SELECT * FROM usuarios WHERE email = ? AND empresa_id = ? LIMIT 1',
            [$email, $empresaId]
        );
    }

    public function getByEmpresa(int $empresaId): array
    {
        return $this->query(
            'SELECT id, nome, email, perfil, ativo, created_at FROM usuarios WHERE empresa_id = ? ORDER BY nome',
            [$empresaId]
        );
    }

    public function getComerciais(int $empresaId): array
    {
        return $this->query(
            "SELECT id, nome, perfil FROM usuarios WHERE empresa_id = ? AND perfil IN ('sdr','closer') AND ativo = 1 ORDER BY nome",
            [$empresaId]
        );
    }
}
