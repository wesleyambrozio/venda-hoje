<?php

declare(strict_types=1);

return [
    'session_lifetime' => (int) env('SESSION_LIFETIME', 7200),
    'session_name' => env('SESSION_NAME', 'venda_hoje_session'),
    'perfis_hierarquia' => [
        'sdr' => 1,
        'closer' => 2,
        'gestor' => 3,
        'admin_saas' => 4,
    ],
];
