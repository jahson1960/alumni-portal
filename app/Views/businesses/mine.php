<div class="max-w-[1280px] mx-auto px-4 md:px-8 py-8">
  <div class="section-header">
    <h1 class="section-title text-base">My Businesses</h1>
    <a href="<?= e(url('businesses/create')) ?>" class="section-link">+ Add Business</a>
  </div>

  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ($businesses as $biz): ?>
      <div class="card p-5">
        <div class="flex items-center gap-3 mb-3">
          <?php if (!empty($biz['logo'])): ?>
            <img src="<?= e($biz['logo']) ?>" alt="" class="w-12 h-12 object-contain rounded border border-slate-200 bg-white p-1">
          <?php else: ?>
            <div class="w-12 h-12 rounded bg-slate-100 flex items-center justify-center text-primary-navy font-bold text-sm flex-shrink-0"><?= e(mb_substr($biz['name'], 0, 2)) ?></div>
          <?php endif; ?>
          <div class="min-w-0">
            <a href="<?= e(url('businesses/' . $biz['id'])) ?>" class="text-sm font-bold text-primary-navy hover:text-gold truncate block"><?= e($biz['name']) ?></a>
            <?php if ($biz['category']): ?><p class="text-xs text-slate-500 truncate"><?= e($biz['category']) ?></p><?php endif; ?>
          </div>
        </div>
        <div class="flex gap-3">
          <a href="<?= e(url('businesses/' . $biz['id'] . '/edit')) ?>" class="text-xs text-sky-600 hover:underline">Edit</a>
          <form method="POST" action="<?= e(url('businesses/' . $biz['id'] . '/delete')) ?>" onsubmit="return confirm('Delete this business listing?');">
            <?= csrf_field() ?>
            <button type="submit" class="text-xs text-red-600 hover:underline">Delete</button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
    <?php if (empty($businesses)): ?>
      <p class="text-sm text-slate-400 col-span-full">You haven't listed a business yet.</p>
    <?php endif; ?>
  </div>
</div>
