<?php

declare(strict_types=1);

namespace App\Core;

class Auth
{
    private const SESSION_USER_KEY = 'auth_user';

    public static function attempt(string $email, string $password): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT u.*, e.nome AS empresa_nome, e.ativo AS empresa_ativa
             FROM usuarios u
             JOIN empresas e ON e.id = u.empresa_id
             WHERE u.email = ? AND u.ativo = 1 LIMIT 1'
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !$user['empresa_ativa']) {
            return false;
        }

        if (!password_verify($password, $user['senha'])) {
            return false;
        }

        unset($user['senha']);
        $_SESSION[self::SESSION_USER_KEY] = $user;
        return true;
    }

    public static function logout(): void
    {
        unset($_SESSION[self::SESSION_USER_KEY]);
        session_regenerate_id(true);
    }

    public static function check(): bool
    {
        return isset($_SESSION[self::SESSION_USER_KEY]);
    }

    public static function user(): ?array
    {
        return $_SESSION[self::SESSION_USER_KEY] ?? null;
    }

    public static function id(): ?int
    {
        return self::user()['id'] ?? null;
    }

    public static function perfil(): ?string
    {
        return self::user()['perfil'] ?? null;
    }

    public static function empresaId(): ?int
    {
        return self::user()['empresa_id'] ?? null;
    }

    public static function hasPerfil(string|array $perfis): bool
    {
        $perfil = self::perfil();
        if (!$perfil) {
            return false;
        }
        $perfis = is_array($perfis) ? $perfis : [$perfis];
        return in_array($perfil, $perfis, true);
    }

    public static function csrfToken(): string
    {
        if (empty($_SESSION['_csrf_token'])) {
            $_SESSION['_csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_csrf_token'];
    }

    public static function validateCsrf(string $token): bool
    {
        return hash_equals($_SESSION['_csrf_token'] ?? '', $token);
    }
}
