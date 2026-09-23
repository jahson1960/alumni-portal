<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\BlockedUser;
use App\Models\Connection;
use App\Models\Follow;
use App\Models\Notification;

class ConnectionController extends Controller
{
    public function __construct()
    {
        if (!Auth::check() || !Auth::user()) {
            Auth::logout();
            header('Location: ' . url('login'));
            exit;
        }
    }

    private const SORTS = ['recent', 'name_asc', 'name_desc'];

    /** True when the request came from the Connections page's own JS (fetch), which wants a JSON reply instead of a redirect. */
    private function wantsJson(): bool
    {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
    }

    private function json(array $data): void
    {
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    public function index(): void
    {
        $this->requireVisibility('connections');

        $sort = in_array($this->input('sort'), self::SORTS, true) ? $this->input('sort') : 'recent';
        $userId = (int) Auth::id();

        $this->view('connections.index', [
            'title' => 'My Connections',
            'activeNav' => 'connections',
            'connections' => Connection::myConnections($userId, $sort),
            'sort' => $sort,
            'incoming' => Connection::incomingRequests($userId),
            'outgoing' => Connection::outgoingRequests($userId),
            'suggestions' => Connection::suggestions($userId, 9),
            'following' => Follow::following($userId),
        ]);
    }

    /** JSON polling endpoint the Connections page uses to notice requests/accepts that happened on the other end. */
    public function poll(): void
    {
        $userId = (int) Auth::id();
        $this->json([
            'incoming' => Connection::countIncoming($userId),
            'outgoing' => Connection::countOutgoing($userId),
            'connections' => Connection::countAccepted($userId),
        ]);
    }

    public function sendRequest(string $userId): void
    {
        $this->requireCsrf();
        if (Connection::request((int) Auth::id(), (int) $userId)) {
            Notification::notify(
                (int) $userId,
                'connection_request',
                Auth::user()['name'] . ' sent you a connection request.',
                'connections#requests'
            );
        }
        if ($this->wantsJson()) {
            $this->json(['ok' => true]);
            return;
        }
        $this->flash('success', 'Connection request sent.');
        $this->redirectBack("alumni/{$userId}");
    }

    public function cancelRequest(string $userId): void
    {
        $this->requireCsrf();
        Connection::deleteBetweenAsParty((int) Auth::id(), (int) $userId);
        if ($this->wantsJson()) {
            $this->json(['ok' => true]);
            return;
        }
        $this->flash('success', 'Connection request cancelled.');
        $this->redirectBack("alumni/{$userId}");
    }

    public function accept(string $connectionId): void
    {
        $this->requireCsrf();
        $row = Connection::find((int) $connectionId);
        if ($row && Connection::accept((int) $connectionId, (int) Auth::id())) {
            Notification::notify(
                (int) $row['requester_id'],
                'connection_accepted',
                Auth::user()['name'] . ' accepted your connection request.',
                'alumni/' . Auth::id()
            );
        }
        if ($this->wantsJson()) {
            $this->json(['ok' => true]);
            return;
        }
        $this->flash('success', 'Connection accepted.');
        $this->redirect('connections#requests');
    }

    public function decline(string $connectionId): void
    {
        $this->requireCsrf();
        Connection::deleteAsParty((int) $connectionId, (int) Auth::id());
        if ($this->wantsJson()) {
            $this->json(['ok' => true]);
            return;
        }
        $this->flash('success', 'Connection request declined.');
        $this->redirect('connections#requests');
    }

    public function remove(string $userId): void
    {
        $this->requireCsrf();
        Connection::deleteBetweenAsParty((int) Auth::id(), (int) $userId);
        if ($this->wantsJson()) {
            $this->json(['ok' => true]);
            return;
        }
        $this->flash('success', 'Connection removed.');
        $this->redirectBack("alumni/{$userId}");
    }

    /** Blocking severs the connection and stops the other user from sending a new request. */
    public function block(string $userId): void
    {
        $this->requireCsrf();
        $actingId = (int) Auth::id();
        Connection::deleteBetweenAsParty($actingId, (int) $userId);
        BlockedUser::block($actingId, (int) $userId);
        if ($this->wantsJson()) {
            $this->json(['ok' => true]);
            return;
        }
        $this->flash('success', 'User blocked.');
        $this->redirectBack("alumni/{$userId}");
    }

    public function follow(string $userId): void
    {
        $this->requireCsrf();
        if (Follow::follow((int) Auth::id(), (int) $userId)) {
            Notification::notify(
                (int) $userId,
                'new_follower',
                Auth::user()['name'] . ' started following you.',
                'alumni/' . Auth::id()
            );
        }
        if ($this->wantsJson()) {
            $this->json(['ok' => true]);
            return;
        }
        $this->redirectBack("alumni/{$userId}");
    }

    public function unfollow(string $userId): void
    {
        $this->requireCsrf();
        Follow::unfollow((int) Auth::id(), (int) $userId);
        if ($this->wantsJson()) {
            $this->json(['ok' => true]);
            return;
        }
        $this->redirectBack("alumni/{$userId}");
    }
}
