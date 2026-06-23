<?php

declare(strict_types=1);

if (!function_exists('env')) {
    function env(string $key, mixed $default = null): mixed
    {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? null;
        if ($value === null) {
            return $default;
        }
        return match (strtolower((string) $value)) {
            'true', '(true)' => true,
            'false', '(false)' => false,
            'empty', '(empty)' => '',
            'null', '(null)' => null,
            default => $value,
        };
    }
}

if (!function_exists('config')) {
    function config(string $key, mixed $default = null): mixed
    {
        static $configs = [];
        [$file, $item] = array_pad(explode('.', $key, 2), 2, null);

        if (!isset($configs[$file])) {
            $path = BASE_PATH . "/config/{$file}.php";
            $configs[$file] = file_exists($path) ? require $path : [];
        }

        if ($item === null) {
            return $configs[$file];
        }

        return $configs[$file][$item] ?? $default;
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $base = rtrim(config('app.url'), '/');
        $path = $path === '' ? '' : '/' . ltrim($path, '/');
        return $base . $path;
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('flash')) {
    function flash(string $type, string $message): void
    {
        $_SESSION['_flash'][$type] = $message;
    }
}

if (!function_exists('getFlash')) {
    function getFlash(): array
    {
        $flash = $_SESSION['_flash'] ?? [];
        unset($_SESSION['_flash']);
        return $flash;
    }
}

if (!function_exists('old')) {
    function old(string $key, mixed $default = ''): mixed
    {
        return $_SESSION['_old'][$key] ?? $default;
    }
}

if (!function_exists('setOld')) {
    function setOld(array $data): void
    {
        $_SESSION['_old'] = $data;
    }
}

if (!function_exists('clearOld')) {
    function clearOld(): void
    {
        unset($_SESSION['_old']);
    }
}

if (!function_exists('e')) {
    function e(?string $value): string
    {
        return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('formatDate')) {
    function formatDate(?string $date, string $format = 'd/m/Y'): string
    {
        if (!$date) {
            return '-';
        }
        return (new DateTime($date))->format($format);
    }
}

if (!function_exists('formatDateTime')) {
    function formatDateTime(?string $date): string
    {
        return formatDate($date, 'd/m/Y H:i');
    }
}

if (!function_exists('funilLabel')) {
    function funilLabel(string $etapa): string
    {
        return config('app.funil_labels')[$etapa] ?? $etapa;
    }
}

if (!function_exists('perfilLabel')) {
    function perfilLabel(string $perfil): string
    {
        return config('app.perfil_labels')[$perfil] ?? $perfil;
    }
}

if (!function_exists('scoreClassificacao')) {
    function scoreClassificacao(float $score): string
    {
        if ($score >= 75) {
            return 'quente';
        }
        if ($score >= 50) {
            return 'morno';
        }
        return 'frio';
    }
}

if (!function_exists('scoreClassificacaoLabel')) {
    function scoreClassificacaoLabel(float $score): string
    {
        return match (scoreClassificacao($score)) {
            'quente' => 'Quente',
            'morno' => 'Morno',
            default => 'Frio',
        };
    }
}
