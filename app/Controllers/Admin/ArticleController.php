<?php

namespace App\Controllers\Admin;

use App\Models\Article;

class ArticleController extends AdminController
{
    public function index(): void
    {
        $this->view('admin.articles.index', [
            'title' => 'Article Review Queue',
            'activeNav' => 'articles',
            'pending' => Article::pending(),
            'published' => Article::published(),
        ]);
    }

    public function publish(string $id): void
    {
        $this->requireCsrf();
        Article::publish((int) $id);
        $this->flash('success', 'Article published.');
        $this->redirect('admin/articles');
    }

    public function destroy(string $id): void
    {
        $this->requireCsrf();
        Article::delete((int) $id);
        $this->flash('success', 'Article removed.');
        $this->redirect('admin/articles');
    }
}
