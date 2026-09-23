<?php
$activeAlerts = array_values(array_filter($alerts, fn ($a) => (int) $a['is_active'] === 1));
$inactiveAlerts = array_values(array_filter($alerts, fn ($a) => (int) $a['is_active'] === 0));
$iconColors = ['bg-sky-100 text-sky-600', 'bg-rose-100 text-rose-600', 'bg-emerald-100 text-emerald-600', 'bg-amber-100 text-amber-600', 'bg-indigo-100 text-indigo-600'];

$renderRow = function (array $alert, int $i) use ($iconColors) {
    $criteria = array_filter([
        $alert['location'] ?? null,
        $alert['job_type'] ?? null,
        $alert['category_name'] ?? null,
    ]);
    ?>
    <div class="card p-4 flex flex-wrap items-center gap-4">
        <div class="w-11 h-11 rounded-lg <?= e($iconColors[$i % count($iconColors)]) ?> flex items-center justify-center flex-shrink-0 text-lg"><i class="fa-solid fa-bell"></i></div>
        <div class="min-w-0 flex-1">
            <h4 class="text-sm font-bold text-primary-navy truncate"><?= e($alert['name']) ?></h4>
            <p class="text-xs text-slate-500 truncate"><?= $criteria ? e(implode(' · ', $criteria)) : 'Any job, any location' ?></p>
            <p class="text-xs text-slate-400 mt-0.5">
                <i class="fa-regular fa-envelope"></i> Email &middot; <?= $alert['frequency'] === 'weekly' ? 'Weekly' : 'Daily' ?> &middot;
                <?php if ((int) $alert['match_count'] > 0): ?>
                    <a href="<?= e(url('jobs') . '?' . $alert['match_query']) ?>" class="text-gold font-semibold hover:underline"><?= (int) $alert['match_count'] ?> matching job<?= (int) $alert['match_count'] === 1 ? '' : 's' ?> &rarr;</a>
                <?php else: ?>
                    <span class="text-slate-400">0 matching jobs</span>
                <?php endif; ?>
            </p>
        </div>
        <div class="flex items-center gap-3 flex-shrink-0">
            <form method="POST" action="<?= e(url('jobs/alerts/' . $alert['id'] . '/toggle')) ?>">
                <?= csrf_field() ?>
                <button type="submit" class="flex items-center gap-2 text-xs font-semibold <?= $alert['is_active'] ? 'text-emerald-600' : 'text-slate-400' ?>">
                    <span class="relative inline-flex h-5 w-9 items-center rounded-full <?= $alert['is_active'] ? 'bg-emerald-500' : 'bg-slate-300' ?>">
                        <span class="inline-block h-4 w-4 transform rounded-full bg-white transition <?= $alert['is_active'] ? 'translate-x-4' : 'translate-x-0.5' ?>"></span>
                    </span>
                    <?= $alert['is_active'] ? 'On' : 'Off' ?>
                </button>
            </form>
            <div class="relative">
                <button type="button" class="kebab-btn text-slate-400 hover:text-primary-navy px-1"><i class="bi bi-three-dots-vertical"></i></button>
                <div class="kebab-menu hidden absolute right-0 top-full mt-1 z-20 bg-white border border-slate-200 rounded-lg shadow-lg w-32 py-1">
                    <a href="<?= e(url('jobs/alerts/' . $alert['id'] . '/edit')) ?>" class="block px-3 py-2 text-xs text-slate-700 hover:bg-slate-50">Edit</a>
                    <form method="POST" action="<?= e(url('jobs/alerts/' . $alert['id'] . '/delete')) ?>" data-confirm="Delete this job alert?">
                        <?= csrf_field() ?>
                        <button type="submit" class="w-full text-left px-3 py-2 text-xs text-red-600 hover:bg-slate-50">Delete</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <?php
};
?>
  <div class="flex justify-end mb-6">
    <a href="<?= e(url('jobs/alerts/create')) ?>" class="btn-gold !px-4 !py-2.5 text-sm">+ Create Alert</a>
  </div>

  <div class="flex flex-nowrap items-center gap-6 mb-5 border-b border-slate-200 overflow-x-auto overflow-y-hidden">
    <button type="button" class="alert-tab-link flex items-center gap-2 text-sm font-semibold pb-2.5 border-b-2 -mb-px whitespace-nowrap text-gold border-gold" data-tab="active">Active Alerts <span class="text-slate-400">(<?= count($activeAlerts) ?>)</span></button>
    <button type="button" class="alert-tab-link flex items-center gap-2 text-sm font-semibold pb-2.5 border-b-2 -mb-px whitespace-nowrap text-slate-500 border-transparent hover:text-primary-navy" data-tab="inactive">Inactive Alerts <span class="text-slate-400">(<?= count($inactiveAlerts) ?>)</span></button>
  </div>

  <div id="alerts-active" class="space-y-3">
    <?php foreach ($activeAlerts as $i => $alert): $renderRow($alert, $i); ?><?php endforeach; ?>
    <?php if (empty($activeAlerts)): ?>
      <p class="text-sm text-slate-400">No active alerts. <a href="<?= e(url('jobs/alerts/create')) ?>" class="text-gold hover:underline">Create one</a> to get started.</p>
    <?php endif; ?>
  </div>

  <div id="alerts-inactive" class="space-y-3 hidden">
    <?php foreach ($inactiveAlerts as $i => $alert): $renderRow($alert, $i); ?><?php endforeach; ?>
    <?php if (empty($inactiveAlerts)): ?>
      <p class="text-sm text-slate-400">No inactive alerts.</p>
    <?php endif; ?>
  </div>

  <?php if (!empty($alerts)): ?>
    <div class="card p-3 mt-6 flex items-start gap-2.5 !bg-slate-50">
      <i class="fa-solid fa-circle-info text-slate-400 mt-0.5"></i>
      <p class="text-xs text-slate-500">Matching job counts update every time you visit this page based on your saved criteria. Edit any alert to update your criteria.</p>
    </div>
  <?php endif; ?>

<script>
  (function () {
    var tabLinks = document.querySelectorAll('.alert-tab-link');
    var panels = { active: document.getElementById('alerts-active'), inactive: document.getElementById('alerts-inactive') };

    tabLinks.forEach(function (btn) {
      btn.addEventListener('click', function () {
        var tab = btn.dataset.tab;
        tabLinks.forEach(function (b) {
          var isActive = b === btn;
          b.classList.toggle('text-gold', isActive);
          b.classList.toggle('border-gold', isActive);
          b.classList.toggle('text-slate-500', !isActive);
          b.classList.toggle('border-transparent', !isActive);
        });
        Object.keys(panels).forEach(function (key) { panels[key].classList.toggle('hidden', key !== tab); });
      });
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
