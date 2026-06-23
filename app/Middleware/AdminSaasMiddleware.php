<?php

declare(strict_types=1);

namespace App\Middleware;

class AdminSaasMiddleware extends BaseRoleMiddleware
{
    protected function allowedRoles(): array
    {
        return ['admin_saas'];
    }
}
