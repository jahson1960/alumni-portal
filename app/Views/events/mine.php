<div class="max-w-[1100px] mx-auto px-4 md:px-8 py-8">
  <h1 class="text-2xl font-extrabold text-primary-navy">My Events</h1>
  <p class="text-sm text-slate-500 mt-1 mb-6">Manage your event registrations, saved events, and view your event history.</p>

  <div class="card !p-0 mb-6">
    <div class="flex flex-wrap items-center justify-between gap-3 px-5 border-b border-slate-100">
      <div class="flex items-center gap-6 overflow-x-auto overflow-y-hidden min-w-0">
        <?php if (\App\Models\PageTabVisibility::isVisible('events_mine', 'registrations')): ?>
          <a href="<?= e(url('events/mine')) ?>" class="flex items-center gap-2 text-sm font-semibold py-4 border-b-2 -mb-px whitespace-nowrap <?= $tab === 'registrations' ? 'text-gold border-gold' : 'text-slate-500 border-transparent hover:text-primary-navy' ?>"><i class="fa-solid fa-tag"></i> My Registrations (<?= $registrationsCount ?>)</a>
        <?php endif; ?>
        <?php if (\App\Models\PageTabVisibility::isVisible('events_mine', 'saved')): ?>
          <a href="<?= e(url('events/mine') . '?tab=saved') ?>" class="flex items-center gap-2 text-sm font-semibold py-4 border-b-2 -mb-px whitespace-nowrap <?= $tab === 'saved' ? 'text-gold border-gold' : 'text-slate-500 border-transparent hover:text-primary-navy' ?>"><i class="bi bi-bookmark"></i> Saved Events (<?= $savedCount ?>)</a>
        <?php endif; ?>
        <?php if (\App\Models\PageTabVisibility::isVisible('events_mine', 'history')): ?>
          <a href="<?= e(url('events/mine') . '?tab=history') ?>" class="flex items-center gap-2 text-sm font-semibold py-4 border-b-2 -mb-px whitespace-nowrap <?= $tab === 'history' ? 'text-gold border-gold' : 'text-slate-500 border-transparent hover:text-primary-navy' ?>"><i class="fa-regular fa-circle-check"></i> Event History (<?= $historyCount ?>)</a>
        <?php endif; ?>
      </div>
      <a href="<?= e(url('events')) ?>" class="btn bg-white border border-gold !text-gold hover:bg-gold hover:!text-white !px-4 !py-2 text-xs flex items-center gap-2 flex-shrink-0 my-2">Browse All Events <i class="fa-solid fa-arrow-right"></i></a>
    </div>
  </div>

  <?php if ($tab === 'saved'): ?>
    <?php require __DIR__ . '/mine/saved.php'; ?>
  <?php elseif ($tab === 'history'): ?>
    <?php require __DIR__ . '/mine/history.php'; ?>
  <?php else: ?>
    <?php require __DIR__ . '/mine/registrations.php'; ?>
  <?php endif; ?>

  <div class="card !bg-amber-50 !border-amber-100 p-5 mt-6 flex flex-wrap items-center justify-between gap-4">
    <div class="flex items-center gap-3">
      <div class="w-10 h-10 rounded-lg bg-amber-100 text-gold flex items-center justify-center flex-shrink-0"><i class="fa-regular fa-calendar"></i></div>
      <div>
        <p class="text-sm font-bold text-primary-navy">Can't find an event?</p>
        <p class="text-xs text-slate-500">Explore all upcoming events and opportunities to connect with the alumni community.</p>
      </div>
    </div>
    <a href="<?= e(url('events')) ?>" class="btn-gold !px-5 !py-2.5 text-sm flex items-center gap-2 flex-shrink-0">Browse All Events <i class="fa-solid fa-arrow-right"></i></a>
  </div>
</div>

<script>
  (function () {
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
