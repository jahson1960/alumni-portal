<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Connection;
use App\Models\Message;
use App\Models\Notification;
use App\Models\User;

class MessageController extends Controller
{
    public function __construct()
    {
        if (!Auth::check() || !Auth::user()) {
            Auth::logout();
            header('Location: ' . url('login'));
            exit;
        }
    }

    public function index(): void
    {
        $this->requireVisibility('messages');
        $this->render(null, $this->input('archived') === '1');
    }

    public function show(string $userId): void
    {
        $this->requireVisibility('messages');

        $viewerId = (int) Auth::id();
        $partnerId = (int) $userId;
        $partner = User::find($partnerId);

        if (!$partner || $partnerId === $viewerId || !$this->areConnected($viewerId, $partnerId)) {
            $this->flash('error', 'You can only message alumni you are connected with.');
            $this->redirect('messages');
        }

        Message::markThreadRead($viewerId, $partnerId);

        [$backUrl, $backLabel] = $this->smartBack(url('messages'), 'Back to Messages');

        $this->render($partner, false, $backUrl, $backLabel);
    }

    private function render(?array $partner, bool $archived = false, ?string $backUrl = null, string $backLabel = 'Back to Messages'): void
    {
        $viewerId = (int) Auth::id();

        $this->view('messages.index', [
            'title' => $partner ? 'Message ' . $partner['name'] : 'Messages',
            'activeNav' => 'messages',
            'archived' => $archived,
            'backUrl' => $backUrl ?? url('messages'),
            'backLabel' => $backLabel,
            'conversations' => $archived ? [] : Message::conversations($viewerId),
            'partner' => $partner,
            'messages' => $partner ? Message::thread($viewerId, (int) $partner['id']) : [],
            'mutualConnections' => $partner ? Connection::mutualConnections($viewerId, (int) $partner['id'], 3) : [],
            'mutualCount' => $partner ? Connection::countMutualConnections($viewerId, (int) $partner['id']) : 0,
        ]);
    }

    public function store(string $userId): void
    {
        $this->requireCsrf();
        $viewerId = (int) Auth::id();
        $partnerId = (int) $userId;

        if (!$this->areConnected($viewerId, $partnerId)) {
            $this->flash('error', 'You can only message alumni you are connected with.');
            $this->redirect('messages');
        }

        $body = trim((string) $this->input('body', ''));
        if ($body !== '') {
            Message::send($viewerId, $partnerId, $body);
            Notification::notify(
                $partnerId,
                'new_message',
                Auth::user()['name'] . ' sent you a message.',
                'messages/' . $viewerId
            );
        }

        $this->redirect("messages/{$partnerId}");
    }

    /** JSON polling endpoint: new messages in this thread since a given id. */
    public function poll(string $userId): void
    {
        header('Content-Type: application/json');
        $viewerId = (int) Auth::id();
        $partnerId = (int) $userId;

        if (!$this->areConnected($viewerId, $partnerId)) {
            http_response_code(403);
            echo json_encode(['error' => 'Not connected.']);
            return;
        }

        $since = (int) $this->input('since', 0);
        Message::markThreadRead($viewerId, $partnerId);
        $newMessages = Message::thread($viewerId, $partnerId, $since);

        echo json_encode(['messages' => array_map(fn ($m) => [
            'id' => (int) $m['id'],
            'sender_id' => (int) $m['sender_id'],
            'body' => $m['body'],
            'created_at' => $m['created_at'],
        ], $newMessages)]);
    }

    private function areConnected(int $a, int $b): bool
    {
        $status = Connection::statusBetween($a, $b);
        return $status !== null && $status['status'] === 'accepted';
    }
}
