<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\FunilEtapaConfig;

class FunilService
{
    public function getEtapas(int $empresaId): array
    {
        $padrao = config('app.funil_etapas');
        $labels = config('app.funil_labels');
        $salvas = [];
        foreach ((new FunilEtapaConfig())->getByEmpresa($empresaId) as $etapa) {
            $salvas[$etapa['etapa_chave']] = $etapa;
        }

        $etapas = [];
        foreach ($padrao as $indice => $chave) {
            $config = $salvas[$chave] ?? [];
            $etapas[] = [
                'chave' => $chave,
                'rotulo' => trim((string) ($config['rotulo'] ?? $labels[$chave] ?? $chave)) ?: $chave,
                'ordem' => (int) ($config['ordem'] ?? ($indice + 1)),
            ];
        }

        usort($etapas, static fn (array $a, array $b): int => $a['ordem'] <=> $b['ordem']);
        return $etapas;
    }

    public function labels(int $empresaId): array
    {
        $labels = [];
        foreach ($this->getEtapas($empresaId) as $etapa) {
            $labels[$etapa['chave']] = $etapa['rotulo'];
        }
        return $labels;
    }
}
