<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Upload;
use App\Models\Follow;
use App\Models\Notification;
use App\Models\Post;
use App\Models\PostComment;
use App\Models\PostLike;

class FeedController extends Controller
{
    private const PER_PAGE = 15;
    private const POST_TYPES = ['general', 'achievement', 'announcement', 'question', 'job_opportunity', 'event', 'partnership'];
    private const SORTS = ['recent', 'liked'];
    private const VISIBILITIES = ['public', 'connections'];

    /** The composer's quick post-type picker — a curated subset of POST_TYPES with its own icons. */
    private const COMPOSER_TYPES = [
        'general' => ['label' => 'Update', 'icon' => 'bi-file-earmark-text'],
        'achievement' => ['label' => 'Achievement', 'icon' => 'bi-trophy'],
        'question' => ['label' => 'Question', 'icon' => 'bi-question-circle'],
        'job_opportunity' => ['label' => 'Job Opportunity', 'icon' => 'bi-briefcase'],
        'event' => ['label' => 'Event', 'icon' => 'bi-calendar-event'],
    ];

    /** The sidebar's category filter — a curated subset of POST_TYPES with its own labels/icons. */
    private const FEED_CATEGORIES = [
        '' => ['label' => 'All Posts', 'icon' => 'bi-grid'],
        'achievement' => ['label' => 'Achievements', 'icon' => 'bi-trophy'],
        'announcement' => ['label' => 'Announcements', 'icon' => 'bi-megaphone'],
        'question' => ['label' => 'Questions', 'icon' => 'bi-question-circle'],
        'general' => ['label' => 'General Discussions', 'icon' => 'bi-chat-square-text'],
    ];

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
        $this->requireVisibility('feed');

        $type = array_key_exists($this->input('type', ''), self::FEED_CATEGORIES) ? $this->input('type', '') : '';
        $sort = in_array($this->input('sort'), self::SORTS, true) ? $this->input('sort') : 'recent';

        $page = max(1, (int) $this->input('page', 1));
        $offset = ($page - 1) * self::PER_PAGE;
        $viewerId = (int) Auth::id();
        $posts = Post::feed(self::PER_PAGE, $offset, $type !== '' ? $type : null, $sort, $viewerId);
        $hasMore = count($posts) === self::PER_PAGE;

        if ($this->wantsJson()) {
            // Infinite-scroll: subsequent pages are fetched as an HTML fragment, not a full page.
            header('X-Has-More: ' . ($hasMore ? '1' : '0'));
            $this->view('feed.partials.posts_list', [
                'posts' => $this->decoratePosts($posts),
            ], false);
            return;
        }

