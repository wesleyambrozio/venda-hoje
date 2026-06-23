<?php

declare(strict_types=1);

namespace App\Middleware;

class CloserMiddleware extends BaseRoleMiddleware
{
    protected function allowedRoles(): array
    {
        return ['closer', 'gestor', 'admin_saas'];
    }
}
