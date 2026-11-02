<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\PageView;

/**
 * Backs the client-side tracker (app/Views/partials/analytics_tracker.php) - every visitor,
 * logged in or not, hits this, so it's a plain Controller, not an AdminController subclass.
 */
class AnalyticsController extends Controller
{
    public function track(): void
    {
        header('Content-Type: application/json');

        $visitorId = (string) ($_COOKIE['av_vid'] ?? '');
        if ($visitorId === '') {
            echo json_encode(['id' => null]);
            return;
        }

        $path = trim((string) $this->input('path', ''));
        if ($path === '' || mb_strlen($path) > 255) {
            echo json_encode(['id' => null]);
            return;
        }

        $referrer = trim((string) $this->input('referrer', ''));
        if (mb_strlen($referrer) > 255) {
            $referrer = mb_substr($referrer, 0, 255);
        }

        $id = PageView::record($visitorId, Auth::check() ? (int) Auth::id() : null, $path, $referrer);
        echo json_encode(['id' => $id]);
    }

    public function duration(): void
    {
        header('Content-Type: application/json');

        $visitorId = (string) ($_COOKIE['av_vid'] ?? '');
        $id = (int) $this->input('id', 0);
        $seconds = (int) $this->input('seconds', 0);

        if ($visitorId !== '' && $id > 0) {
            PageView::updateDuration($id, $visitorId, $seconds);
        }

        echo json_encode(['ok' => true]);
    }
}
