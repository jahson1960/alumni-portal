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

  <form method="POST" action="<?= e(url('jobs/' . $job['id'] . '/apply')) ?>" enctype="multipart/form-data" class="card p-6 space-y-6">
    <?= csrf_field() ?>

    <?php if (empty($fieldsBySection['profile_info']) && empty($fieldsBySection['education']) && empty($fieldsBySection['experience'])): ?>
      <p class="text-sm text-slate-400">This job doesn't have any additional questions — just submit to apply.</p>
    <?php endif; ?>

    <?php foreach (\App\Models\ApplicationTemplate::SECTIONS as $sectionKey => $sectionLabel): ?>
      <?php if (!empty($fieldsBySection[$sectionKey])): ?>
        <div>
          <h3 class="text-xs font-extrabold text-slate-400 uppercase tracking-wide mb-4 pb-2 border-b border-slate-100"><?= e($sectionLabel) ?></h3>
          <div class="space-y-5">
            <?php foreach ($fieldsBySection[$sectionKey] as $field): $prev = $prefillByFieldId[$field['id']] ?? ''; ?>
              <?php require __DIR__ . '/partials/apply_field.php'; ?>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    <?php endforeach; ?>

    <div class="pt-4 border-t border-slate-100">
      <button type="submit" class="btn-gold !px-5 !py-2.5 text-sm w-full sm:w-auto flex items-center justify-center gap-2"><i class="fa-solid fa-paper-plane"></i> <?= $isResubmit ? 'Resubmit Application' : 'Submit Application' ?></button>
    </div>
  </form>
</div>
