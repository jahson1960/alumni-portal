<?php $isEdit = $cause !== null; ?>
<a href="<?= e(url('admin/giving-causes')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to Causes</a>

<?php require dirname(dirname(__DIR__)) . '/partials/errors.php'; ?>

<form method="POST" action="<?= e($isEdit ? url('admin/giving-causes/' . $cause['id']) : url('admin/giving-causes')) ?>" class="card p-6 max-w-2xl space-y-4">
  <?= csrf_field() ?>

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="form-label" for="column_group">Menu Column</label>
      <select id="column_group" name="column_group" class="form-input">
        <?php $groupLabels = ['support' => 'Support the Future', 'involve' => 'Get Involved', 'impact' => 'Create Impact']; ?>
        <?php foreach ($groupLabels as $value => $label): ?>
          <option value="<?= e($value) ?>" <?= ($cause['column_group'] ?? 'support') === $value ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="form-label" for="icon">Icon (Font Awesome class)</label>
      <input type="text" id="icon" name="icon" class="form-input" placeholder="fa-solid fa-heart" value="<?= e($cause['icon'] ?? 'fa-solid fa-heart') ?>">
    </div>
  </div>

  <div>
    <label class="form-label" for="title">Title</label>
    <input type="text" id="title" name="title" class="form-input" value="<?= e($cause['title'] ?? '') ?>" required>
  </div>

  <div>
    <label class="form-label" for="slug">URL Slug</label>
    <input type="text" id="slug" name="slug" class="form-input" placeholder="e.g. make-a-donation" value="<?= e($cause['slug'] ?? '') ?>">
    <p class="text-xs text-slate-400 mt-1">Leave blank to auto-generate from the title. Page will be at /give/causes/&lt;slug&gt;. Changing this after publishing will break existing links.</p>
  </div>

  <div>
    <label class="form-label" for="description">Short Description</label>
    <input type="text" id="description" name="description" class="form-input" placeholder="One line shown in the menu" value="<?= e($cause['description'] ?? '') ?>">
  </div>

  <div>
    <label class="form-label">Page Content</label>
    <div data-rich-editor="body"></div>
    <textarea id="body" name="body" class="hidden"><?= e($cause['body'] ?? '') ?></textarea>
    <p class="text-xs text-slate-400 mt-1">Shown on the cause's own detail page.</p>
  </div>

  <button type="submit" class="btn-gold"><?= $isEdit ? 'Save Changes' : 'Create Cause' ?></button>
</form>
