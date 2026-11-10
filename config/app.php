<?php

declare(strict_types=1);

return [
    'name' => env('APP_NAME', 'Venda Hoje CRM'),
    'env' => env('APP_ENV', 'local'),
    'debug' => filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN),
    'url' => rtrim(env('APP_URL', 'http://localhost'), '/'),
    'key' => env('APP_KEY', ''),
    'timezone' => 'America/Sao_Paulo',
    'funil_etapas' => [
        'novo_lead',
        'em_contato',
        'qualificado',
        'videochamada_agendada',
        'videochamada_realizada',
        'proposta_enviada',
        'negociacao',
        'fechado',
        'perdido',
    ],
    'funil_labels' => [
        'novo_lead' => 'Novo Lead',
        'em_contato' => 'Em Contato',
        'qualificado' => 'Qualificado',
        'videochamada_agendada' => 'Videochamada Agendada',
        'videochamada_realizada' => 'Videochamada Realizada',
        'proposta_enviada' => 'Proposta Enviada',
        'negociacao' => 'Negociação',
        'fechado' => 'Fechado',
        'perdido' => 'Perdido',
    ],
    'canais_atendimento' => ['telefone', 'whatsapp', 'email', 'videochamada', 'presencial', 'outro'],
    'perfis' => ['sdr', 'closer', 'gestor', 'admin_saas'],
    'perfil_labels' => [
        'sdr' => 'SDR',
        'closer' => 'Closer',
        'gestor' => 'Gestor',
        'admin_saas' => 'Admin SaaS',
    ],
];
