<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\User;

/**
 * Self-service password change — reached either voluntarily, or via a forced redirect right
 * after login when the account has must_change_password set (new admin-registered accounts,
 * or after an admin resets someone's password).
 */
class ChangePasswordController extends Controller
{
    public function __construct()
    {
        if (!Auth::check()) {
            $this->redirect('login');
        }
    }

    public function show(): void
    {
        $this->view('auth.change_password', [
            'title' => 'Change Your Password',
            'forced' => !empty(Auth::user()['must_change_password']),
        ], 'auth');
    }

    public function update(): void
    {
        $this->requireCsrf();

        $password = (string) $this->input('password', '');
        $confirm = (string) $this->input('password_confirmation', '');

        $errors = [];
        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters.';
        } elseif ($password !== $confirm) {
            $errors[] = 'Password confirmation does not match.';
        }

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->redirect('change-password');
        }

        User::update((int) Auth::id(), [
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'must_change_password' => 0,
        ]);

        $redirect = $_SESSION['_post_password_change_redirect'] ?? null;
        unset($_SESSION['_post_password_change_redirect']);

        $this->flash('success', 'Your password has been updated.');

        if ($redirect) {
            header('Location: ' . $redirect);
            exit;
        }
        $this->redirect(Auth::isAdmin() ? 'admin/dashboard' : '/');
    }
}
