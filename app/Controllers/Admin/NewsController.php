<?php

namespace App\Controllers\Admin;

use App\Core\Sanitizer;
use App\Core\Upload;
use App\Models\Category;
use App\Models\NewsPost;

class NewsController extends AdminController
{
    public function index(): void
    {
        $this->view('admin.news.index', [
            'title' => 'News & Blog',
            'activeNav' => 'news',
            'posts' => NewsPost::all('created_at DESC'),
        ]);
    }

    public function create(): void
    {
        $this->view('admin.news.form', [
            'title' => 'New News Post',
            'activeNav' => 'news',
            'post' => null,
            'allCategories' => Category::forType('news'),
            'selectedCategoryIds' => [],
        ]);
    }

    public function store(): void
    {
        $this->save(null);
    }

    public function edit(string $id): void
    {
        $post = NewsPost::find((int) $id);
        if (!$post) {
            $this->flash('error', 'News post not found.');
            $this->redirect('admin/news');
        }

        $this->view('admin.news.form', [
            'title' => 'Edit News Post',
            'activeNav' => 'news',
            'post' => $post,
            'allCategories' => Category::forType('news'),
            'selectedCategoryIds' => array_column(Category::forNews((int) $id), 'id'),
        ]);
    }

    public function update(string $id): void
    {
        $this->save((int) $id);
    }

    public function destroy(string $id): void
    {
        $this->requireCsrf();
        NewsPost::delete((int) $id);
        $this->flash('success', 'News post deleted.');
        $this->redirect('admin/news');
    }

    private function save(?int $id): void
    {
        $this->requireCsrf();

        $title = trim((string) $this->input('title', ''));
        $excerpt = trim((string) $this->input('excerpt', ''));
        $body = trim((string) $this->input('body', ''));
        $categoryIds = array_map('intval', (array) $this->input('categories', []));
        $author = trim((string) $this->input('author', 'RBSN Team'));
        $region = trim((string) $this->input('region', ''));
        $tags = trim((string) $this->input('tags', ''));
        $status = $this->input('status', 'published') === 'draft' ? 'draft' : 'published';
        $imageUrl = trim((string) $this->input('image_url', ''));

        $errors = [];
        if ($title === '') {
            $errors[] = 'Title is required.';
        }
        if ($body === '') {
            $errors[] = 'Body is required.';
        }
        $body = Sanitizer::html($body);

        $imagePath = $imageUrl !== '' ? $imageUrl : null;
        try {
            $uploaded = Upload::image($this->file('image'), 'news');
            if ($uploaded) {
                $imagePath = upload_url($uploaded);
            }
        } catch (\RuntimeException $e) {
            $errors[] = $e->getMessage();
        }

        if ($id === null && !$imagePath) {
            $errors[] = 'Please upload an image or provide an image URL.';
        }

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->redirect($id ? "admin/news/{$id}/edit" : 'admin/news/create');
        }

        $slugBase = $this->slugify($title);
        $slug = $slugBase;
        $i = 2;
        while (NewsPost::slugExists($slug, $id ?? 0)) {
            $slug = $slugBase . '-' . $i++;
        }

        $data = [
            'title' => $title,
            'slug' => $slug,
            'excerpt' => $excerpt !== '' ? $excerpt : trim(mb_substr(strip_tags((string) $body), 0, 200)),
            'body' => $body,
            'author' => $author,
            'region' => $region !== '' ? $region : null,
            'tags' => $tags !== '' ? $tags : null,
            'status' => $status,
            'published_at' => date('Y-m-d H:i:s'),
        ];
        if ($imagePath) {
            $data['image'] = $imagePath;
        }

        if ($id === null) {
            $id = NewsPost::create($data);
            $this->flash('success', 'News post created.');
        } else {
            NewsPost::update($id, $data);
            $this->flash('success', 'News post updated.');
        }
        Category::syncNewsCategories($id, $categoryIds);

        $this->redirect('admin/news');
    }

    private function slugify(string $text): string
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9]+/', '-', $text), '-'));
        return $slug !== '' ? $slug : 'post-' . time();
    }
}
