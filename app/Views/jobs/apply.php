<?php
$inputTypes = ['text' => 'text', 'email' => 'email', 'phone' => 'tel', 'number' => 'number', 'url' => 'url'];
$sectionMeta = [
    'profile_info' => ['icon' => 'fa-regular fa-id-badge', 'blurb' => 'The basics, so the employer knows who you are.'],
    'education' => ['icon' => 'fa-solid fa-graduation-cap', 'blurb' => 'Pulled straight from your profile.'],
    'experience' => ['icon' => 'fa-solid fa-briefcase', 'blurb' => 'Pulled straight from your profile.'],
    'additional_info' => ['icon' => 'fa-solid fa-sliders', 'blurb' => 'A few extra questions from the employer.'],
];
$visibleSections = array_filter(
    array_keys(\App\Models\ApplicationTemplate::SECTIONS),
    fn ($key) => !empty($fieldsBySection[$key])
);
$sectionNumber = 0;
?>
<div class="max-w-[720px] mx-auto px-4 md:px-8 py-8 md:py-10">
  <a href="<?= e(url('jobs/' . $job['id'])) ?>" class="text-sm text-slate-500 hover:text-gold mb-5 inline-flex items-center gap-2 font-medium"><i class="fa-solid fa-arrow-left"></i> Back to Job</a>

  <div class="relative rounded-3xl overflow-hidden mb-6 !border-0 bg-gradient-to-br from-primary-navy to-slate-900 text-white p-6 md:p-7">
    <div class="absolute -right-10 -top-10 w-40 h-40 rounded-full bg-white/5"></div>
    <div class="absolute -right-4 bottom-0 w-24 h-24 rounded-full bg-gold/10"></div>
    <div class="relative flex items-center gap-4">
      <div class="w-14 h-14 rounded-2xl bg-white p-1.5 flex-shrink-0 shadow-lg"><?= job_logo_html($job, 'w-full h-full') ?></div>
      <div class="min-w-0">
        <p class="text-[0.65rem] font-bold text-gold uppercase tracking-wide mb-0.5">You're applying for</p>
        <h1 class="text-lg md:text-xl font-extrabold truncate"><?= e($job['title']) ?></h1>
        <p class="text-sm text-slate-300 truncate"><?= e($job['company']) ?><?= $job['location'] ? ' &bull; ' . e($job['location']) : '' ?></p>
      </div>
    </div>
  </div>

  <?php require dirname(__DIR__) . '/partials/errors.php'; ?>

  <?php if ($isResubmit): ?>
    <div class="rounded-2xl bg-sky-50 border border-sky-100 p-4 mb-6 flex items-start gap-3">
      <div class="w-8 h-8 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-rotate text-xs"></i></div>
      <p class="text-xs text-sky-800 pt-1.5">You've already applied to this job. Submitting again replaces your previous answers.</p>
    </div>
  <?php endif; ?>

  <p class="text-sm text-slate-500 mb-5">Just a few details and you're all set — most of this is already filled in from your profile.</p>

  <form method="POST" action="<?= e(url('jobs/' . $job['id'] . '/apply')) ?>" enctype="multipart/form-data" class="space-y-5">
    <?= csrf_field() ?>

    <?php if (!$visibleSections): ?>
      <div class="rounded-2xl border border-dashed border-slate-200 p-8 text-center">
        <div class="w-12 h-12 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center text-xl mx-auto mb-3"><i class="fa-solid fa-check"></i></div>
        <p class="text-sm text-slate-500">This job doesn't have any additional questions — just submit to apply.</p>
      </div>
    <?php endif; ?>

    <?php foreach (\App\Models\ApplicationTemplate::SECTIONS as $sectionKey => $sectionLabel): ?>
      <?php if (!empty($fieldsBySection[$sectionKey])): $sectionNumber++; $meta = $sectionMeta[$sectionKey] ?? ['icon' => 'fa-regular fa-circle', 'blurb' => '']; ?>
        <div class="card !rounded-2xl !shadow-sm !border-slate-100 p-5 md:p-6">
          <div class="flex items-start gap-3 mb-5">
            <span class="w-9 h-9 rounded-xl bg-gold/10 text-gold flex items-center justify-center flex-shrink-0 text-sm"><i class="<?= e($meta['icon']) ?>"></i></span>
            <div class="min-w-0">
              <h3 class="text-sm font-bold text-primary-navy"><?= e($sectionLabel) ?></h3>
              <?php if ($meta['blurb']): ?><p class="text-xs text-slate-400 mt-0.5"><?= e($meta['blurb']) ?></p><?php endif; ?>
            </div>
            <span class="ml-auto text-[0.65rem] font-bold text-slate-300 flex-shrink-0">0<?= $sectionNumber ?></span>
          </div>
          <div class="space-y-5">
            <?php foreach ($fieldsBySection[$sectionKey] as $field): $prev = $prefillByFieldId[$field['id']] ?? ''; ?>
              <?php require __DIR__ . '/partials/apply_field.php'; ?>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>
    <?php endforeach; ?>

    <div class="card !rounded-2xl !shadow-sm !border-slate-100 p-5 md:p-6 flex flex-col sm:flex-row items-center gap-4">
      <div class="flex items-center gap-2.5 text-xs text-slate-400 flex-1">
        <i class="fa-solid fa-lock text-slate-300"></i>
        <span>Your answers are only shared with <?= e($job['company']) ?>.</span>
      </div>
      <button type="submit" class="btn-gold !rounded-full !px-7 !py-3 text-sm w-full sm:w-auto flex items-center justify-center gap-2 flex-shrink-0 shadow-lg shadow-gold"><i class="fa-solid fa-paper-plane"></i> <?= $isResubmit ? 'Resubmit Application' : 'Submit Application' ?></button>
    </div>
  </form>
</div>
