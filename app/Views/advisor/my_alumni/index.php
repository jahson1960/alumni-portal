<div class="max-w-[1280px] mx-auto px-4 md:px-8 py-8">
  <div class="section-header">
    <h1 class="section-title text-base">My Assigned Alumni</h1>
  </div>
  <p class="text-sm text-slate-500 mb-6"><?= count($alumni) ?> alumni assigned to you.</p>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ($alumni as $alum): ?>
      <a href="<?= e(url('advisor/my-alumni/' . $alum['id'])) ?>" class="card p-5 block hover:shadow-md transition-shadow">
        <div class="flex items-center gap-3 mb-2">
          <?= avatar_html($alum, 'w-10 h-10') ?>
          <div class="min-w-0">
            <p class="text-sm font-bold text-primary-navy truncate"><?= e($alum['name']) ?></p>
            <p class="text-xs text-slate-500 truncate"><?= e($alum['headline'] ?: ($alum['company'] ?: '')) ?></p>
          </div>
        </div>
        <p class="text-xs text-slate-400"><?= e(location_display($alum) ?: '—') ?></p>
      </a>
    <?php endforeach; ?>
    <?php if (empty($alumni)): ?>
      <p class="text-sm text-slate-400 col-span-full">No alumni have been assigned to you yet.</p>
    <?php endif; ?>
  </div>
</div>
