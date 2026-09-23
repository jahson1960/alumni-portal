<?php $appliedCount = count(array_intersect(array_column($jobs, 'id'), $appliedIds)); ?>
  <div class="flex flex-nowrap items-center gap-6 mb-5 border-b border-slate-200 overflow-x-auto overflow-y-hidden">
    <button type="button" class="saved-tab-link flex items-center gap-2 text-sm font-semibold pb-2.5 border-b-2 -mb-px whitespace-nowrap text-gold border-gold" data-tab="all">All Saved <span class="text-slate-400">(<?= count($jobs) ?>)</span></button>
    <button type="button" class="saved-tab-link flex items-center gap-2 text-sm font-semibold pb-2.5 border-b-2 -mb-px whitespace-nowrap text-slate-500 border-transparent hover:text-primary-navy" data-tab="applied">Applied <span class="text-slate-400">(<?= $appliedCount ?>)</span></button>
  </div>

  <div class="space-y-3">
    <?php foreach ($jobs as $job): $hasApplied = in_array((int) $job['id'], $appliedIds, true); $jobCats = \App\Models\Category::forJob((int) $job['id']); ?>
      <div class="saved-job-row card p-4 flex flex-wrap items-center gap-4" data-applied="<?= $hasApplied ? '1' : '0' ?>">
        <a href="<?= e(url('jobs/' . $job['id'])) ?>" class="flex-shrink-0"><?= job_logo_html($job, 'w-11 h-11') ?></a>
        <div class="min-w-0 flex-1">
          <a href="<?= e(url('jobs/' . $job['id'])) ?>" class="text-sm font-bold text-primary-navy hover:text-gold truncate block"><?= e($job['title']) ?></a>
          <p class="text-xs text-slate-500 truncate"><?= e($job['company']) ?></p>
          <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
            <?php if ($jobCats): ?><?= category_badges_html($jobCats) ?><?php endif; ?>
            <span class="badge bg-slate-100 text-slate-600"><?= e($job['job_type']) ?></span>
          </div>
        </div>
        <div class="text-xs text-slate-500 flex-shrink-0">
          <p><i class="fa-solid fa-location-dot"></i> <?= e($job['location']) ?></p>
          <p class="text-slate-400 mt-0.5">Saved <?= e(time_ago($job['saved_at'])) ?></p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
          <?php if ($hasApplied): ?>
            <span class="badge-green">Applied</span>
          <?php else: ?>
            <a href="<?= e(url('jobs/' . $job['id'])) ?>" class="btn-gold !px-4 !py-2 text-xs">Apply Now</a>
          <?php endif; ?>
          <div class="relative">
            <button type="button" class="kebab-btn text-slate-400 hover:text-primary-navy px-1"><i class="bi bi-three-dots-vertical"></i></button>
            <div class="kebab-menu hidden absolute right-0 top-full mt-1 z-20 bg-white border border-slate-200 rounded-lg shadow-lg w-36 py-1">
              <a href="<?= e(url('jobs/' . $job['id'])) ?>" class="block px-3 py-2 text-xs text-slate-700 hover:bg-slate-50">View Job</a>
              <form method="POST" action="<?= e(url('jobs/' . $job['id'] . '/save')) ?>">
                <?= csrf_field() ?>
                <button type="submit" class="w-full text-left px-3 py-2 text-xs text-red-600 hover:bg-slate-50">Remove</button>
              </form>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
    <?php if (empty($jobs)): ?>
      <p class="text-sm text-slate-400">You haven't saved any jobs yet. Use the bookmark icon on a job card to save it here.</p>
    <?php endif; ?>
    <p id="saved-applied-empty" class="hidden text-sm text-slate-400">You haven't applied to any of your saved jobs yet.</p>
  </div>

<script>
  (function () {
    var tabLinks = document.querySelectorAll('.saved-tab-link');
    var rows = document.querySelectorAll('.saved-job-row');
    var appliedEmpty = document.getElementById('saved-applied-empty');

    function activate(tab) {
      tabLinks.forEach(function (b) {
        var isActive = b.dataset.tab === tab;
        b.classList.toggle('text-gold', isActive);
        b.classList.toggle('border-gold', isActive);
        b.classList.toggle('text-slate-500', !isActive);
        b.classList.toggle('border-transparent', !isActive);
      });
      var visibleCount = 0;
      rows.forEach(function (row) {
        var show = tab === 'all' || row.dataset.applied === '1';
        row.classList.toggle('hidden', !show);
        if (show) visibleCount++;
      });
      appliedEmpty.classList.toggle('hidden', tab === 'all' || visibleCount > 0);
    }

    tabLinks.forEach(function (btn) {
      btn.addEventListener('click', function () { activate(btn.dataset.tab); });
    });

    document.querySelectorAll('.kebab-btn').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.stopPropagation();
        var menu = btn.nextElementSibling;
        document.querySelectorAll('.kebab-menu').forEach(function (m) { if (m !== menu) m.classList.add('hidden'); });
        menu.classList.toggle('hidden');
      });
    });
    document.addEventListener('click', function () {
      document.querySelectorAll('.kebab-menu').forEach(function (m) { m.classList.add('hidden'); });
    });
  })();
</script>
