<div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
  <div class="card p-5">
    <div class="text-2xl font-extrabold text-primary-navy"><?= e($counts['alumni']) ?></div>
    <div class="text-xs text-slate-500 mt-1">Active Alumni</div>
  </div>
  <div class="card p-5">
    <div class="text-2xl font-extrabold text-primary-navy"><?= e($counts['jobs']) ?></div>
    <div class="text-xs text-slate-500 mt-1">Open Jobs</div>
  </div>
  <div class="card p-5">
    <div class="text-2xl font-extrabold text-primary-navy"><?= e($counts['events']) ?></div>
    <div class="text-xs text-slate-500 mt-1">Events</div>
  </div>
  <div class="card p-5">
    <div class="text-2xl font-extrabold text-primary-navy"><?= e($counts['news']) ?></div>
    <div class="text-xs text-slate-500 mt-1">News Posts</div>
  </div>
  <div class="card p-5">
    <div class="text-2xl font-extrabold text-primary-navy"><?= e($counts['resources']) ?></div>
    <div class="text-xs text-slate-500 mt-1">Resources</div>
  </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
  <div class="card p-6">
    <h3 class="section-title text-xs mb-4">Quick Actions</h3>
    <div class="grid grid-cols-2 gap-3">
      <a href="<?= e(url('admin/news/create')) ?>" class="btn-dark !mt-0 !w-auto text-center px-4 py-2 text-xs">+ News Post</a>
      <a href="<?= e(url('admin/jobs/create')) ?>" class="btn-dark !mt-0 !w-auto text-center px-4 py-2 text-xs">+ Job Listing</a>
      <a href="<?= e(url('admin/events/create')) ?>" class="btn-dark !mt-0 !w-auto text-center px-4 py-2 text-xs">+ Event</a>
      <a href="<?= e(url('admin/settings')) ?>" class="btn-dark !mt-0 !w-auto text-center px-4 py-2 text-xs">Edit Homepage</a>
    </div>
  </div>

  <div class="card p-6">
    <h3 class="section-title text-xs mb-4">Recently Joined Alumni</h3>
    <div class="space-y-3">
      <?php foreach ($recentAlumni as $alum): ?>
        <a href="<?= e(url('admin/alumni/' . $alum['id'])) ?>" class="flex items-center gap-3">
          <?= avatar_html($alum, 'w-8 h-8') ?>
          <div class="min-w-0">
            <div class="text-xs font-semibold text-primary-navy truncate"><?= e($alum['name']) ?></div>
            <div class="text-[0.68rem] text-slate-400"><?= e($alum['email']) ?></div>
          </div>
        </a>
      <?php endforeach; ?>
      <?php if (empty($recentAlumni)): ?>
        <p class="text-xs text-slate-400">No alumni yet.</p>
      <?php endif; ?>
    </div>
  </div>
</div>
