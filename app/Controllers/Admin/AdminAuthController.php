<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;

class AdminAuthController extends Controller
{
    public function showLogin(): void
    {
        if (Auth::isAdmin()) {
            $this->redirect('admin/dashboard');
        }
        $this->view('admin.auth.login', ['title' => 'Admin Login'], 'auth');
    }

    public function login(): void
    {
        $this->requireCsrf();

        $email = trim((string) $this->input('email', ''));
        $password = (string) $this->input('password', '');

        if (Auth::attempt($email, $password) && Auth::isAdmin()) {
            $this->redirect('admin/dashboard');
        }

        Auth::logout();
        $_SESSION['_errors'] = ['Incorrect admin email or password.'];
        $this->old(['email' => $email]);
        $this->redirect('admin/login');
    }

    public function logout(): void
    {
        $this->requireCsrf();
        Auth::logout();
        $this->redirect('admin/login');
    }
}
