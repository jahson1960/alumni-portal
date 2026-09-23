<?php

namespace App\Controllers\Admin;

use App\Models\DonationMethod;

class DonationMethodController extends AdminController
{
    public function index(): void
    {
        $this->view('admin.donation_methods.index', [
            'title' => 'Donation Methods',
            'activeNav' => 'donation_methods',
            'methods' => DonationMethod::all(),
        ]);
    }

    public function create(): void
    {
        $this->view('admin.donation_methods.form', [
            'title' => 'New Donation Method',
            'activeNav' => 'donation_methods',
            'method' => null,
        ]);
    }

    public function store(): void
    {
        $this->save(null);
    }

    public function edit(string $id): void
    {
        $method = DonationMethod::find((int) $id);
        if (!$method) {
            $this->flash('error', 'Donation method not found.');
            $this->redirect('admin/donation-methods');
        }

        $this->view('admin.donation_methods.form', [
            'title' => 'Edit Donation Method',
            'activeNav' => 'donation_methods',
            'method' => $method,
        ]);
    }

    public function update(string $id): void
    {
        $this->save((int) $id);
    }

    public function destroy(string $id): void
    {
        $this->requireCsrf();
        DonationMethod::delete((int) $id);
        $this->flash('success', 'Donation method deleted.');
        $this->redirect('admin/donation-methods');
    }

    private function save(?int $id): void
    {
        $this->requireCsrf();

        $label = trim((string) $this->input('label', ''));
        $bankName = trim((string) $this->input('bank_name', ''));
        $accountName = trim((string) $this->input('account_name', ''));
        $accountNumber = trim((string) $this->input('account_number', ''));
        $sortCode = trim((string) $this->input('sort_code', ''));
        $sortOrder = (int) $this->input('sort_order', 0);
        $isActive = $this->input('is_active') ? 1 : 0;

        $errors = [];
        if ($label === '') {
            $errors[] = 'A label is required (e.g. "Naira Account").';
        }
        if ($accountNumber === '') {
            $errors[] = 'An account number is required.';
        }

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->redirect($id ? "admin/donation-methods/{$id}/edit" : 'admin/donation-methods/create');
        }

        $data = [
            'label' => $label,
            'bank_name' => $bankName !== '' ? $bankName : null,
            'account_name' => $accountName !== '' ? $accountName : null,
            'account_number' => $accountNumber,
            'sort_code' => $sortCode !== '' ? $sortCode : null,
            'sort_order' => $sortOrder,
            'is_active' => $isActive,
        ];

        if ($id === null) {
            DonationMethod::create($data);
            $this->flash('success', 'Donation method created.');
        } else {
            DonationMethod::update($id, $data);
            $this->flash('success', 'Donation method updated.');
        }

        $this->redirect('admin/donation-methods');
    }
}
