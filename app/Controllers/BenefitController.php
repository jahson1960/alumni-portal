<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Benefit;

class BenefitController extends Controller
{
    public function index(): void
    {
        $this->requireVisibility('benefits');

        $benefits = Benefit::all('category ASC, title ASC');
        $grouped = [];
        foreach ($benefits as $benefit) {
            $grouped[$benefit['category'] ?: 'General'][] = $benefit;
        }

        $this->view('benefits.index', [
            'title' => 'Alumni Benefits',
            'activeNav' => 'benefits',
            'grouped' => $grouped,
        ]);
    }
}
