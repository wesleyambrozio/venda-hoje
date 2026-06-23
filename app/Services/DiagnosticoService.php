<?php

declare(strict_types=1);

namespace App\Services;

class DiagnosticoService
{
    public function calcularScore(array $respostas, array $criterios): array
    {
        $criterioMap = [];
        foreach ($criterios as $c) {
            $criterioMap[$c['id']] = $c;
        }

        $somaPonderada = 0.0;
        $somaMaxima = 0.0;

        foreach ($respostas as $criterioId => $valor) {
            if (!isset($criterioMap[$criterioId])) {
                continue;
            }
            $peso = (int) $criterioMap[$criterioId]['peso'];
            $somaPonderada += (int) $valor * $peso;
            $somaMaxima += 5 * $peso;
        }

        $score = $somaMaxima > 0 ? ($somaPonderada / $somaMaxima) * 100 : 0;
        $classificacao = scoreClassificacao($score);

        return [
            'score' => round($score, 2),
            'classificacao' => $classificacao,
        ];
    }
}
