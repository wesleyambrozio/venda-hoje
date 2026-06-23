<?php

declare(strict_types=1);

namespace App\Core;

class View
{
    public static function render(string $view, array $data = [], ?string $layout = 'main'): void
    {
        extract($data);
        $csrf = Auth::csrfToken();
        $user = Auth::user();
        $flash = getFlash();

        ob_start();
        $viewPath = BASE_PATH . '/app/Views/' . str_replace('.', '/', $view) . '.php';
        if (!file_exists($viewPath)) {
            throw new \RuntimeException("View não encontrada: {$view}");
        }
        require $viewPath;
        $content = ob_get_clean();

        if ($layout) {
            $layoutPath = BASE_PATH . '/app/Views/layouts/' . $layout . '.php';
            if (!file_exists($layoutPath)) {
                echo $content;
                return;
            }
            require $layoutPath;
        } else {
            echo $content;
        }
    }

    public static function partial(string $partial, array $data = []): void
    {
        extract($data);
        $path = BASE_PATH . '/app/Views/' . str_replace('.', '/', $partial) . '.php';
        if (file_exists($path)) {
            require $path;
        }
    }
}
