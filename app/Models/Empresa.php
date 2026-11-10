<?php

declare(strict_types=1);

namespace App\Models;

use App\Core\Model;
use App\Services\ThemeService;

class Empresa extends Model
{
    protected string $table = 'empresas';

    public function updateTheme(int $empresaId, string $mode, string $palette): bool
    {
        if (!ThemeService::isValidMode($mode) || !ThemeService::isValidPalette($palette)) {
            return false;
        }

        return $this->update($empresaId, [
            'theme_mode' => $mode,
            'color_palette' => $palette,
        ]);
    }

    public function getTheme(int $empresaId): array
    {
        $empresa = $this->find($empresaId);
        return [
            'theme_mode' => $empresa['theme_mode'] ?? 'dark',
            'color_palette' => $empresa['color_palette'] ?? 'rose',
        ];
    }
}