        $this->view('feed.index', [
            'title' => 'Alumni Feed',
            'activeNav' => 'feed',
            'posts' => $this->decoratePosts($posts),
            'postTypes' => self::POST_TYPES,
            'composerTypes' => self::COMPOSER_TYPES,
            'categories' => self::FEED_CATEGORIES,
            'activeType' => $type,
            'sort' => $sort,
            'page' => $page,
            'hasMore' => $hasMore,
            'totalCount' => Post::countByType($type !== '' ? $type : null),
            'draftCount' => Post::countDraftsForUser($viewerId),
        ]);
    }

    public function show(string $id): void
    {
        $post = Post::findWithAuthor((int) $id);
        if (!$post || !$this->postAccessible($post)) {
            http_response_code(404);
            (new ErrorController())->notFound();
            return;
        }

        $decorated = $this->decoratePosts([$post])[0];

        $this->view('feed.show', [
            'title' => 'Post by ' . $post['author_name'],
            'activeNav' => 'feed',
            'post' => $decorated,
            'comments' => PostComment::forPost((int) $id),
        ]);
    }

    public function store(): void
    {
        $this->requireCsrf();

        $content = trim((string) $this->input('content', ''));
        $postType = in_array($this->input('post_type'), self::POST_TYPES, true) ? $this->input('post_type') : 'general';
        $visibility = in_array($this->input('visibility'), self::VISIBILITIES, true) ? $this->input('visibility') : 'public';
        $asDraft = $this->input('action') === 'draft';

        if ($content === '') {
            $this->flash('error', 'Write something before posting.');
            $this->redirect('feed');
        }

        $image = null;
        try {
            $uploaded = Upload::image($this->file('image'), 'posts');
            if ($uploaded) {
                $image = upload_url($uploaded);
            }
        } catch (\RuntimeException $e) {
            $this->flash('error', $e->getMessage());
            $this->redirect('feed');
        }

        Post::create([
            'user_id' => Auth::id(),
            'post_type' => $postType,
            'status' => $asDraft ? 'draft' : 'published',
            'visibility' => $visibility,
            'content' => $content,
            'image' => $image,
        ]);

        $this->flash('success', $asDraft ? 'Draft saved.' : 'Your post has been shared with the community.');
        $this->redirect($asDraft ? 'feed/drafts' : 'feed');
    }

    public function destroy(string $id): void
    {
        $this->requireCsrf();
        Post::deleteAsAuthorOrAdmin((int) $id, (int) Auth::id(), Auth::isAdmin());
        $this->flash('success', 'Post deleted.');
        $this->redirectBack('feed');
    }

    public function drafts(): void
    {
        $this->requireVisibility('feed');

        $this->view('feed.drafts', [
            'title' => 'My Drafts',
            'activeNav' => 'feed',
            'drafts' => Post::draftsForUser((int) Auth::id()),
            'composerTypes' => self::COMPOSER_TYPES,
        ]);
    }

    public function updateDraft(string $id): void
    {
        $this->requireCsrf();
        $draft = Post::findDraftOwnedBy((int) $id, (int) Auth::id());
        if (!$draft) {
            http_response_code(404);
            (new ErrorController())->notFound();
            return;
        }

        $content = trim((string) $this->input('content', ''));
        if ($content === '') {
            $this->flash('error', 'Write something before saving.');
            $this->redirect('feed/drafts');
        }

        $postType = in_array($this->input('post_type'), self::POST_TYPES, true) ? $this->input('post_type') : $draft['post_type'];
        $visibility = in_array($this->input('visibility'), self::VISIBILITIES, true) ? $this->input('visibility') : $draft['visibility'];
        $publish = $this->input('action') === 'publish';

        Post::update((int) $id, [
            'content' => $content,
            'post_type' => $postType,
            'visibility' => $visibility,
            'status' => $publish ? 'published' : 'draft',
        ]);

        $this->flash('success', $publish ? 'Draft published to the feed.' : 'Draft updated.');
        $this->redirect($publish ? 'feed' : 'feed/drafts');
    }

    public function destroyDraft(string $id): void
    {
        $this->requireCsrf();
        $draft = Post::findDraftOwnedBy((int) $id, (int) Auth::id());
        if ($draft) {
            Post::deleteAsAuthorOrAdmin((int) $id, (int) Auth::id(), false);
            $this->flash('success', 'Draft discarded.');
        }
        $this->redirect('feed/drafts');
    }

    public function like(string $id): void
    {
        $this->requireCsrf();
        $post = Post::findWithAuthor((int) $id);
        if (!$post || !$this->postAccessible($post)) {
            http_response_code(404);
            (new ErrorController())->notFound();
            return;
        }

        $liked = PostLike::toggle((int) $id, (int) Auth::id());

        if ($liked && (int) $post['user_id'] !== (int) Auth::id()) {
            Notification::notify(
                (int) $post['user_id'],
                'post_liked',
                Auth::user()['name'] . ' liked your post.',
                'feed/' . $id
            );
        }

        if ($this->wantsJson()) {
            header('Content-Type: application/json');
            echo json_encode(['liked' => $liked, 'count' => PostLike::count((int) $id)]);
            return;
        }

        $this->redirectBack("feed/{$id}");
    }

    /** Renders the "comment modal" body (post preview + comments list) — used both for the initial open and after posting/deleting a comment via AJAX. */
    public function commentsFragment(string $id): void
    {
        $post = Post::findWithAuthor((int) $id);
        if (!$post || !$this->postAccessible($post)) {
            http_response_code(404);
            return;
        }

        $this->view('feed.partials.comments_modal', [
            'post' => $this->decoratePosts([$post])[0],
            'comments' => PostComment::forPost((int) $id),
        ], false);
    }

    public function share(string $id): void
    {
        $this->requireCsrf();

        $original = Post::findWithAuthor((int) $id);
        if (!$original || !$this->postAccessible($original)) {
            $this->flash('error', 'That post no longer exists.');
            $this->redirect('feed');
        }

        $caption = trim((string) $this->input('caption', ''));

        Post::create([
            'user_id' => Auth::id(),
            'post_type' => 'general',
            'content' => $caption,
            'shared_post_id' => (int) $id,
        ]);

        $this->flash('success', 'Post shared to your feed.');
        $this->redirect('feed');
    }

    public function comment(string $id): void
    {
        $this->requireCsrf();

        $post = Post::findWithAuthor((int) $id);
        if (!$post || !$this->postAccessible($post)) {
            http_response_code(404);
            (new ErrorController())->notFound();
            return;
        }

        $content = trim((string) $this->input('content', ''));
        if ($content !== '') {
            PostComment::create((int) $id, (int) Auth::id(), $content);

            if ((int) $post['user_id'] !== (int) Auth::id()) {
                Notification::notify(
                    (int) $post['user_id'],
                    'post_commented',
                    Auth::user()['name'] . ' commented on your post.',
                    'feed/' . $id
                );
            }
        }

        if ($this->wantsJson()) {
            $this->commentsFragment($id);
            return;
        }

        $this->redirect("feed/{$id}");
    }

    public function deleteComment(string $commentId): void
    {
        $this->requireCsrf();
        $postId = PostComment::postIdFor((int) $commentId);
        PostComment::deleteAsAuthorOrAdmin((int) $commentId, (int) Auth::id(), Auth::isAdmin());

        if ($this->wantsJson()) {
            if ($postId) {
                $this->commentsFragment((string) $postId);
            }
            return;
        }

        $this->redirect($postId ? "feed/{$postId}" : 'feed');
    }

    /** True when the request came from the feed page's own JS (fetch), which wants a JSON/HTML reply instead of a redirect. */
    private function wantsJson(): bool
    {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
    }

    /** A draft is only accessible to its author; everything else follows normal feed visibility. */
    private function postAccessible(array $post): bool
    {
        return $post['status'] === 'published' || (int) $post['user_id'] === (int) Auth::id();
    }

    private function decoratePosts(array $posts): array
    {
        $viewerId = (int) Auth::id();

        foreach ($posts as &$post) {
            $post['like_count'] = PostLike::count((int) $post['id']);
            $post['viewer_has_liked'] = PostLike::hasLiked((int) $post['id'], $viewerId);
            $post['comment_count'] = PostComment::countForPost((int) $post['id']);
            $post['viewer_is_author'] = (int) $post['user_id'] === $viewerId;
            $post['viewer_is_following_author'] = $post['viewer_is_author'] ? false : Follow::isFollowing($viewerId, (int) $post['user_id']);
            $post['shared_post'] = $post['shared_post_id'] ? Post::findWithAuthor((int) $post['shared_post_id']) : null;
        }

        return $posts;
    }
}
