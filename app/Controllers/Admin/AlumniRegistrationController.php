<?php

namespace App\Controllers\Admin;

use App\Controllers\ErrorController;
use App\Core\Auth;
use App\Core\Controller;
use App\Models\AlumniRoster;
use App\Models\EmailQueue;
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
            $this->redirect('login');
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
            'title' => 'Register Alumni',
            'activeNav' => 'register_alumni',
        ], 'admin');
    }

    public function store(): void
    {
        $this->requireCsrf();

        $email = trim((string) $this->input('email', ''));
        $matricNumber = trim((string) $this->input('matric_number', ''));

        $errors = [];
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email address is required.';
        } elseif (User::emailExists($email)) {
            $errors[] = 'An account with that email already exists.';
        }

        $rosterEntry = null;
        if ($matricNumber === '') {
            $errors[] = 'Matric number is required.';
        } else {
            $rosterEntry = AlumniRoster::findByMatric($matricNumber);
            if (!$rosterEntry) {
                $errors[] = 'No alumni roster record was found for that matric number.';
            } elseif ($rosterEntry['claimed_by_user_id']) {
                $errors[] = 'An account has already been created for this matric number.';
            }
        }

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->old(['email' => $email, 'matric_number' => $matricNumber]);
            $this->redirect('admin/register-alumni');
        }

        $name = trim((string) $rosterEntry['full_name']);
        [$firstName, $otherNames] = split_full_name($name);
        $tempPassword = generate_temp_password();

        $id = User::create([
            'role' => 'alumni',
            'name' => $name,
            'first_name' => $firstName,
            'other_names' => $otherNames !== '' ? $otherNames : null,
            'email' => $email,
            'password_hash' => password_hash($tempPassword, PASSWORD_DEFAULT),
            'must_change_password' => 1,
            'graduation_year' => $rosterEntry['graduation_year'],
            'program' => $rosterEntry['program'],
            'matric_number' => $matricNumber,
            'cohort' => $rosterEntry['cohort'],
            'status' => 'active',
        ]);

        AlumniRoster::markClaimed((int) $rosterEntry['id'], $id);

        EmailQueue::enqueue($email, $name, 'Your Rome Business School Nigeria Alumni Portal Account', render_email(
            'Your Alumni Portal Account',
            credential_email_body($firstName, $email, $tempPassword, 'An account has been created for you on the Rome Business School Nigeria Alumni Portal.'),
            'Log In Now',
            'login'
        ));

        $_SESSION['_registered_alumni'] = ['name' => $name, 'email' => $email, 'password' => $tempPassword];
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
            'title' => 'Alumni Registered',
            'activeNav' => 'register_alumni',
            'result' => $result,
        ], 'admin');
    }
}
