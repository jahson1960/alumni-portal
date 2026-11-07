<?php

namespace App\Controllers\Admin;

use App\Controllers\ErrorController;
use App\Core\Auth;
use App\Core\Controller;
use App\Models\AlumniRoster;
use App\Models\Category;
use App\Models\User;

/**
 * Lets admins AND editors register an alumnus on their behalf (e.g. at an in-person event, or
 * when helping someone who can't self-register). Deliberately NOT an AdminController subclass —
 * that base is admin-only, and this is the one admin-area capability editors are granted.
 */
class AlumniRegistrationController extends Controller
{
    public function __construct()
    {
        if (!Auth::check()) {
            $this->redirectToLogin();
        }
        if (!Auth::isAdmin() && !Auth::isEditor()) {
            http_response_code(404);
            (new ErrorController())->notFound();
            exit;
        }
    }

    public function create(): void
    {
        $this->view('admin.alumni_registration.create', [
            'title' => 'Register ' . term_plural(),
            'activeNav' => 'register_alumni',
            'allCategories' => Category::forType('job'),
        ], 'admin');
    }

    public function store(): void
    {
        $this->requireCsrf();

        $email = trim((string) $this->input('email', ''));
        $matricNumber = trim((string) $this->input('matric_number', ''));
        $jobCategoryIds = array_map('intval', (array) $this->input('job_categories', []));

        $errors = [];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email address is required.';
        } elseif (!email_domain_has_mail_server($email)) {
            $errors[] = "We couldn't find a mail server for that email's domain.";
        } elseif (User::emailExists($email)) {
            $errors[] = 'An account with that email already exists.';
        }

        $rosterEntry = null;
        if ($matricNumber === '') {
            $errors[] = 'Matric number is required.';
        } else {
            $rosterEntry = AlumniRoster::findByMatric($matricNumber);
            if (!$rosterEntry) {
                $errors[] = 'No ' . term_plural_lc() . ' roster record was found for that matric number.';
            } elseif ($rosterEntry['claimed_by_user_id']) {
                $errors[] = 'An account has already been created for this matric number.';
            }
        }
        if (!$jobCategoryIds) {
            $errors[] = 'Please select at least one job category.';
        }

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->old(['email' => $email, 'matric_number' => $matricNumber]);
            $this->redirect('admin/register-alumni');
        }

        $rosterEntry['matric_number'] = $matricNumber;
        register_from_roster($rosterEntry, $email, $jobCategoryIds);

        $_SESSION['_registered_alumni'] = ['name' => trim((string) $rosterEntry['full_name']), 'email' => $email];
        $this->redirect('admin/register-alumni/success');
    }

    public function success(): void
    {
        $result = $_SESSION['_registered_alumni'] ?? null;
        unset($_SESSION['_registered_alumni']);
        if (!$result) {
            $this->redirect('admin/register-alumni');
        }

        $this->view('admin.alumni_registration.success', [
            'title' => term_plural() . ' Registered',
            'activeNav' => 'register_alumni',
            'result' => $result,
        ], 'admin');
    }
}
