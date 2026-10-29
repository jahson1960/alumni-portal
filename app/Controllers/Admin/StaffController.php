<?php

namespace App\Controllers\Admin;

use App\Models\EmailQueue;
use App\Models\User;

/**
 * Lets an admin create an Admin, Editor, Recruiter, or Advisor account directly - no alumni
 * roster involved, unlike AlumniRegistrationController. Admin-only (granting roles is
 * higher-trust than registering an alumnus, which editors are also allowed to do).
 */
class StaffController extends AdminController
{
    private const ROLES = ['admin', 'editor', 'recruiter', 'advisor'];
    private const ROLE_LABELS = ['admin' => 'Admin', 'editor' => 'Editor', 'recruiter' => 'Recruiter', 'advisor' => 'Advisor'];

    public function create(): void
    {
        $this->view('admin.staff.create', [
            'title' => 'Create Staff Account',
            'activeNav' => 'create_staff',
            'roleLabels' => self::ROLE_LABELS,
        ]);
    }

    public function store(): void
    {
        $this->requireCsrf();

        $name = trim((string) $this->input('name', ''));
        $email = trim((string) $this->input('email', ''));
        $role = $this->input('role', '');

        $errors = [];
        if ($name === '') {
            $errors[] = 'Full name is required.';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'A valid email address is required.';
        } elseif (User::emailExists($email)) {
            $errors[] = 'An account with that email already exists.';
        }
        if (!in_array($role, self::ROLES, true)) {
            $errors[] = 'Please choose a valid role.';
        }

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->old(['name' => $name, 'email' => $email]);
            $this->redirect('admin/create-staff');
        }

        [$firstName, $otherNames] = split_full_name($name);
        $tempPassword = generate_temp_password();

        $id = User::create([
            'role' => $role,
            'name' => $name,
            'first_name' => $firstName,
            'other_names' => $otherNames !== '' ? $otherNames : null,
            'email' => $email,
            'password_hash' => password_hash($tempPassword, PASSWORD_DEFAULT),
            'must_change_password' => 1,
            'status' => 'active',
        ]);

        EmailQueue::enqueue($email, $name, 'Your Rome Business School Nigeria Alumni Portal Account', render_email(
            'Your Alumni Portal Account',
            credential_email_body($firstName, $email, $tempPassword, 'An account has been created for you on the Rome Business School Nigeria Alumni Portal as a ' . self::ROLE_LABELS[$role] . '.'),
            'Log In Now',
            'login'
        ));

        $_SESSION['_created_staff'] = ['name' => $name, 'email' => $email, 'role' => self::ROLE_LABELS[$role]];
        $this->redirect('admin/create-staff/success');
    }

    public function success(): void
    {
        $result = $_SESSION['_created_staff'] ?? null;
        unset($_SESSION['_created_staff']);
        if (!$result) {
            $this->redirect('admin/create-staff');
        }

        $this->view('admin.staff.success', [
            'title' => 'Staff Account Created',
            'activeNav' => 'create_staff',
            'result' => $result,
        ]);
    }
}
