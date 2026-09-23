<?php

namespace App\Core;

class Upload
{
    private const ALLOWED = ['jpg' => 'image/jpeg', 'jpeg' => 'image/jpeg', 'png' => 'image/png', 'webp' => 'image/webp'];
    private const MAX_SIZE = 3 * 1024 * 1024; // 3MB

    private const ALLOWED_DOCS = [
        'pdf' => 'application/pdf',
        'doc' => 'application/msword',
        'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    ];
    private const MAX_DOC_SIZE = 5 * 1024 * 1024; // 5MB

    /**
     * Validate and move an uploaded image. Returns the stored relative path (e.g. "news/xyz.jpg") or null if no file was submitted.
     * Throws RuntimeException with a user-facing message on validation failure.
     */
    public static function image(?array $file, string $subdir): ?string
    {
        if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Image upload failed. Please try again.');
        }
        if ($file['size'] > self::MAX_SIZE) {
            throw new \RuntimeException('Image must be smaller than 3MB.');
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!isset(self::ALLOWED[$ext])) {
            throw new \RuntimeException('Only JPG, PNG, or WEBP images are allowed.');
        }

        $mime = mime_content_type($file['tmp_name']);
        if ($mime !== self::ALLOWED[$ext]) {
            throw new \RuntimeException('The uploaded file does not look like a valid image.');
        }

        $config = require __DIR__ . '/../../config/config.php';
        $dir = rtrim($config['upload_dir'], '/') . '/' . $subdir;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $filename)) {
            throw new \RuntimeException('Could not save the uploaded image.');
        }

        return $subdir . '/' . $filename;
    }

    /**
     * Validate and move an uploaded resume/CV document. Returns the stored relative path or null if no file was submitted.
     * Throws RuntimeException with a user-facing message on validation failure.
     */
    public static function document(?array $file, string $subdir): ?string
    {
        if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new \RuntimeException('Resume upload failed. Please try again.');
        }
        if ($file['size'] > self::MAX_DOC_SIZE) {
            throw new \RuntimeException('Resume file must be smaller than 5MB.');
        }

        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!isset(self::ALLOWED_DOCS[$ext])) {
            throw new \RuntimeException('Only PDF, DOC, or DOCX files are allowed.');
        }

        // .doc/.docx magic-byte detection is unreliable across systems, so only PDF gets a strict mime check.
        if ($ext === 'pdf' && mime_content_type($file['tmp_name']) !== 'application/pdf') {
            throw new \RuntimeException('The uploaded file does not look like a valid PDF.');
        }

        $config = require __DIR__ . '/../../config/config.php';
        $dir = rtrim($config['upload_dir'], '/') . '/' . $subdir;
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        $filename = bin2hex(random_bytes(16)) . '.' . $ext;
        if (!move_uploaded_file($file['tmp_name'], $dir . '/' . $filename)) {
            throw new \RuntimeException('Could not save the uploaded resume.');
        }

        return $subdir . '/' . $filename;
    }
}
