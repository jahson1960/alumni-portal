<?php $isEdit = $benefit !== null; ?>
<a href="<?= e(url('admin/benefits')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to Benefits</a>

<?php require dirname(dirname(__DIR__)) . '/partials/errors.php'; ?>

<form method="POST" action="<?= e($isEdit ? url('admin/benefits/' . $benefit['id']) : url('admin/benefits')) ?>" class="card p-6 max-w-2xl space-y-4">
  <?= csrf_field() ?>

  <div>
    <label class="form-label" for="title">Title</label>
    <input type="text" id="title" name="title" class="form-input" value="<?= e($benefit['title'] ?? '') ?>" required>
  </div>

  <div>
    <label class="form-label" for="category">Category</label>
    <input type="text" id="category" name="category" class="form-input" value="<?= e($benefit['category'] ?? 'General') ?>">
  </div>

  <div>
    <label class="form-label" for="link">Link URL</label>
    <input type="text" id="link" name="link" class="form-input" placeholder="https://..." value="<?= e($benefit['link'] ?? '') ?>">
  </div>

  <div>
    <label class="form-label" for="description">Description</label>
    <textarea id="description" name="description" rows="3" class="form-textarea"><?= e($benefit['description'] ?? '') ?></textarea>
  </div>

  <button type="submit" class="btn-gold"><?= $isEdit ? 'Save Changes' : 'Create Benefit' ?></button>
</form>
