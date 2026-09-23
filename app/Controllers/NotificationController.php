<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Notification;

class NotificationController extends Controller
{
    public function __construct()
    {
        if (!Auth::check() || !Auth::user()) {
            Auth::logout();
            header('Location: ' . url('login'));
            exit;
        }
    }

    private const CATEGORIES = [
        '' => ['label' => 'All Notifications', 'icon' => 'bi-bell'],
        'messages' => ['label' => 'Messages', 'icon' => 'bi-envelope'],
        'connections' => ['label' => 'Connection Requests', 'icon' => 'bi-person-plus'],
        'mentorship' => ['label' => 'Mentorship', 'icon' => 'bi-mortarboard'],
        'system' => ['label' => 'System Updates', 'icon' => 'bi-gear'],
    ];

    public function index(): void
    {
        // Deliberately not auto-marking everything read here — the unread state (and the gold
        // highlight it drives) should persist until the visitor explicitly clears it below,
        // not vanish the instant this page loads.
        $notifications = Notification::forUser((int) Auth::id());

        $counts = array_fill_keys(array_keys(self::CATEGORIES), 0);
        foreach ($notifications as $n) {
            if (!$n['is_read']) {
                $counts['']++;
                $counts[notification_category($n['type'])]++;
            }
        }

        $this->view('notifications.index', [
            'title' => 'Notifications',
            'activeNav' => 'notifications',
            'notifications' => $notifications,
            'categories' => self::CATEGORIES,
            'unreadCounts' => $counts,
        ]);
    }

    public function markAllRead(): void
    {
        $this->requireCsrf();
        Notification::markAllRead((int) Auth::id());
        $this->redirectBack('notifications');
    }

    /** JSON polling endpoint for the header bell badge. */
    public function poll(): void
    {
        header('Content-Type: application/json');
        echo json_encode(['unread' => Notification::unreadCount((int) Auth::id())]);
    }
}
