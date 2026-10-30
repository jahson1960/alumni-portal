<?php

namespace App\Controllers\Admin;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\User;

/**
 * Backs the live email format + availability check on Register Alumni and Create Staff.
 * Deliberately not an AdminController subclass - Editors can use Register Alumni too, and
 * AdminController's non-admin path hard-redirects and logs out, which would break silently on
 * a background fetch instead of just answering it with "not authorized".
 */
class EmailCheckController extends Controller
{
    public function check(): void
    {
        header('Content-Type: application/json');

        if (!Auth::check() || !(Auth::isAdmin() || Auth::isEditor())) {
            http_response_code(403);
            echo json_encode(['error' => 'Not authorized.']);
            return;
        }

        $email = trim((string) $this->input('email', ''));

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            echo json_encode(['status' => 'invalid_format']);
            return;
        }
        if (!email_domain_has_mail_server($email)) {
            echo json_encode(['status' => 'unreachable_domain']);
            return;
        }
        if (User::emailExists($email)) {
            echo json_encode(['status' => 'taken']);
            return;
        }
        echo json_encode(['status' => 'ok']);
    }
}
