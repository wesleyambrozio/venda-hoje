<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Gatilho;

class GatilhoController extends Controller
{
    private Gatilho $gatilhoModel;

    public function __construct()
    {
        $this->gatilhoModel = new Gatilho();
    }

    public function index(): void
    {
        $user = Auth::user();
        $categoria = $this->input('categoria');
        $etapa = $this->input('etapa');

        $gatilhos = $this->gatilhoModel->getByEmpresa(
            (int) $user['empresa_id'],
            $etapa ?: null,
            $categoria ?: null
        );

        $categorias = $this->gatilhoModel->getCategorias((int) $user['empresa_id']);
        $favoritos = $this->gatilhoModel->getFavoritos((int) $user['id'], (int) $user['empresa_id']);
        $favoritoIds = array_column($favoritos, 'id');

        $this->view('pages/gatilhos/index', [
            'title' => 'Biblioteca de Gatilhos',
            'gatilhos' => $gatilhos,
            'categorias' => $categorias,
            'favoritoIds' => $favoritoIds,
            'filters' => ['categoria' => $categoria, 'etapa' => $etapa],
        ]);
    }

    public function toggleFavorito(int $id): void
    {
        $this->validateCsrf();
        $user = Auth::user();
        $isFav = $this->gatilhoModel->toggleFavorito((int) $user['id'], $id);
        flash('success', $isFav ? 'Gatilho favoritado!' : 'Gatilho removido dos favoritos.');
        $this->redirect(url('/gatilhos'));
    }
}
