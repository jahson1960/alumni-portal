<a href="<?= e(url('admin/alumni')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to Alumni</a>

<div class="card p-6 max-w-2xl">
  <div class="flex items-center gap-4 mb-6">
    <?= avatar_html($alum, 'w-16 h-16') ?>
    <div>
      <h2 class="text-lg font-extrabold text-primary-navy"><?= e($alum['name']) ?></h2>
      <p class="text-sm text-slate-500"><?= e($alum['email']) ?></p>
      <span class="<?= $alum['status'] === 'active' ? 'badge-blue' : 'badge-gold' ?> mt-1"><?= e($alum['status']) ?></span>
      <?php if ($alum['role'] === 'editor'): ?><span class="badge bg-indigo-100 text-indigo-700 mt-1 ml-1.5">Editor</span><?php endif; ?>
    </div>
  </div>

  <dl class="grid grid-cols-2 gap-4 text-sm">
    <div><dt class="text-slate-400 text-xs">Headline</dt><dd class="text-slate-700"><?= e($alum['headline'] ?: '—') ?></dd></div>
    <div><dt class="text-slate-400 text-xs">Company</dt><dd class="text-slate-700"><?= e($alum['company'] ?: '—') ?></dd></div>
    <div><dt class="text-slate-400 text-xs">Location</dt><dd class="text-slate-700"><?= e(location_display($alum) ?: '—') ?></dd></div>
    <div><dt class="text-slate-400 text-xs">Industry</dt><dd class="text-slate-700"><?= e($alum['industry'] ?: '—') ?></dd></div>
    <div><dt class="text-slate-400 text-xs">Profile Visibility</dt><dd class="text-slate-700"><?= e(ucfirst($alum['profile_visibility'])) ?></dd></div>
    <div><dt class="text-slate-400 text-xs">Program</dt><dd class="text-slate-700"><?= e($alum['program'] ?: '—') ?></dd></div>
    <div><dt class="text-slate-400 text-xs">Graduation Year</dt><dd class="text-slate-700"><?= e($alum['graduation_year'] ?: '—') ?></dd></div>
    <div><dt class="text-slate-400 text-xs">Phone</dt><dd class="text-slate-700"><?= e($alum['phone'] ?: '—') ?></dd></div>
    <div class="col-span-2"><dt class="text-slate-400 text-xs mb-1">Bio</dt><dd class="text-slate-700 prose prose-sm max-w-none"><?= $alum['bio'] ?: '—' ?></dd></div>
    <div class="col-span-2"><dt class="text-slate-400 text-xs">Joined</dt><dd class="text-slate-700"><?= e(format_date($alum['created_at'], 'M j, Y')) ?></dd></div>
  </dl>

  <div class="mt-6 pt-6 border-t border-slate-100">
    <h3 class="section-title text-xs mb-3">Alumni Spotlight</h3>
    <?php if (!empty($alum['is_spotlighted'])): ?>
      <p class="text-sm text-slate-600 mb-3 whitespace-pre-line"><?= e($alum['spotlight_note'] ?: '(no note)') ?></p>
      <form method="POST" action="<?= e(url('admin/alumni/' . $alum['id'] . '/unspotlight')) ?>">
        <?= csrf_field() ?>
        <button type="submit" class="btn bg-slate-100 text-slate-600 hover:bg-slate-200 !px-4 !py-2 text-xs">Remove from Spotlight</button>
      </form>
    <?php else: ?>
      <form method="POST" action="<?= e(url('admin/alumni/' . $alum['id'] . '/spotlight')) ?>" class="space-y-2">
        <?= csrf_field() ?>
        <textarea name="spotlight_note" rows="2" class="form-textarea text-sm" placeholder="Optional spotlight note..."></textarea>
        <button type="submit" class="btn-gold !px-4 !py-2 text-xs">Add to Spotlight</button>
      </form>
    <?php endif; ?>
  </div>

  <div class="mt-6 pt-6 border-t border-slate-100">
    <h3 class="section-title text-xs mb-3">Editor Access</h3>
    <p class="text-xs text-slate-500 mb-3">Editors can see any page restricted to "Editors & Admins" in Page Visibility settings. No admin-panel access is granted.</p>
    <form method="POST" action="<?= e(url('admin/alumni/' . $alum['id'] . '/role')) ?>">
      <?= csrf_field() ?>
      <?php if ($alum['role'] === 'editor'): ?>
        <button type="submit" class="btn bg-slate-100 text-slate-600 hover:bg-slate-200 !px-4 !py-2 text-xs">Remove Editor Access</button>
      <?php else: ?>
        <button type="submit" class="btn bg-indigo-100 text-indigo-700 hover:bg-indigo-200 !px-4 !py-2 text-xs">Make Editor</button>
      <?php endif; ?>
    </form>
  </div>

  <div class="flex gap-3 mt-6 pt-6 border-t border-slate-100">
    <?php if ($alum['status'] === 'active'): ?>
      <form method="POST" action="<?= e(url('admin/alumni/' . $alum['id'] . '/suspend')) ?>">
        <?= csrf_field() ?>
        <button type="submit" class="btn bg-amber-100 text-amber-700 hover:bg-amber-200">Suspend Account</button>
      </form>
    <?php else: ?>
      <form method="POST" action="<?= e(url('admin/alumni/' . $alum['id'] . '/activate')) ?>">
        <?= csrf_field() ?>
        <button type="submit" class="btn bg-green-100 text-green-700 hover:bg-green-200">Activate Account</button>
      </form>
    <?php endif; ?>
    <form method="POST" action="<?= e(url('admin/alumni/' . $alum['id'] . '/delete')) ?>" onsubmit="return confirm('Delete this alumni account? This cannot be undone.');">
      <?= csrf_field() ?>
      <button type="submit" class="btn bg-red-100 text-red-700 hover:bg-red-200">Delete Account</button>
    </form>
  </div>
</div>
