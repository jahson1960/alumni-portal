<?php

namespace App\Controllers\Admin;

use App\Models\Article;

class ArticleController extends AdminController
{
    public function index(): void
    {
        $pendingP = $this->paginate(Article::countPending(), 30, 'pending');
        $publishedP = $this->paginate(Article::countPublishedAll(), 30, 'published');

        $this->view('admin.articles.index', [
            'title' => 'Article Review Queue',
            'activeNav' => 'articles',
            'pending' => Article::paginatePending($pendingP['perPage'], $pendingP['offset']),
            'published' => Article::paginatePublished($publishedP['perPage'], $publishedP['offset']),
            'pendingPagination' => $pendingP,
            'publishedPagination' => $publishedP,
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
