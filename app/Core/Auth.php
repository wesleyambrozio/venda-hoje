<?php

declare(strict_types=1);

namespace App\Core;

class Auth
{
    private const SESSION_USER_KEY = 'auth_user';
    private const LOGIN_ATTEMPTS_KEY = '_login_attempts';
    private const LOGIN_LOCK_KEY = '_login_lock_until';
    private const MAX_ATTEMPTS = 5;
    private const LOCK_SECONDS = 300;

    public static function attempt(string $email, string $password): bool
    {
        if (self::isLoginLocked()) {
            return false;
        }

        $db = Database::getInstance();
        $stmt = $db->prepare(
            'SELECT u.*, e.nome AS empresa_nome, e.ativo AS empresa_ativa,
                    e.theme_mode, e.color_palette
             FROM usuarios u
             JOIN empresas e ON e.id = u.empresa_id
             WHERE u.email = ? AND u.ativo = 1 LIMIT 1'
        );
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if (!$user || !$user['empresa_ativa']) {
            self::registerFailedLogin();
            return false;
        }

        if (!password_verify($password, $user['senha'])) {
            self::registerFailedLogin();
            return false;
        }

        unset($user['senha']);
        session_regenerate_id(true);
        $_SESSION[self::SESSION_USER_KEY] = $user;
        unset($_SESSION[self::LOGIN_ATTEMPTS_KEY], $_SESSION[self::LOGIN_LOCK_KEY]);
        return true;
    }

    public static function isLoginLocked(): bool
    {
        $until = (int) ($_SESSION[self::LOGIN_LOCK_KEY] ?? 0);
        return $until > time();
    }

    public static function loginLockRemaining(): int
    {
        $until = (int) ($_SESSION[self::LOGIN_LOCK_KEY] ?? 0);
        return max(0, $until - time());
    }

    private static function registerFailedLogin(): void
    {
        $attempts = (int) ($_SESSION[self::LOGIN_ATTEMPTS_KEY] ?? 0) + 1;
        $_SESSION[self::LOGIN_ATTEMPTS_KEY] = $attempts;
        if ($attempts >= self::MAX_ATTEMPTS) {
            $_SESSION[self::LOGIN_LOCK_KEY] = time() + self::LOCK_SECONDS;
            $_SESSION[self::LOGIN_ATTEMPTS_KEY] = 0;
        }
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

    public static function refreshUserTheme(string $mode, string $palette): void
    {
        if (!isset($_SESSION[self::SESSION_USER_KEY])) {
            return;
        }
        $_SESSION[self::SESSION_USER_KEY]['theme_mode'] = $mode;
        $_SESSION[self::SESSION_USER_KEY]['color_palette'] = $palette;
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
