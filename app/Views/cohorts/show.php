<div class="max-w-[1280px] mx-auto px-4 md:px-8 py-8">
  <a href="<?= e(url('cohorts')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to Cohorts</a>

  <div class="card p-6 mb-6">
    <h1 class="text-xl font-extrabold text-primary-navy mb-1"><?= e($program) ?> <?= e($year) ?></h1>
    <div class="flex flex-wrap gap-6 mt-3 text-sm">
      <div><span class="text-lg font-extrabold text-primary-navy"><?= (int) $stats['members'] ?></span> <span class="text-xs text-slate-500">Alumni</span></div>
      <div><span class="text-lg font-extrabold text-primary-navy"><?= (int) $stats['countries'] ?></span> <span class="text-xs text-slate-500">Countries</span></div>
      <div><span class="text-lg font-extrabold text-primary-navy"><?= (int) $stats['companies'] ?></span> <span class="text-xs text-slate-500">Companies</span></div>
    </div>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ($members as $person): ?>
      <a href="<?= e(url('alumni/' . $person['id'])) ?>" class="card p-5 flex items-start gap-4">
        <?= avatar_html($person, 'w-12 h-12') ?>
        <div class="min-w-0">
          <h4 class="text-sm font-bold text-primary-navy truncate"><?= e($person['name']) ?></h4>
          <p class="text-xs text-slate-500 truncate"><?= e($person['headline'] ?: '') ?></p>
          <?php if ($person['company']): ?><p class="text-xs text-slate-500 truncate"><?= e($person['company']) ?></p><?php endif; ?>
          <?php if (location_display($person)): ?><p class="text-[0.68rem] text-slate-400 mt-1"><i class="fa-solid fa-location-dot"></i> <?= e(location_display($person)) ?></p><?php endif; ?>
        </div>
      </a>
    <?php endforeach; ?>
  </div>
</div>
