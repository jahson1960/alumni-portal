<?php $isEdit = $alert !== null; ?>
<div class="max-w-2xl mx-auto px-4 md:px-8 py-8">
  <a href="<?= e(url('jobs') . '?tab=alerts') ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to Job Alerts</a>

  <div class="section-header">
    <h1 class="section-title text-base"><?= $isEdit ? 'Edit Job Alert' : 'Create Job Alert' ?></h1>
  </div>

  <?php require dirname(__DIR__, 2) . '/partials/errors.php'; ?>

  <form method="POST" action="<?= e($isEdit ? url('jobs/alerts/' . $alert['id']) : url('jobs/alerts')) ?>" class="card p-6 space-y-4">
    <?= csrf_field() ?>

    <div>
      <label class="form-label" for="name">Alert Name</label>
      <input type="text" id="name" name="name" class="form-input" placeholder="e.g. Product Manager in Technology" value="<?= e($alert['name'] ?? '') ?>" required>
    </div>

    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="form-label" for="keywords">Keywords</label>
        <input type="text" id="keywords" name="keywords" class="form-input" placeholder="Job title, skills..." value="<?= e($alert['keywords'] ?? '') ?>">
      </div>
      <div>
        <label class="form-label" for="location">Location</label>
        <input type="text" id="location" name="location" class="form-input" placeholder="Lagos, Nigeria" value="<?= e($alert['location'] ?? '') ?>">
      </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="form-label" for="category_id">Job Category</label>
        <select id="category_id" name="category_id" class="form-input">
          <option value="">Any category</option>
          <?php foreach ($allCategories as $cat): ?>
            <option value="<?= $cat['id'] ?>" <?= (int) ($alert['category_id'] ?? 0) === (int) $cat['id'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="form-label" for="job_type">Job Type</label>
        <select id="job_type" name="job_type" class="form-input">
          <option value="">Any type</option>
          <?php foreach (\App\Models\Job::JOB_TYPES as $type): ?>
            <option value="<?= e($type) ?>" <?= ($alert['job_type'] ?? '') === $type ? 'selected' : '' ?>><?= e($type) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="form-label" for="work_mode">Work Mode</label>
        <select id="work_mode" name="work_mode" class="form-input">
          <option value="">Any</option>
          <?php foreach (\App\Models\Job::WORK_MODES as $key => $label): ?>
            <option value="<?= e($key) ?>" <?= ($alert['work_mode'] ?? '') === $key ? 'selected' : '' ?>><?= e($label) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div>
        <label class="form-label" for="frequency">Email Frequency</label>
        <select id="frequency" name="frequency" class="form-input">
          <option value="daily" <?= ($alert['frequency'] ?? 'daily') === 'daily' ? 'selected' : '' ?>>Daily</option>
          <option value="weekly" <?= ($alert['frequency'] ?? '') === 'weekly' ? 'selected' : '' ?>>Weekly</option>
        </select>
      </div>
    </div>

    <button type="submit" class="btn-gold"><?= $isEdit ? 'Save Changes' : 'Create Alert' ?></button>
  </form>
</div>
