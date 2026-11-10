<?php

declare(strict_types=1);

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        $this->view('pages/auth/login', ['title' => 'Login'], 'guest');
    }

    public function login(): void
    {
        $this->validateCsrf();

        if (Auth::isLoginLocked()) {
            $mins = (int) ceil(Auth::loginLockRemaining() / 60);
            flash('error', "Muitas tentativas. Aguarde {$mins} minuto(s) e tente novamente.");
            $this->redirect(url('/login'));
        }

        $email = trim((string) $this->input('email'));
        $password = (string) $this->input('password');

        if ($email === '' || $password === '') {
            flash('error', 'Preencha email e senha.');
            setOld(['email' => $email]);
            $this->redirect(url('/login'));
        }

        if (Auth::attempt($email, $password)) {
            clearOld();
            flash('success', 'Bem-vindo de volta!');
            $this->redirect(url('/dashboard'));
        }

        flash('error', 'Credenciais inválidas ou conta inativa.');
        setOld(['email' => $email]);
        $this->redirect(url('/login'));
    }

    public function logout(): void
    {
        $this->validateCsrf();
        Auth::logout();
        flash('success', 'Logout realizado com sucesso.');
        $this->redirect(url('/login'));
    }
}
