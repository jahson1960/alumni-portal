<?php $isEdit = $post !== null; ?>
<a href="<?= e(url('admin/news')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to News</a>

<?php require dirname(dirname(__DIR__)) . '/partials/errors.php'; ?>

<form method="POST" action="<?= e($isEdit ? url('admin/news/' . $post['id']) : url('admin/news')) ?>" enctype="multipart/form-data" class="card p-6 max-w-2xl space-y-4">
  <?= csrf_field() ?>

  <div>
    <label class="form-label" for="title">Title</label>
    <input type="text" id="title" name="title" class="form-input" value="<?= e($post['title'] ?? '') ?>" required>
  </div>

  <div>
    <label class="form-label" for="status">Status</label>
    <select id="status" name="status" class="form-input max-w-xs">
      <option value="published" <?= ($post['status'] ?? 'published') === 'published' ? 'selected' : '' ?>>Published</option>
      <option value="draft" <?= ($post['status'] ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
    </select>
  </div>

  <div>
    <label class="form-label">Categories</label>
    <div class="flex flex-wrap gap-3">
      <?php foreach ($allCategories as $cat): ?>
        <label class="flex items-center gap-1.5 text-sm text-slate-700 bg-slate-50 border border-slate-200 rounded px-2.5 py-1.5">
          <input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>" <?= in_array($cat['id'], $selectedCategoryIds, true) ? 'checked' : '' ?> class="rounded border-slate-300 text-gold focus:ring-gold">
          <?= e($cat['name']) ?>
        </label>
      <?php endforeach; ?>
      <?php if (empty($allCategories)): ?>
        <p class="text-xs text-slate-400">No news categories yet. <a href="<?= e(url('admin/categories')) ?>" class="text-gold hover:underline">Add one</a>.</p>
      <?php endif; ?>
    </div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
    <div>
      <label class="form-label" for="author">Author</label>
      <input type="text" id="author" name="author" class="form-input" value="<?= e($post['author'] ?? 'RBSN Team') ?>">
    </div>
    <div>
      <label class="form-label" for="region">Region</label>
      <input type="text" id="region" name="region" class="form-input" placeholder="e.g. Africa, Europe, Global" value="<?= e($post['region'] ?? '') ?>">
    </div>
  </div>

  <div>
    <label class="form-label" for="tags">Tags</label>
    <input type="text" id="tags" name="tags" class="form-input" placeholder="Comma-separated, e.g. Alumni Network, Mentorship, Leadership" value="<?= e($post['tags'] ?? '') ?>">
  </div>

  <div>
    <label class="form-label" for="excerpt">Excerpt</label>
    <textarea id="excerpt" name="excerpt" rows="2" class="form-textarea"><?= e($post['excerpt'] ?? '') ?></textarea>
  </div>

  <div>
    <label class="form-label">Body</label>
    <div data-rich-editor="body"></div>
    <textarea id="body" name="body" class="hidden"><?= e($post['body'] ?? '') ?></textarea>
  </div>

  <div>
    <label class="form-label">Featured Image</label>
    <?php if (!empty($post['image'])): ?>
      <img src="<?= e($post['image']) ?>" alt="" class="w-32 h-20 object-cover rounded mb-2">
    <?php endif; ?>
    <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" class="text-sm mb-2 block">
    <input type="url" name="image_url" placeholder="...or paste an image URL" class="form-input" value="<?= (!empty($post['image']) && !str_starts_with($post['image'], asset('uploads'))) ? e($post['image']) : '' ?>">
  </div>

  <button type="submit" class="btn-gold"><?= $isEdit ? 'Save Changes' : 'Publish Post' ?></button>
</form>
