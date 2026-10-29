<?php

namespace App\Controllers\Admin;

use App\Models\DonationConfirmation;

class DonationConfirmationController extends AdminController
{
    public function index(): void
    {
        $p = $this->paginate(DonationConfirmation::countAll(), 30);
        $this->view('admin.donation_confirmations.index', [
            'title' => 'Donation Confirmations',
            'activeNav' => 'donation_confirmations',
            'confirmations' => DonationConfirmation::paginateAll($p['perPage'], $p['offset']),
            ...$p,
        ]);
    }
}
