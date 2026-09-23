<?php $isEdit = $resource !== null; ?>
<a href="<?= e(url('admin/resources')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to Resources</a>

<?php require dirname(dirname(__DIR__)) . '/partials/errors.php'; ?>

<form method="POST" action="<?= e($isEdit ? url('admin/resources/' . $resource['id']) : url('admin/resources')) ?>" class="card p-6 max-w-2xl space-y-4">
  <?= csrf_field() ?>

  <div>
    <label class="form-label" for="title">Title</label>
    <input type="text" id="title" name="title" class="form-input" value="<?= e($resource['title'] ?? '') ?>" required>
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="form-label" for="category">Category</label>
      <select id="category" name="category" class="form-input">
        <?php foreach (array_merge(\App\Models\Resource::CAREER_CATEGORIES, \App\Models\Resource::INTERVIEW_CATEGORIES) as $cat): ?>
          <option value="<?= e($cat) ?>" <?= ($resource['category'] ?? 'Career') === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
        <?php endforeach; ?>
      </select>
      <p class="text-xs text-slate-400 mt-1">Business/Career/Personal Development show on the Career Resources tab; the rest show on Interview Prep.</p>
    </div>
    <div>
      <label class="form-label" for="resource_type">Type</label>
      <select id="resource_type" name="resource_type" class="form-input">
        <?php foreach (\App\Models\Resource::TYPES as $type): ?>
          <option value="<?= e($type) ?>" <?= ($resource['resource_type'] ?? 'Guide') === $type ? 'selected' : '' ?>><?= e($type) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="form-label" for="read_time_minutes">Read Time (minutes)</label>
      <input type="number" id="read_time_minutes" name="read_time_minutes" class="form-input" min="0" value="<?= e($resource['read_time_minutes'] ?? '') ?>">
    </div>
    <div>
      <label class="form-label" for="difficulty">Difficulty</label>
      <select id="difficulty" name="difficulty" class="form-input">
        <option value="">Not specified</option>
        <?php foreach (['beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced'] as $key => $label): ?>
          <option value="<?= e($key) ?>" <?= ($resource['difficulty'] ?? '') === $key ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div>
    <label class="form-label" for="link">Link URL</label>
    <input type="text" id="link" name="link" class="form-input" placeholder="https://..." value="<?= e($resource['link'] ?? '') ?>">
  </div>

  <div>
    <label class="form-label" for="description">Description</label>
    <textarea id="description" name="description" rows="3" class="form-textarea"><?= e($resource['description'] ?? '') ?></textarea>
  </div>

  <label class="flex items-center gap-2">
    <input type="checkbox" id="is_featured" name="is_featured" value="1" <?= !empty($resource['is_featured']) ? 'checked' : '' ?> class="rounded border-slate-300 text-gold focus:ring-gold">
    <span class="text-sm text-slate-700">Feature on the Career Resources tab</span>
  </label>

  <button type="submit" class="btn-gold"><?= $isEdit ? 'Save Changes' : 'Create Resource' ?></button>
</form>
