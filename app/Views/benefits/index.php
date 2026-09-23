<div class="max-w-[1280px] mx-auto px-4 md:px-8 py-8">
  <div class="section-header">
    <h1 class="section-title text-base">Alumni Benefits</h1>
  </div>

  <?php foreach ($grouped as $category => $items): ?>
    <div class="mb-8">
      <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-3"><?= e($category) ?></h3>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($items as $benefit): ?>
          <a href="<?= e($benefit['link'] ?: '#') ?>" class="card p-5 flex items-start gap-3">
            <div class="w-10 h-10 rounded-lg bg-slate-100 text-primary-navy flex items-center justify-center text-lg flex-shrink-0"><i class="fa-solid fa-gift"></i></div>
            <div class="min-w-0">
              <h4 class="text-sm font-bold text-primary-navy"><?= e($benefit['title']) ?></h4>
              <p class="text-xs text-slate-500"><?= e($benefit['description']) ?></p>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  <?php endforeach; ?>

  <?php if (empty($grouped)): ?>
    <p class="text-sm text-slate-400">No benefits listed yet.</p>
  <?php endif; ?>
</div>
