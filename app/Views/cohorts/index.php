<div class="max-w-[1280px] mx-auto px-4 md:px-8 py-8">
  <div class="section-header">
    <h1 class="section-title text-base">Class & Cohort Communities</h1>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ($cohorts as $cohort): ?>
      <a href="<?= e(url('cohorts/' . urlencode($cohort['program']) . '/' . $cohort['graduation_year'])) ?>" class="card p-5">
        <h4 class="text-sm font-bold text-primary-navy"><?= e($cohort['program']) ?> <?= e($cohort['graduation_year']) ?></h4>
        <p class="text-xs text-slate-500 mt-1"><?= (int) $cohort['member_count'] ?> alumnus<?= (int) $cohort['member_count'] === 1 ? '' : 'es' ?></p>
      </a>
    <?php endforeach; ?>
    <?php if (empty($cohorts)): ?>
      <p class="text-sm text-slate-400 col-span-full">No cohorts to show yet.</p>
    <?php endif; ?>
  </div>
</div>
