<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;

abstract class AdminController extends Controller
{
    public function __construct()
    {
        if (!Auth::isAdmin() || !Auth::user()) {
            Auth::logout();
            header('Location: ' . url('admin/login'));
            exit;
        }
    }

    protected function view(string $view, array $data = [], string|false $layout = 'admin'): void
    {
        parent::view($view, $data, $layout);
    }
}
