<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Sanitizer;
use App\Models\Article;

class KnowledgeController extends Controller
{
    private const CATEGORIES = ['article', 'research', 'case_study', 'white_paper'];

    public function index(): void
    {
        $this->requireVisibility('knowledge');

        $category = $this->input('category');
        $category = in_array($category, self::CATEGORIES, true) ? (string) $category : null;
        $search = trim((string) $this->input('q', ''));

        $this->view('knowledge.index', [
            'title' => 'Alumni Insights',
            'activeNav' => 'knowledge',
            'articles' => Article::published($category, $search !== '' ? $search : null),
            'activeCategory' => $category,
            'search' => $search,
        ]);
    }

    public function show(string $id): void
    {
        $article = Article::findPublished((int) $id);
        if (!$article) {
            http_response_code(404);
            (new ErrorController())->notFound();
            return;
        }

        $this->view('knowledge.show', [
            'title' => $article['title'],
            'activeNav' => 'knowledge',
            'article' => $article,
        ]);
    }

    public function submitForm(): void
    {
        $this->guard();
        $this->view('knowledge.submit', [
            'title' => 'Submit an Article',
            'activeNav' => 'knowledge',
            'categories' => self::CATEGORIES,
            'myArticles' => Article::byAuthor((int) Auth::id()),
            'needsRichEditor' => true,
        ]);
    }

    public function store(): void
    {
        $this->requireCsrf();
        $this->guard();

        $title = trim((string) $this->input('title', ''));
        $category = in_array($this->input('category'), self::CATEGORIES, true) ? $this->input('category') : 'article';
        $body = Sanitizer::html(trim((string) $this->input('body', '')));

        $errors = [];
        if ($title === '') {
            $errors[] = 'Title is required.';
        }
        if (!$body) {
            $errors[] = 'Body is required.';
        }

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->redirect('knowledge/submit');
        }

        Article::create([
            'user_id' => Auth::id(),
            'title' => $title,
            'category' => $category,
            'body' => $body,
        ]);

        $this->flash('success', 'Your submission has been sent for review. It will appear once approved by an admin.');
        $this->redirect('knowledge/submit');
    }

    private function guard(): void
    {
        if (!Auth::check() || !Auth::user()) {
            Auth::logout();
            header('Location: ' . url('login'));
            exit;
        }
    }
}
