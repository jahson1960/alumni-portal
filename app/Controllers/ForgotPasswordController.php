<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\EmailQueue;
use App\Models\PasswordReset;
use App\Models\User;

class ForgotPasswordController extends Controller
{
    public function show(): void
    {
        $this->view('auth.forgot_password', ['title' => 'Forgot Password'], 'auth');
    }

    public function send(): void
    {
        $this->requireCsrf();

        $email = trim((string) $this->input('email', ''));
        $user = $email !== '' ? User::findByEmail($email) : false;

        if ($user && $user['status'] === 'active') {
            $token = PasswordReset::create((int) $user['id']);
            [$firstName] = split_full_name((string) $user['name']);
            EmailQueue::enqueue($user['email'], $user['name'], 'Reset Your ' . portal_name() . ' Password', render_email(
                'Reset Your Password',
                password_reset_email_body($firstName),
                'Reset Password',
                'reset-password/' . $token
            ));
        }

        // Always the same message whether or not the email matched an account, so this page can
        // never be used to check which addresses are registered.
        $this->flash('success', "If an account exists for that email, we've sent a link to reset the password.");
        $this->redirect('forgot-password');
    }

    public function showReset(string $token): void
    {
        if (!PasswordReset::findValidByToken($token)) {
            $this->flash('error', 'That password reset link is invalid or has expired. Please request a new one.');
            $this->redirect('forgot-password');
        }

        $this->view('auth.reset_password', ['title' => 'Reset Password', 'token' => $token], 'auth');
    }

    public function update(string $token): void
    {
        $this->requireCsrf();

        $reset = PasswordReset::findValidByToken($token);
        if (!$reset) {
            $this->flash('error', 'That password reset link is invalid or has expired. Please request a new one.');
            $this->redirect('forgot-password');
        }

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
            $this->redirect('reset-password/' . $token);
        }

        $user = User::find((int) $reset['user_id']);
        if (!$user) {
            $this->flash('error', 'That account could not be found.');
            $this->redirect('forgot-password');
        }

        User::update((int) $user['id'], [
            'password_hash' => password_hash($password, PASSWORD_DEFAULT),
            'must_change_password' => 0,
        ]);
        PasswordReset::markUsed((int) $reset['id']);

        Auth::login(User::find((int) $user['id']));

        $this->flash('success', 'Your password has been reset. You are now logged in.');
        $this->redirect(Auth::isAdmin() ? 'admin/dashboard' : '/');
    }
}
