<?php $viewer = \App\Core\Auth::user(); ?>
<div class="max-w-[1400px] mx-auto px-4 md:px-8 py-8">

  <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-extrabold text-primary-navy">Find a Mentor</h1>
      <p class="text-sm text-slate-500 mt-1">Connect with experienced alumni who can guide and support your growth.</p>
    </div>
    <?php if ($viewer): ?>
      <a href="<?= e(url('mentorship/requests')) ?>" class="text-xs font-semibold text-gold hover:underline flex items-center gap-1">My Requests <i class="bi bi-arrow-right"></i></a>
    <?php endif; ?>
  </div>

  <?php if ($viewer && !empty($viewer['is_mentor'])): ?>
    <div id="mentor-banner" class="card p-4 mb-6 flex flex-wrap items-center gap-3 !bg-indigo-50 !border-indigo-100">
      <div class="w-9 h-9 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-chalkboard-user"></i></div>
      <p class="text-xs text-slate-600 flex-1 min-w-[160px]">You're listed as a mentor. <a href="<?= e(url('mentorship/become')) ?>" class="text-indigo-600 font-semibold hover:underline">Manage your mentor profile &rarr;</a></p>
      <button type="button" onclick="document.getElementById('mentor-banner').remove()" class="text-slate-400 hover:text-slate-600 flex-shrink-0"><i class="fa-solid fa-xmark"></i></button>
    </div>
  <?php elseif ($viewer): ?>
    <div class="card p-4 mb-6 flex flex-wrap items-center gap-3 !bg-indigo-50 !border-indigo-100">
      <div class="w-9 h-9 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-hand-holding-heart"></i></div>
      <p class="text-xs text-slate-600 flex-1 min-w-[160px]">Have expertise to share? Give back to the RBSN community.</p>
      <a href="<?= e(url('mentorship/become')) ?>" class="btn !bg-indigo-600 !text-white hover:!bg-indigo-700 !px-4 !py-2 text-xs whitespace-nowrap">Become a Mentor &rarr;</a>
    </div>
  <?php else: ?>
    <div class="card p-4 mb-6 flex flex-wrap items-center gap-3 !bg-indigo-50 !border-indigo-100">
      <div class="w-9 h-9 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-hand-holding-heart"></i></div>
      <p class="text-xs text-slate-600 flex-1 min-w-[160px]">Have expertise to share? Log in to become a mentor.</p>
      <a href="<?= e(url('login')) ?>" class="btn !bg-indigo-600 !text-white hover:!bg-indigo-700 !px-4 !py-2 text-xs whitespace-nowrap">Log In &rarr;</a>
    </div>
  <?php endif; ?>

  <p class="text-sm text-slate-500 mb-4">Showing <span class="font-semibold text-primary-navy"><?= number_format($totalMentors) ?></span> mentor<?= $totalMentors === 1 ? '' : 's' ?></p>

  <button type="button" id="sidebar-toggle" class="lg:hidden btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !py-2.5 text-sm w-full flex items-center justify-center gap-2 mb-4">
    <i class="bi bi-funnel"></i> <span>Filters</span>
    <?php if ($filters): ?><span class="w-2 h-2 rounded-full bg-gold"></span><?php endif; ?>
    <i class="bi bi-chevron-down transition-transform" id="sidebar-toggle-icon"></i>
  </button>

  <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-6 items-start">

    <!-- Filter sidebar -->
    <aside id="sidebar-collapsible" class="hidden lg:block card p-5 lg:sticky lg:top-[calc(var(--header-height)+1rem)] lg:max-h-[calc(100vh-var(--header-height)-2rem)] lg:overflow-y-auto">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-bold text-primary-navy flex items-center gap-2"><i class="bi bi-funnel text-slate-400"></i> Filter Mentors</h3>
        <?php if ($filters): ?>
          <a href="<?= e(url('mentorship')) ?>" class="text-slate-400 hover:text-gold" title="Clear all"><i class="bi bi-arrow-clockwise"></i></a>
        <?php endif; ?>
      </div>
      <form method="GET" action="<?= e(url('mentorship')) ?>" class="space-y-3">
        <div class="relative">
          <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
          <input type="text" name="q" value="<?= e($filters['q'] ?? '') ?>" placeholder="Search by expertise, industry, or keyword..." class="form-input !pl-9 text-sm">
        </div>
        <div class="relative">
          <i class="bi bi-briefcase absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm z-10"></i>
          <select name="industry" class="form-input !pl-9 text-sm">
            <option value="">Expertise / Industry</option>
            <?php foreach ($industries as $opt): ?>
              <option value="<?= e($opt) ?>" <?= ($filters['industry'] ?? '') === $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="relative">
          <i class="bi bi-bar-chart-steps absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm z-10"></i>
          <select name="career_stage" class="form-input !pl-9 text-sm">
            <option value="">Career Stage</option>
            <?php foreach ($careerStages as $key => $label): ?>
              <option value="<?= e($key) ?>" <?= ($filters['career_stage'] ?? '') === $key ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="relative">
          <i class="bi bi-geo-alt absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm z-10"></i>
          <select name="location" class="form-input !pl-9 text-sm">
            <option value="">Location</option>
            <?php foreach ($locations as $opt): ?>
              <option value="<?= e($opt) ?>" <?= ($filters['location'] ?? '') === $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="relative">
          <i class="bi bi-clock absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm z-10"></i>
          <select name="availability" class="form-input !pl-9 text-sm">
            <option value="">Availability</option>
            <?php foreach ($availabilityOptions as $key => $label): ?>
              <option value="<?= e($key) ?>" <?= ($filters['availability'] ?? '') === $key ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="pt-2 flex items-center gap-3">
          <button type="submit" class="btn-gold !px-4 !py-2 text-xs flex-1"><i class="bi bi-funnel"></i> Apply Filters</button>
          <?php if ($filters): ?>
            <a href="<?= e(url('mentorship')) ?>" class="text-xs text-slate-500 hover:text-gold whitespace-nowrap">Clear all</a>
          <?php endif; ?>
        </div>
      </form>
    </aside>

    <!-- Main -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
      <?php foreach ($mentors as $mentor): ?>
        <div class="card p-5">
          <div class="flex items-start gap-3 mb-3">
            <a href="<?= e(url('alumni/' . $mentor['id'])) ?>"><?= avatar_html($mentor, 'w-12 h-12') ?></a>
            <div class="min-w-0">
              <a href="<?= e(url('alumni/' . $mentor['id'])) ?>" class="text-sm font-bold text-primary-navy hover:text-gold truncate block"><?= e($mentor['name']) ?></a>
              <p class="text-xs text-slate-500 truncate"><?= e($mentor['headline'] ?: '') ?></p>
              <?php if ($mentor['company']): ?><p class="text-xs text-slate-500 truncate"><?= e($mentor['company']) ?></p><?php endif; ?>
            </div>
          </div>
          <?php if ($mentor['mentorship_areas']): ?>
            <div class="mb-3"><?= tag_list_html($mentor['mentorship_areas'], 'badge-gold') ?></div>
          <?php endif; ?>
          <?php if (\App\Core\Auth::check() && (int) \App\Core\Auth::id() !== (int) $mentor['id']): ?>
            <details>
              <summary class="text-xs font-semibold text-gold hover:underline cursor-pointer list-none flex items-center gap-1.5"><i class="bi bi-envelope"></i> Request Mentorship</summary>
              <form method="POST" action="<?= e(url('mentorship/' . $mentor['id'] . '/request')) ?>" class="mt-2 space-y-2">
                <?= csrf_field() ?>
                <input type="text" name="area" placeholder="Area (e.g. Entrepreneurship)" class="form-input text-xs">
                <textarea name="message" rows="2" class="form-textarea text-xs" placeholder="Message (optional)"></textarea>
                <button type="submit" class="btn-gold !px-3 !py-1.5 text-xs w-full">Send Request</button>
              </form>
            </details>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
      <?php if (empty($mentors)): ?>
        <p class="text-sm text-slate-400 col-span-full">No mentors found matching those filters. Check back later, or <a href="<?= e(url('mentorship/become')) ?>" class="text-gold hover:underline">become a mentor yourself</a>.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
  (function () {
    var sidebarToggle = document.getElementById('sidebar-toggle');
    var sidebarIcon = document.getElementById('sidebar-toggle-icon');
    var sidebar = document.getElementById('sidebar-collapsible');
    if (sidebarToggle && sidebar) {
      sidebarToggle.addEventListener('click', function () {
        var isOpen = sidebar.classList.toggle('hidden') === false;
        sidebarIcon.classList.toggle('rotate-180', isOpen);
      });
    }
  })();
</script>
