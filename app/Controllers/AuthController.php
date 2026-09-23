<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\AlumniRoster;
use App\Models\User;

class AuthController extends Controller
{
    public function showRegister(): void
    {
        if (Auth::check()) {
            $this->redirect('/');
        }
        $this->view('auth.register', ['title' => 'Create Your Alumni Account'], 'auth');
    }

    public function register(): void
    {
        $this->requireCsrf();

        $name = trim((string) $this->input('name', ''));
        $email = trim((string) $this->input('email', ''));
        $password = (string) $this->input('password', '');
        $passwordConfirm = (string) $this->input('password_confirmation', '');
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
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        } elseif ($password !== $passwordConfirm) {
            $errors[] = 'Password confirmation does not match.';
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
                $errors[] = 'We could not find that matric number in our alumni records. Please contact support if you believe this is an error.';
            } elseif ($rosterEntry['claimed_by_user_id']) {
                $errors[] = 'An account has already been created for this matric number.';
            } elseif ((int) $rosterEntry['graduation_year'] !== (int) $graduationYear) {
                $errors[] = 'The graduation year you entered does not match our records for this matric number.';
            } elseif (mb_strtolower($rosterEntry['cohort']) !== mb_strtolower($cohort)) {
                $errors[] = 'The cohort you entered does not match our records for this matric number.';
            }
        }

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->old(['name' => $name, 'email' => $email, 'graduation_year' => $graduationYear, 'program' => $program, 'matric_number' => $matricNumber, 'cohort' => $cohort]);
            $this->redirect('register');
        }

        $id = User::create([
            'role' => 'alumni',
            'name' => $name,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'graduation_year' => $graduationYear !== '' ? (int) $graduationYear : null,
            'program' => $program !== '' ? $program : null,
            'matric_number' => $matricNumber,
            'cohort' => $cohort,
            'status' => 'active',
        ]);

        AlumniRoster::markClaimed((int) $rosterEntry['id'], $id);

        $user = User::find($id);
        Auth::login($user);
        $this->flash('success', 'Welcome to the RBSN Alumni Network! Complete your profile to get the most out of it.');
        $this->redirect('profile/edit');
    }

    public function showLogin(): void
    {
        if (Auth::check()) {
            $this->redirect(Auth::isAdmin() ? 'admin/dashboard' : '/');
        }
        $this->view('auth.login', ['title' => 'Login'], 'auth');
    }

    public function login(): void
    {
        $this->requireCsrf();

        $email = trim((string) $this->input('email', ''));
        $password = (string) $this->input('password', '');

        if (Auth::attempt($email, $password)) {
            $this->flash('success', 'Welcome back!');
            $this->redirect(Auth::isAdmin() ? 'admin/dashboard' : '/');
        }

        $_SESSION['_errors'] = ['Incorrect email or password, or the account has been suspended.'];
        $this->old(['email' => $email]);
        $this->redirect('login');
    }

    public function logout(): void
    {
        $this->requireCsrf();
        Auth::logout();
        $this->flash('success', 'You have been logged out.');
        $this->redirect('/');
    }
}
