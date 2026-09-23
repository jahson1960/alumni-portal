<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Category;
use App\Models\NewsPost;
use App\Models\SavedNewsPost;
use App\Models\Setting;

class NewsController extends Controller
{
    private const PER_PAGE = 9;
    private const SORTS = ['recent', 'oldest'];

    public function index(): void
    {
        $this->requireVisibility('news');

        $categorySlug = $this->input('category');
        $categorySlug = $categorySlug !== null && $categorySlug !== '' ? (string) $categorySlug : null;
        $q = trim((string) $this->input('q', ''));
        $sort = in_array($this->input('sort'), self::SORTS, true) ? $this->input('sort') : 'recent';
        $page = max(1, (int) $this->input('page', 1));

        $total = NewsPost::countPublished($categorySlug, $q !== '' ? $q : null);
        $totalPages = max(1, (int) ceil($total / self::PER_PAGE));
        $page = min($page, $totalPages);

        $viewerId = Auth::id();

        $showFeatured = $categorySlug === null && $q === '' && $page === 1;
        $featuredPost = $showFeatured ? (NewsPost::published(1)[0] ?? null) : null;

        $this->view('news.index', [
            'title' => 'News & Blog',
            'activeNav' => 'news',
            'posts' => NewsPost::published(self::PER_PAGE, $categorySlug, $q !== '' ? $q : null, $sort, ($page - 1) * self::PER_PAGE),
            'featuredPost' => $featuredPost,
            'recentWidget' => NewsPost::published(5),
            'allCategories' => Category::forType('news'),
            'categoryCounts' => NewsPost::categoryCounts(),
            'totalCount' => NewsPost::countPublished(),
            'activeCategory' => $categorySlug,
            'search' => $q,
            'sort' => $sort,
            'page' => $page,
            'totalPages' => $totalPages,
            'savedIds' => $viewerId ? SavedNewsPost::savedIdsFor((int) $viewerId) : [],
            'submitEmail' => Setting::get('news_submit_email', 'stories@rbsn.example.com'),
        ]);
    }

    public function toggleSave(string $id): void
    {
        $this->requireCsrf();
        if (!Auth::check()) {
            $this->redirect('login');
        }

        SavedNewsPost::toggle((int) Auth::id(), (int) $id);
        $this->redirectBack('news');
    }

    public function show(string $slug): void
    {
        $post = NewsPost::findBySlug($slug);
        if (!$post) {
            http_response_code(404);
            (new ErrorController())->notFound();
            return;
        }

        $categories = Category::forNews((int) $post['id']);
        $categorySlug = $categories[0]['slug'] ?? null;

        $related = $categorySlug ? NewsPost::published(4, $categorySlug) : [];
        $related = array_filter($related, fn ($p) => (int) $p['id'] !== (int) $post['id']);
        if (count($related) < 3) {
            $more = NewsPost::published(3 + count($related) + 1);
            $more = array_filter($more, fn ($p) => (int) $p['id'] !== (int) $post['id'] && !in_array((int) $p['id'], array_column($related, 'id'), true));
            $related = array_merge($related, $more);
        }
        $related = array_slice($related, 0, 3);

        $tags = array_filter(array_map('trim', explode(',', (string) ($post['tags'] ?? ''))));

        $this->view('news.show', [
            'title' => $post['title'],
            'activeNav' => 'news',
            'post' => $post,
            'categories' => $categories,
            'tags' => $tags,
            'related' => $related,
        ]);
    }
}
