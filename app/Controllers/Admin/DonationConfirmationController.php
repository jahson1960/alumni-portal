<?php

namespace App\Controllers\Admin;

use App\Models\DonationConfirmation;

class DonationConfirmationController extends AdminController
{
    public function index(): void
    {
        $this->view('admin.donation_confirmations.index', [
            'title' => 'Donation Confirmations',
            'activeNav' => 'donation_confirmations',
            'confirmations' => DonationConfirmation::all(),
        ]);
    }
}
