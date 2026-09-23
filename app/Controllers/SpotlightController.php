<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Setting;
use App\Models\User;

class SpotlightController extends Controller
{
    public function index(): void
    {
        $this->requireVisibility('spotlight');

        $this->view('spotlight.index', [
            'title' => 'Alumni Spotlight',
            'activeNav' => 'spotlight',
            'alumni' => User::spotlighted(),
            'nominateEmail' => Setting::get('spotlight_nominate_email', 'alumni@rbsn.example.com'),
        ]);
    }
}
