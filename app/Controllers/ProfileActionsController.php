<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Event;
use App\Models\Notification;
use App\Models\ProfileReport;
use App\Models\User;

/** Secondary actions from the alumni profile page's "Quick Actions" sidebar. */
class ProfileActionsController extends Controller
{
    private const REPORT_REASONS = [
        'Impersonation or fake profile',
        'Inappropriate content',
        'Spam or scam',
        'Harassment',
        'Other',
    ];

    public function __construct()
    {
        if (!Auth::check()) {
            $this->redirect('login');
        }
    }

    public function inviteToEvent(string $id): void
    {
        $this->requireCsrf();
        $target = User::find((int) $id);
        $eventId = (int) $this->input('event_id', 0);
        $event = $eventId ? Event::find($eventId) : null;

        if (!$target || !$event) {
            $this->flash('error', 'Could not send that invitation.');
            $this->redirect("alumni/{$id}");
        }

        Notification::notify(
            (int) $id,
            'event_invite',
            Auth::user()['name'] . ' invited you to "' . $event['title'] . '".',
            'events/' . $event['id']
        );

        $this->flash('success', 'Invitation sent to ' . $target['name'] . '.');
        $this->redirect("alumni/{$id}");
    }

    public function report(string $id): void
    {
        $this->requireCsrf();
        $reporterId = (int) Auth::id();
        $target = User::find((int) $id);
        $reason = trim((string) $this->input('reason', ''));
        $notes = trim((string) $this->input('notes', ''));

        if (!$target || (int) $id === $reporterId || !in_array($reason, self::REPORT_REASONS, true)) {
            $this->flash('error', 'Could not submit that report.');
            $this->redirect("alumni/{$id}");
        }

        ProfileReport::create([
            'reporter_id' => $reporterId,
            'reported_user_id' => (int) $id,
            'reason' => $reason,
            'notes' => $notes !== '' ? $notes : null,
        ]);

        foreach (User::adminIds() as $adminId) {
            Notification::notify(
                (int) $adminId,
                'profile_reported',
                Auth::user()['name'] . ' reported ' . $target['name'] . "'s profile ({$reason}).",
                'admin/profile-reports'
            );
        }

        $this->flash('success', 'Thanks — our team will review this profile.');
        $this->redirect("alumni/{$id}");
    }
}
