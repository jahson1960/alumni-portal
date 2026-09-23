<div class="max-w-3xl mx-auto px-4 md:px-8 py-8">
  <a href="<?= e($backUrl) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> <?= e($backLabel) ?></a>

  <div class="card p-6 md:p-8">
    <div class="flex items-center gap-4 mb-4">
      <?php if (!empty($business['logo'])): ?>
        <img src="<?= e($business['logo']) ?>" alt="" class="w-16 h-16 object-contain rounded border border-slate-200 bg-white p-1">
      <?php else: ?>
        <div class="w-16 h-16 rounded bg-slate-100 flex items-center justify-center text-primary-navy font-bold"><?= e(mb_substr($business['name'], 0, 2)) ?></div>
      <?php endif; ?>
      <div>
        <h1 class="text-xl font-extrabold text-primary-navy"><?= e($business['name']) ?></h1>
        <p class="text-sm text-slate-500">Founded by <a href="<?= e(url('alumni/' . $business['owner_id'])) ?>" class="text-sky-600 hover:underline"><?= e($business['owner_name']) ?></a></p>
      </div>
    </div>

    <div class="flex flex-wrap gap-3 mb-4">
      <?php if ($business['category']): ?><span class="badge-blue"><?= e($business['category']) ?></span><?php endif; ?>
      <?php if ($business['location']): ?><span class="text-xs text-slate-500"><i class="fa-solid fa-location-dot"></i> <?= e($business['location']) ?></span><?php endif; ?>
      <?php if ($business['website']): ?><a href="<?= e($business['website']) ?>" target="_blank" rel="noopener" class="text-xs text-sky-600 hover:underline"><i class="fa-solid fa-globe"></i> Website</a><?php endif; ?>
    </div>

    <?php if ($business['description']): ?>
      <p class="text-sm text-slate-600 whitespace-pre-line"><?= e($business['description']) ?></p>
    <?php endif; ?>
  </div>
</div>
