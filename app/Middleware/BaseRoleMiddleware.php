<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Core\Auth;

abstract class BaseRoleMiddleware
{
    abstract protected function allowedRoles(): array;

    public function handle(): void
    {
        if (!Auth::check()) {
            header('Location: ' . url('/login'));
            exit;
        }

        if (!Auth::hasPerfil($this->allowedRoles())) {
            http_response_code(403);
            flash('error', 'Você não tem permissão para acessar esta área.');
            header('Location: ' . url('/dashboard'));
            exit;
        }
    }
}
