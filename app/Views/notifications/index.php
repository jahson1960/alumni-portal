<?php $hasUnread = $unreadCounts[''] > 0; ?>
<div class="max-w-[1100px] mx-auto px-4 md:px-8 py-8">

  <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-extrabold text-primary-navy">Notifications</h1>
      <p class="text-sm text-slate-500 mt-1">Stay updated with important activity and updates.</p>
    </div>
    <?php if ($hasUnread): ?>
      <form method="POST" action="<?= e(url('notifications/mark-all-read')) ?>">
        <?= csrf_field() ?>
        <button type="submit" class="text-xs font-semibold text-gold hover:underline flex items-center gap-1.5"><i class="bi bi-check2-all"></i> Mark all as read</button>
      </form>
    <?php endif; ?>
  </div>

  <button type="button" id="sidebar-toggle" class="lg:hidden btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !py-2.5 text-sm w-full flex items-center justify-center gap-2 mb-4">
    <i class="bi bi-list"></i> <span>Categories</span>
    <?php if ($unreadCounts[''] > 0): ?><span class="bg-gold text-white text-[10px] font-bold rounded-full w-5 h-5 flex items-center justify-center"><?= $unreadCounts[''] ?></span><?php endif; ?>
    <i class="bi bi-chevron-down transition-transform" id="sidebar-toggle-icon"></i>
  </button>

  <div class="grid grid-cols-1 lg:grid-cols-[240px_1fr] gap-6 items-start">

    <!-- Sidebar -->
    <aside id="sidebar-collapsible" class="hidden lg:block card p-2 lg:sticky lg:top-[calc(var(--header-height)+1rem)] lg:max-h-[calc(100vh-var(--header-height)-2rem)] lg:overflow-y-auto">
      <?php foreach ($categories as $key => $cat): ?>
        <button type="button" class="notif-tab-link w-full flex items-center justify-between gap-2 px-3 py-2.5 rounded-lg text-sm font-semibold <?= $key === '' ? 'bg-gold/10 text-gold' : 'text-slate-600 hover:bg-slate-50' ?>" data-category="<?= e($key) ?>">
          <span class="flex items-center gap-2.5"><i class="bi <?= e($cat['icon']) ?>"></i> <?= e($cat['label']) ?></span>
          <?php if ($unreadCounts[$key] > 0): ?>
            <span class="bg-gold text-white text-[10px] font-bold rounded-full w-5 h-5 flex items-center justify-center flex-shrink-0"><?= $unreadCounts[$key] ?></span>
          <?php endif; ?>
        </button>
      <?php endforeach; ?>
    </aside>

    <!-- List -->
    <div class="card overflow-hidden">
      <?php foreach ($notifications as $n): ?>
        <a href="<?= e(url($n['link'] ?: '/')) ?>"
           class="notif-row flex items-center gap-3 px-4 py-3.5 border-b border-slate-100 last:border-0 <?= !$n['is_read'] ? 'bg-gold/5' : 'hover:bg-slate-50' ?>"
           data-category="<?= e(notification_category($n['type'])) ?>">
          <div class="w-9 h-9 rounded-full bg-slate-100 text-primary-navy flex items-center justify-center flex-shrink-0"><i class="bi <?= e(notification_icon($n['type'])) ?> text-sm"></i></div>
          <div class="min-w-0 flex-1">
            <p class="text-sm text-slate-700 <?= !$n['is_read'] ? 'font-semibold' : '' ?>"><?= e($n['message']) ?></p>
            <p class="text-[0.68rem] text-slate-400 mt-0.5"><?= e(time_ago($n['created_at'])) ?></p>
          </div>
          <?php if (!$n['is_read']): ?>
            <span class="w-2 h-2 rounded-full bg-gold flex-shrink-0"></span>
          <?php endif; ?>
          <i class="bi bi-chevron-right text-slate-300 flex-shrink-0"></i>
        </a>
      <?php endforeach; ?>
      <p id="notifications-empty" class="text-sm text-slate-400 text-center px-4 py-8 <?= empty($notifications) ? '' : 'hidden' ?>">No notifications yet.</p>
      <p id="notifications-category-empty" class="hidden text-sm text-slate-400 text-center px-4 py-8">Nothing here yet.</p>
    </div>
  </div>
</div>

<script>
  (function () {
    var tabLinks = document.querySelectorAll('.notif-tab-link');
    var rows = document.querySelectorAll('.notif-row');
    var categoryEmpty = document.getElementById('notifications-category-empty');
    var sidebarToggle = document.getElementById('sidebar-toggle');
    var sidebarIcon = document.getElementById('sidebar-toggle-icon');
    var sidebar = document.getElementById('sidebar-collapsible');

    function activate(category) {
      tabLinks.forEach(function (b) {
        var isActive = b.dataset.category === category;
        b.classList.toggle('bg-gold/10', isActive);
        b.classList.toggle('text-gold', isActive);
        b.classList.toggle('text-slate-600', !isActive);
      });

      var visibleCount = 0;
      rows.forEach(function (row) {
        var show = category === '' || row.dataset.category === category;
        row.classList.toggle('hidden', !show);
        if (show) visibleCount++;
      });
      categoryEmpty.classList.toggle('hidden', visibleCount > 0 || rows.length === 0);
    }

    tabLinks.forEach(function (btn) {
      btn.addEventListener('click', function () {
        activate(btn.dataset.category);
        if (sidebar && window.innerWidth < 1024) {
          sidebar.classList.add('hidden');
          if (sidebarIcon) sidebarIcon.classList.remove('rotate-180');
        }
      });
    });

    if (sidebarToggle && sidebar) {
      sidebarToggle.addEventListener('click', function () {
        var isOpen = sidebar.classList.toggle('hidden') === false;
        sidebarIcon.classList.toggle('rotate-180', isOpen);
      });
    }
  })();
</script>
