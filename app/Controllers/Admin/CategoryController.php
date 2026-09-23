<?php

namespace App\Controllers\Admin;

use App\Models\Category;

class CategoryController extends AdminController
{
    public function index(): void
    {
        $this->view('admin.categories.index', [
            'title' => 'Categories',
            'activeNav' => 'categories',
            'jobCategories' => Category::forType('job'),
            'newsCategories' => Category::forType('news'),
        ]);
    }

    public function store(): void
    {
        $this->requireCsrf();

        $type = $this->input('type') === 'news' ? 'news' : 'job';
        $name = trim((string) $this->input('name', ''));

        if ($name === '') {
            $this->flash('error', 'Category name is required.');
            $this->redirect('admin/categories');
        }

        try {
            Category::create($type, $name);
            $this->flash('success', 'Category added.');
        } catch (\PDOException $e) {
            $this->flash('error', 'A category with that name already exists for this type.');
        }

        $this->redirect('admin/categories');
    }

    public function edit(string $id): void
    {
        $category = Category::find((int) $id);
        if (!$category) {
            $this->flash('error', 'Category not found.');
            $this->redirect('admin/categories');
        }

        $this->view('admin.categories.edit', [
            'title' => 'Edit Category',
            'activeNav' => 'categories',
            'category' => $category,
        ]);
    }

    public function update(string $id): void
    {
        $this->requireCsrf();

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

        $this->redirect('admin/categories');
    }

    public function destroy(string $id): void
    {
        $this->requireCsrf();
        Category::delete((int) $id);
        $this->flash('success', 'Category deleted.');
        $this->redirect('admin/categories');
    }
}
