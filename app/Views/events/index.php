<?php
$viewTabs = array_filter([
    'all' => 'All Events',
    'global' => 'Global Events',
    'reunions' => 'Reunions',
    'executive' => 'Executive Programmes',
    'webinars' => 'Webinars',
    'featured' => 'Featured Events',
], fn ($label, $key) => \App\Models\PageTabVisibility::isVisible('events', $key), ARRAY_FILTER_USE_BOTH);
$qs = function (array $overrides) use ($view, $format, $q, $display) {
    $params = array_merge(['view' => $view, 'format' => $format, 'q' => $q, 'display' => $display], $overrides);
    $params = array_filter($params, fn ($v) => $v !== null && $v !== '');
    return '?' . http_build_query($params);
};
?>
<div class="max-w-[1280px] mx-auto px-4 md:px-8 py-8">

  <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-extrabold text-primary-navy">Upcoming Events</h1>
      <p class="text-sm text-slate-500 mt-1">Connect, learn and grow with fellow alumni through our curated events and experiences.</p>
    </div>
    <?php if (\App\Core\Auth::isAdmin()): ?>
      <a href="<?= e(url('admin/events/create')) ?>" class="btn-gold !px-4 !py-2.5 text-sm flex items-center gap-2 flex-shrink-0"><i class="fa-solid fa-plus"></i> Create Event</a>
    <?php endif; ?>
  </div>

  <div class="flex items-center gap-5 overflow-x-auto overflow-y-hidden min-w-0 mb-6 border-b border-slate-200">
    <?php foreach ($viewTabs as $key => $label): ?>
      <a href="<?= e(url('events') . $qs(['view' => $key === 'all' ? null : $key, 'month' => null, 'per_page' => null])) ?>" class="text-sm font-semibold pb-2.5 border-b-2 -mb-px whitespace-nowrap <?= $view === $key ? 'text-gold border-gold' : 'text-slate-500 border-transparent hover:text-primary-navy' ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
  </div>

  <div class="flex flex-wrap items-center justify-between gap-3 mb-6">
    <form method="GET" action="<?= e(url('events')) ?>" class="flex-1 min-w-[220px] max-w-sm">
      <input type="hidden" name="view" value="<?= e($view) ?>">
      <?php if ($format): ?><input type="hidden" name="format" value="<?= e($format) ?>"><?php endif; ?>
      <div class="relative">
        <input type="text" name="q" value="<?= e($q) ?>" placeholder="Search events..." class="form-input !pl-9 text-sm">
        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
      </div>
    </form>

    <div class="flex items-center gap-2 flex-shrink-0">
      <div class="relative">
        <button type="button" id="events-filters-btn" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-4 !py-2.5 text-xs flex items-center gap-2">
          <i class="fa-solid fa-sliders"></i> Filters <?php if ($format): ?><span class="w-1.5 h-1.5 rounded-full bg-gold"></span><?php endif; ?>
        </button>
        <div id="events-filters-panel" class="hidden absolute right-0 top-full mt-2 w-44 bg-white rounded-lg shadow-lg border border-slate-100 py-2 z-30">
          <a href="<?= e(url('events') . $qs(['format' => null, 'month' => null, 'per_page' => null])) ?>" class="block px-4 py-2 text-sm <?= $format === '' ? 'text-gold font-semibold' : 'text-slate-600 hover:bg-slate-50' ?>">All Formats</a>
          <a href="<?= e(url('events') . $qs(['format' => 'in-person', 'month' => null, 'per_page' => null])) ?>" class="block px-4 py-2 text-sm <?= $format === 'in-person' ? 'text-gold font-semibold' : 'text-slate-600 hover:bg-slate-50' ?>">In-Person</a>
          <a href="<?= e(url('events') . $qs(['format' => 'virtual', 'month' => null, 'per_page' => null])) ?>" class="block px-4 py-2 text-sm <?= $format === 'virtual' ? 'text-gold font-semibold' : 'text-slate-600 hover:bg-slate-50' ?>">Virtual</a>
        </div>
      </div>

      <a href="<?= e(url('events') . $qs(['display' => $display === 'calendar' ? null : 'calendar', 'month' => null, 'per_page' => null])) ?>" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-4 !py-2.5 text-xs flex items-center gap-2 whitespace-nowrap">
        <?php if ($display === 'calendar'): ?><i class="bi bi-grid-3x3-gap"></i> Grid View<?php else: ?><i class="fa-regular fa-calendar"></i> Calendar View<?php endif; ?>
      </a>
    </div>
  </div>

  <?php if ($display === 'calendar'): ?>
    <?php require __DIR__ . '/partials/calendar.php'; ?>
  <?php else: ?>
    <?php require __DIR__ . '/partials/grid.php'; ?>
  <?php endif; ?>

</div>

<script>
  (function () {
    var btn = document.getElementById('events-filters-btn');
    var panel = document.getElementById('events-filters-panel');
    if (!btn || !panel) return;

    btn.addEventListener('click', function (e) {
      e.stopPropagation();
      panel.classList.toggle('hidden');
    });
    document.addEventListener('click', function (e) {
      if (!panel.contains(e.target) && e.target !== btn) panel.classList.add('hidden');
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape') panel.classList.add('hidden');
    });
  })();
</script>
