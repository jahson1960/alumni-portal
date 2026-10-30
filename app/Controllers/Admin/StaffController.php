<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Models\EmailQueue;
use App\Models\User;

/**
 * Lets an admin create an Admin, Editor, Recruiter, or Advisor account directly - no alumni
 * roster involved, unlike AlumniRegistrationController. Admin-only (granting roles is
 * higher-trust than registering an alumnus, which editors are also allowed to do). Also the
 * list/manage screen for those same four roles - deliberately separate from Admin > Alumni,
 * which is alumni-only.
 */
class StaffController extends AdminController
{
    private const ROLES = ['admin', 'editor', 'recruiter', 'advisor'];
    private const ROLE_LABELS = ['admin' => 'Admin', 'editor' => 'Editor', 'recruiter' => 'Recruiter', 'advisor' => 'Advisor'];

    public function index(): void
    {
        $p = $this->paginate(User::countStaff(), 30);
        $this->view('admin.staff.index', [
            'title' => 'Staff',
            'activeNav' => 'staff_list',
            'staff' => User::paginateStaff($p['perPage'], $p['offset']),
            ...$p,
        ]);
    }

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
        } elseif (!email_domain_has_mail_server($email)) {
            $errors[] = "We couldn't find a mail server for that email's domain.";
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

    public function suspend(string $id): void
    {
        $this->requireCsrf();
        $this->guardAgainstSelfOrLastAdmin((int) $id, 'suspend');
        User::update((int) $id, ['status' => 'suspended']);
        $this->flash('success', 'Staff account suspended.');
        $this->redirect('admin/staff');
    }

    public function activate(string $id): void
    {
        $this->requireCsrf();
        User::update((int) $id, ['status' => 'active']);
        $this->flash('success', 'Staff account activated.');
        $this->redirect('admin/staff');
    }

    public function destroy(string $id): void
    {
        $this->requireCsrf();
        $this->guardAgainstSelfOrLastAdmin((int) $id, 'delete');
        User::delete((int) $id);
        $this->flash('success', 'Staff account deleted.');
        $this->redirect('admin/staff');
    }

    /** Generates a new temporary password, forces a change on next login, and emails it to the staff member. */
    public function resetPassword(string $id): void
    {
        $this->requireCsrf();
        $staff = User::find((int) $id);
        if (!$staff || !in_array($staff['role'], self::ROLES, true)) {
            $this->flash('error', 'Staff account not found.');
            $this->redirect('admin/staff');
        }

        $tempPassword = generate_temp_password();
        User::update((int) $id, [
            'password_hash' => password_hash($tempPassword, PASSWORD_DEFAULT),
            'must_change_password' => 1,
        ]);

        EmailQueue::enqueue($staff['email'], $staff['name'], 'Your Alumni Portal Password Has Been Reset', render_email(
            'Password Reset',
            credential_email_body(
                $staff['first_name'] ?: $staff['name'],
                $staff['email'],
                $tempPassword,
                'An administrator has reset your password on the Rome Business School Nigeria Alumni Portal.'
            ),
            'Log In Now',
            'login'
        ));

        $this->flash('success', 'Password reset. A temporary password has been emailed.');
        $this->redirect('admin/staff');
    }

    /**
     * Blocks suspending/deleting your own account (so you can't lock yourself out mid-session)
     * and blocks suspending/deleting the last remaining active admin (so the team can't end up
     * with zero admins able to fix it).
     */
    private function guardAgainstSelfOrLastAdmin(int $id, string $action): void
    {
        if ($id === (int) Auth::id()) {
            $this->flash('error', "You can't {$action} your own account.");
            $this->redirect('admin/staff');
        }

        $target = User::find($id);
        if ($target && $target['role'] === 'admin' && $target['status'] === 'active' && count(User::adminIds()) <= 1) {
            $this->flash('error', 'Cannot ' . $action . ' the last remaining admin account.');
            $this->redirect('admin/staff');
        }
    }
}
