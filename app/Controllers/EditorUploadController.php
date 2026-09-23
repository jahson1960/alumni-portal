<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Csrf;
use App\Core\Upload;

class EditorUploadController extends Controller
{
    /** AJAX endpoint for the alumni-facing rich text editor's image button (any logged-in user). */
    public function image(): void
    {
        header('Content-Type: application/json');

        if (!Auth::check()) {
            http_response_code(401);
            echo json_encode(['error' => 'You must be logged in.']);
            return;
        }

        if (!Csrf::verify($_POST['_csrf'] ?? null)) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid form submission.']);
            return;
        }

        try {
            $path = Upload::image($_FILES['file'] ?? null, 'editor');
        } catch (\RuntimeException $e) {
            http_response_code(422);
            echo json_encode(['error' => $e->getMessage()]);
            return;
        }

        if (!$path) {
            http_response_code(422);
            echo json_encode(['error' => 'No image was uploaded.']);
            return;
        }

        echo json_encode(['url' => upload_url($path)]);
    }
}
