<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Services\AnalyticsService;
use App\Services\FollowupService;

class DashboardController extends Controller
{
    public function index(): void
    {
        $user = Auth::user();
        $analytics = new AnalyticsService();
        $followupService = new FollowupService();

        $metrics = $analytics->getDashboardMetrics(
            (int) $user['empresa_id'],
            (int) $user['id'],
            $user['perfil']
        );

        $alertas = $followupService->getAlertasPendentes(
            (int) $user['empresa_id'],
            $user['perfil'] === 'sdr' ? (int) $user['id'] : null
        );

        $this->view('pages/dashboard/index', [
            'title' => 'Dashboard',
            'metrics' => $metrics,
            'alertas' => $alertas,
        ]);
    }
}
