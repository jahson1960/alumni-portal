<?php

namespace App\Controllers\Admin;

use App\Models\Category;

class CategoryController extends AdminController
{
    /** Old bookmarked URL for the combined Job+News categories page, now split in two. */
    public function index(): void
    {
        $this->redirect('admin/job-categories');
    }

    public function jobIndex(): void
    {
        $p = $this->paginate(Category::countByType('job'), 30);
        $this->view('admin.categories.job_index', [
            'title' => 'Job Categories',
            'activeNav' => 'job_categories',
            'categories' => Category::paginateByType('job', $p['perPage'], $p['offset']),
            ...$p,
        ]);
    }

    public function newsIndex(): void
    {
        $p = $this->paginate(Category::countByType('news'), 30);
        $this->view('admin.categories.news_index', [
            'title' => 'News Categories',
            'activeNav' => 'news_categories',
            'categories' => Category::paginateByType('news', $p['perPage'], $p['offset']),
            ...$p,
        ]);
    }

    public function store(): void
    {
        $this->requireCsrf();

        $type = $this->input('type') === 'news' ? 'news' : 'job';
        $name = trim((string) $this->input('name', ''));
        $redirectTo = $type === 'news' ? 'admin/news-categories' : 'admin/job-categories';
        $wantsJson = ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';

        if ($name === '') {
            if ($wantsJson) {
                $this->jsonError('Category name is required.');
                return;
            }
            $this->flash('error', 'Category name is required.');
            $this->redirect($redirectTo);
        }

        try {
            $id = Category::create($type, $name);
            if ($wantsJson) {
                $newCategory = Category::find($id);
                header('Content-Type: application/json');
                echo json_encode([
                    'id' => $id,
                    'name' => $newCategory['name'],
                    'html' => job_category_chip_html($newCategory),
                ]);
                return;
            }
            $this->flash('success', 'Category added.');
        } catch (\PDOException $e) {
            if ($wantsJson) {
                $this->jsonError('A category with that name already exists for this type.');
                return;
            }
            $this->flash('error', 'A category with that name already exists for this type.');
        }

        $this->redirect($redirectTo);
    }

    private function jsonError(string $message): void
    {
        http_response_code(422);
        header('Content-Type: application/json');
        echo json_encode(['error' => $message]);
    }

    public function edit(string $id): void
    {
        $category = Category::find((int) $id);
        if (!$category) {
            $this->flash('error', 'Category not found.');
            $this->redirect('admin/job-categories');
        }

        $this->view('admin.categories.edit', [
            'title' => 'Edit Category',
            'activeNav' => $category['type'] === 'news' ? 'news_categories' : 'job_categories',
            'category' => $category,
        ]);
    }

    public function update(string $id): void
    {
        $this->requireCsrf();

        $category = Category::find((int) $id);
        $redirectTo = ($category && $category['type'] === 'news') ? 'admin/news-categories' : 'admin/job-categories';

        $name = trim((string) $this->input('name', ''));
        if ($name === '') {
            $this->flash('error', 'Category name is required.');
            $this->redirect("admin/categories/{$id}/edit");
        }

        try {
            Category::update((int) $id, $name);
            $this->flash('success', 'Category updated.');
        } catch (\PDOException $e) {
            $this->flash('error', 'A category with that name already exists for this type.');
        }

        $this->redirect($redirectTo);
    }

    public function destroy(string $id): void
    {
        $this->requireCsrf();

        $category = Category::find((int) $id);
        $redirectTo = ($category && $category['type'] === 'news') ? 'admin/news-categories' : 'admin/job-categories';

        Category::delete((int) $id);
        $this->flash('success', 'Category deleted.');
        $this->redirect($redirectTo);
    }
}
