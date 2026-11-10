<?php

declare(strict_types=1);

namespace App\Services;

class ThemeService
{
    public const PALETTES = ['indigo', 'emerald', 'rose', 'amber', 'cyan', 'violet'];
    public const MODES = ['light', 'dark'];

    public static function paletteLabels(): array
    {
        return [
            'indigo' => 'Índigo',
            'emerald' => 'Esmeralda',
            'rose' => 'Rose',
            'amber' => 'Âmbar',
            'cyan' => 'Ciano',
            'violet' => 'Violeta',
        ];
    }

    public static function paletteColors(): array
    {
        return [
            'indigo' => '#6366f1',
            'emerald' => '#10b981',
            'rose' => '#f43f5e',
            'amber' => '#f59e0b',
            'cyan' => '#06b6d4',
            'violet' => '#8b5cf6',
        ];
    }

    public static function isValidPalette(string $palette): bool
    {
        return in_array($palette, self::PALETTES, true);
    }

    public static function isValidMode(string $mode): bool
    {
        return in_array($mode, self::MODES, true);
    }

    public static function abordagemOpcoes(): array
    {
        return [
            'descoberta' => [
                'label' => 'Descoberta',
                'texto' => 'Qual o maior desafio do seu time comercial hoje?',
            ],
            'pergunta_personalizada' => [
                'label' => 'Pergunta personalizada',
                'texto' => 'Vi que sua empresa tem crescido — o que mais tem atrapalhado o time comercial neste momento?',
            ],
            'case_sucesso' => [
                'label' => 'Case de sucesso',
                'texto' => 'A empresa X, do mesmo porte que a sua, aumentou conversão em 40% nos primeiros 60 dias.',
            ],
        ];
    }
}
