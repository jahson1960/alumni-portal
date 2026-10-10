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
        $this->view('auth.register', [
            'title' => 'Create Your Alumni Account',
        ], 'auth');
    }

    public function register(): void
    {
        $this->requireCsrf();

        $email = trim((string) $this->input('email', ''));
        $password = (string) $this->input('password', '');
        $passwordConfirm = (string) $this->input('password_confirmation', '');
        $matricNumber = trim((string) $this->input('matric_number', ''));

        $errors = [];
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
        } else {
            $rosterEntry = AlumniRoster::findByMatric($matricNumber);
            if (!$rosterEntry) {
                $errors[] = 'We could not find that matric number in our alumni records. Please contact support if you believe this is an error.';
            } elseif ($rosterEntry['claimed_by_user_id']) {
                $errors[] = 'An account has already been created for this matric number.';
            }
        }

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->old(['email' => $email, 'matric_number' => $matricNumber]);
            $this->redirect('register');
        }

        $name = trim((string) $rosterEntry['full_name']);
        [$firstName, $otherNames] = split_full_name($name);

        $id = User::create([
            'role' => 'alumni',
            'name' => $name,
            'first_name' => $firstName,
            'other_names' => $otherNames !== '' ? $otherNames : null,
            'email' => $email,
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'graduation_year' => $rosterEntry['graduation_year'],
            'program' => $rosterEntry['program'],
            'matric_number' => $matricNumber,
            'cohort' => $rosterEntry['cohort'],
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
        $redirect = sanitize_redirect_target($this->input('redirect'));
        if ($redirect !== null) {
            $_SESSION['_intended_url'] = $redirect;
        } else {
            unset($_SESSION['_intended_url']);
        }
        $this->view('auth.login', ['title' => 'Login'], 'auth');
    }

    public function login(): void
    {
        $this->requireCsrf();

        $email = trim((string) $this->input('email', ''));
        $password = (string) $this->input('password', '');

        if (Auth::attempt($email, $password)) {
            $intended = sanitize_redirect_target($_SESSION['_intended_url'] ?? null);
            unset($_SESSION['_intended_url']);
            $destination = $intended ?? url(Auth::isAdmin() ? 'admin/dashboard' : '/');

            if (!empty(Auth::user()['must_change_password'])) {
                $_SESSION['_post_password_change_redirect'] = $destination;
                $this->redirect('change-password');
            }

            header('Location: ' . $destination);
            exit;
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
