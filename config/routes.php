<?php

declare(strict_types=1);

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\LeadController;
use App\Controllers\AtendimentoController;
use App\Controllers\DiagnosticoController;
use App\Controllers\GatilhoController;
use App\Controllers\HistoricoController;
use App\Controllers\FollowupController;
use App\Controllers\EmpresaController;
use App\Controllers\ConfigController;
use App\Middleware\AuthMiddleware;
use App\Middleware\GuestMiddleware;
use App\Middleware\CloserMiddleware;
use App\Middleware\GestorMiddleware;
use App\Middleware\AdminSaasMiddleware;

$auth = AuthMiddleware::class;
$guest = GuestMiddleware::class;
$closer = CloserMiddleware::class;
$gestor = GestorMiddleware::class;
$admin = AdminSaasMiddleware::class;

// Auth
$router->get('/login', [AuthController::class, 'showLogin'], [$guest]);
$router->post('/login', [AuthController::class, 'login'], [$guest]);
$router->get('/logout', [AuthController::class, 'logout'], [$auth]);

// Dashboard
$router->get('/', [DashboardController::class, 'index'], [$auth]);
$router->get('/dashboard', [DashboardController::class, 'index'], [$auth]);

// Leads
$router->get('/leads', [LeadController::class, 'index'], [$auth]);
$router->get('/leads/novo', [LeadController::class, 'create'], [$auth]);
$router->post('/leads', [LeadController::class, 'store'], [$auth]);
$router->get('/leads/{id}', [LeadController::class, 'show'], [$auth]);
$router->get('/leads/{id}/editar', [LeadController::class, 'edit'], [$auth]);
$router->post('/leads/{id}', [LeadController::class, 'update'], [$auth]);
$router->post('/leads/{id}/excluir', [LeadController::class, 'delete'], [$auth, $gestor]);

// Atendimentos
$router->get('/leads/{leadId}/atendimento', [AtendimentoController::class, 'create'], [$auth]);
$router->post('/leads/{leadId}/atendimento', [AtendimentoController::class, 'store'], [$auth]);
$router->post('/atendimentos/{id}/encerrar-followup', [AtendimentoController::class, 'encerrarFollowUp'], [$auth]);

// Diagnóstico
$router->get('/leads/{leadId}/diagnostico', [DiagnosticoController::class, 'create'], [$auth, $closer]);
$router->post('/leads/{leadId}/diagnostico', [DiagnosticoController::class, 'store'], [$auth, $closer]);
$router->get('/diagnosticos/{id}', [DiagnosticoController::class, 'show'], [$auth]);

// Gatilhos
$router->get('/gatilhos', [GatilhoController::class, 'index'], [$auth]);
$router->post('/gatilhos/{id}/favorito', [GatilhoController::class, 'toggleFavorito'], [$auth]);

// Histórico
$router->get('/historico', [HistoricoController::class, 'index'], [$auth]);

// Follow-ups
$router->get('/followups', [FollowupController::class, 'index'], [$auth]);
$router->post('/followups/{id}/concluir', [FollowupController::class, 'concluir'], [$auth]);

// Admin SaaS - Empresas
$router->get('/admin/empresas', [EmpresaController::class, 'index'], [$auth, $admin]);
$router->get('/admin/empresas/nova', [EmpresaController::class, 'create'], [$auth, $admin]);
$router->post('/admin/empresas', [EmpresaController::class, 'store'], [$auth, $admin]);

// Config (Gestor)
$router->get('/config', [ConfigController::class, 'index'], [$auth, $gestor]);
$router->get('/config/usuarios', [ConfigController::class, 'usuarios'], [$auth, $gestor]);
$router->post('/config/usuarios', [ConfigController::class, 'storeUsuario'], [$auth, $gestor]);
$router->get('/config/gatilhos', [ConfigController::class, 'gatilhos'], [$auth, $gestor]);
$router->post('/config/gatilhos', [ConfigController::class, 'storeGatilho'], [$auth, $gestor]);
$router->get('/config/criterios', [ConfigController::class, 'criterios'], [$auth, $gestor]);
$router->post('/config/criterios/{id}', [ConfigController::class, 'updateCriterio'], [$auth, $gestor]);
