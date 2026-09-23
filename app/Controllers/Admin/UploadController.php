<?php

namespace App\Controllers\Admin;

use App\Core\Upload;

class UploadController extends AdminController
{
    /**
     * AJAX endpoint used by the rich text editor's image button. Returns JSON.
     */
    public function editorImage(): void
    {
        header('Content-Type: application/json');

        if (!\App\Core\Csrf::verify($_POST['_csrf'] ?? null)) {
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
