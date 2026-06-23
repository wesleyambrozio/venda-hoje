<?php

declare(strict_types=1);

namespace App\Middleware;

class GestorMiddleware extends BaseRoleMiddleware
{
    protected function allowedRoles(): array
    {
        return ['gestor', 'admin_saas'];
    }
}
