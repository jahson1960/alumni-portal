<?php

namespace App\Controllers\Admin;

use App\Controllers\ErrorController;
use App\Core\Auth;
use App\Core\Controller;
use App\Models\AlumniRoster;
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

        $name = trim((string) $this->input('name', ''));
        $email = trim((string) $this->input('email', ''));
        $graduationYear = trim((string) $this->input('graduation_year', ''));
        $program = trim((string) $this->input('program', ''));
        $matricNumber = trim((string) $this->input('matric_number', ''));
        $cohort = trim((string) $this->input('cohort', ''));

        $errors = [];
        if ($name === '') {
            $errors[] = 'Full name is required.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email address is required.';
        } elseif (User::emailExists($email)) {
            $errors[] = 'An account with that email already exists.';
        }

        $rosterEntry = null;
        if ($matricNumber === '') {
            $errors[] = 'Matric number is required.';
        } elseif ($graduationYear === '') {
            $errors[] = 'Graduation year is required.';
        } elseif ($cohort === '') {
            $errors[] = 'Cohort is required.';
        } else {
            $rosterEntry = AlumniRoster::findByMatric($matricNumber);
            if (!$rosterEntry) {
                $errors[] = 'No alumni roster record was found for that matric number.';
            } elseif ($rosterEntry['claimed_by_user_id']) {
                $errors[] = 'An account has already been created for this matric number.';
            } elseif ((int) $rosterEntry['graduation_year'] !== (int) $graduationYear) {
                $errors[] = 'The graduation year does not match our roster records for this matric number.';
            } elseif (mb_strtolower($rosterEntry['cohort']) !== mb_strtolower($cohort)) {
                $errors[] = 'The cohort does not match our roster records for this matric number.';
            }
        }

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->old(['name' => $name, 'email' => $email, 'graduation_year' => $graduationYear, 'program' => $program, 'matric_number' => $matricNumber, 'cohort' => $cohort]);
            $this->redirect('admin/register-alumni');
        }

        $tempPassword = self::generatePassword();

        $id = User::create([
            'role' => 'alumni',
            'name' => $name,
            'email' => $email,
            'password_hash' => password_hash($tempPassword, PASSWORD_DEFAULT),
            'graduation_year' => $graduationYear !== '' ? (int) $graduationYear : null,
            'program' => $program !== '' ? $program : null,
            'matric_number' => $matricNumber,
            'cohort' => $cohort,
            'status' => 'active',
        ]);

        AlumniRoster::markClaimed((int) $rosterEntry['id'], $id);

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

    /** Random temporary password shown once to the staff member registering the account, to relay to the alum. */
    private static function generatePassword(): string
    {
        return substr(str_shuffle('ABCDEFGHJKLMNPQRSTUVWXYZabcdefghijkmnpqrstuvwxyz23456789'), 0, 10);
    }
}
