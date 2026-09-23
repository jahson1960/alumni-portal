<?php

namespace App\Controllers;

use App\Core\Controller;

class ErrorController extends Controller
{
    public function notFound(): void
    {
        $this->view('errors.404', ['title' => 'Page Not Found', 'activeNav' => '']);
    }
}
