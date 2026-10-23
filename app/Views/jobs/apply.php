<?php
$inputTypes = ['text' => 'text', 'email' => 'email', 'phone' => 'tel', 'number' => 'number', 'url' => 'url'];
?>
<div class="max-w-[720px] mx-auto px-4 md:px-8 py-8">
  <a href="<?= e(url('jobs/' . $job['id'])) ?>" class="text-sm text-slate-500 hover:text-gold mb-4 inline-flex items-center gap-2"><i class="fa-solid fa-arrow-left"></i> Back to Job</a>

  <div class="card p-5 md:p-6 mb-6">
    <div class="flex items-center gap-3">
      <?= job_logo_html($job, 'w-12 h-12') ?>
      <div>
        <h1 class="text-base font-extrabold text-primary-navy">Apply for <?= e($job['title']) ?></h1>
        <p class="text-xs text-slate-500"><?= e($job['company']) ?><?= $job['location'] ? ' &bull; ' . e($job['location']) : '' ?></p>
      </div>
    </div>
  </div>

  <?php require dirname(__DIR__) . '/partials/errors.php'; ?>

  <?php if ($isResubmit): ?>
    <div class="card !bg-sky-50 !border-sky-100 p-4 mb-6 flex items-start gap-3">
      <i class="fa-solid fa-circle-info text-sky-500 mt-0.5"></i>
      <p class="text-xs text-sky-800">You've already applied to this job. Submitting again replaces your previous answers.</p>
    </div>
  <?php endif; ?>

  <form method="POST" action="<?= e(url('jobs/' . $job['id'] . '/apply')) ?>" enctype="multipart/form-data" class="card p-6 space-y-5">
    <?= csrf_field() ?>

    <?php if (empty($fields)): ?>
      <p class="text-sm text-slate-400">This job doesn't have any additional questions — just submit to apply.</p>
    <?php endif; ?>

    <?php foreach ($fields as $field): $prev = $previousByLabel[$field['label']] ?? ''; ?>
      <div>
        <label class="form-label" for="field_<?= e((string) $field['id']) ?>">
          <?= e($field['label']) ?> <?php if ($field['is_required']): ?><span class="text-red-500">*</span><?php endif; ?>
        </label>

        <?php if ($field['field_type'] === 'textarea'): ?>
          <textarea id="field_<?= e((string) $field['id']) ?>" name="field_<?= e((string) $field['id']) ?>" rows="4" class="form-textarea" <?= $field['is_required'] ? 'required' : '' ?>><?= e($prev) ?></textarea>

        <?php elseif ($field['field_type'] === 'select'): ?>
          <select id="field_<?= e((string) $field['id']) ?>" name="field_<?= e((string) $field['id']) ?>" class="form-input" <?= $field['is_required'] ? 'required' : '' ?>>
            <option value="">Select an option&hellip;</option>
            <?php foreach (array_filter(array_map('trim', explode(',', (string) $field['options']))) as $choice): ?>
              <option value="<?= e($choice) ?>" <?= $prev === $choice ? 'selected' : '' ?>><?= e($choice) ?></option>
            <?php endforeach; ?>
          </select>

        <?php elseif ($field['field_type'] === 'file'): ?>
          <input type="file" id="field_<?= e((string) $field['id']) ?>" name="field_<?= e((string) $field['id']) ?>" accept=".pdf,.doc,.docx" class="text-sm block" <?= $field['is_required'] && !$prev ? 'required' : '' ?>>
          <?php if ($prev): ?>
            <p class="text-xs text-slate-400 mt-1">Previously submitted: <a href="<?= e($prev) ?>" target="_blank" rel="noopener noreferrer" class="text-gold hover:underline">view file</a> &mdash; choose a new file to replace it, or leave blank to keep it.</p>
          <?php endif; ?>

        <?php else: ?>
          <input type="<?= e($inputTypes[$field['field_type']] ?? 'text') ?>" id="field_<?= e((string) $field['id']) ?>" name="field_<?= e((string) $field['id']) ?>" class="form-input" value="<?= e($prev) ?>" <?= $field['is_required'] ? 'required' : '' ?>>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>

    <div class="pt-4 border-t border-slate-100">
      <button type="submit" class="btn-gold !px-5 !py-2.5 text-sm w-full sm:w-auto flex items-center justify-center gap-2"><i class="fa-solid fa-paper-plane"></i> <?= $isResubmit ? 'Resubmit Application' : 'Submit Application' ?></button>
    </div>
  </form>
</div>
