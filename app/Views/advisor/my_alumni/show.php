<div class="max-w-[900px] mx-auto px-4 md:px-8 py-8">
  <a href="<?= e(url('advisor/my-alumni')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to My Assigned Alumni</a>

  <div class="card p-6 mb-6">
    <div class="flex items-center justify-between gap-4 flex-wrap">
      <div class="flex items-center gap-4">
        <?= avatar_html($alum, 'w-16 h-16') ?>
        <div>
          <h2 class="text-lg font-extrabold text-primary-navy"><?= e($alum['name']) ?></h2>
          <p class="text-sm text-slate-500"><?= e($alum['headline'] ?: $alum['email']) ?></p>
          <p class="text-xs text-slate-400"><?= e(location_display($alum) ?: '—') ?></p>
        </div>
      </div>
      <a href="<?= e(url('messages/' . $alum['id'])) ?>" class="btn-gold !px-4 !py-2 text-xs"><i class="fa-solid fa-envelope"></i> Message</a>
    </div>

    <dl class="grid grid-cols-2 gap-4 text-sm mt-6 pt-6 border-t border-slate-100">
      <div><dt class="text-slate-400 text-xs">Company</dt><dd class="text-slate-700"><?= e($alum['company'] ?: '—') ?></dd></div>
      <div><dt class="text-slate-400 text-xs">Industry</dt><dd class="text-slate-700"><?= e($alum['industry'] ?: '—') ?></dd></div>
      <div><dt class="text-slate-400 text-xs">Program</dt><dd class="text-slate-700"><?= e($alum['program'] ?: '—') ?></dd></div>
      <div><dt class="text-slate-400 text-xs">Graduation Year</dt><dd class="text-slate-700"><?= e($alum['graduation_year'] ?: '—') ?></dd></div>
      <div class="col-span-2"><dt class="text-slate-400 text-xs mb-1">Bio</dt><dd class="text-slate-700 prose prose-sm max-w-none"><?= $alum['bio'] ?: '—' ?></dd></div>
    </dl>
  </div>

  <div class="card p-6">
    <h3 class="section-title text-xs mb-3">My Advising Notes</h3>
    <p class="text-xs text-slate-400 mb-4">Only you and admin can see these notes.</p>

    <form method="POST" action="<?= e(url('advisor/my-alumni/' . $alum['id'] . '/notes')) ?>" class="mb-5 space-y-2">
      <?= csrf_field() ?>
      <textarea name="note" rows="3" class="form-textarea text-sm" placeholder="Add an advising note..." required></textarea>
      <button type="submit" class="btn-gold !px-4 !py-2 text-xs">Add Note</button>
    </form>

    <div class="space-y-3">
      <?php foreach ($notes as $note): ?>
        <div class="bg-slate-50 border border-slate-100 rounded-lg p-3">
          <p class="text-sm text-slate-600 whitespace-pre-line"><?= e($note['note']) ?></p>
          <p class="text-[0.65rem] text-slate-400 mt-1"><?= e(format_date($note['created_at'], 'M j, Y')) ?></p>
        </div>
      <?php endforeach; ?>
      <?php if (empty($notes)): ?>
        <p class="text-xs text-slate-400">No notes yet.</p>
      <?php endif; ?>
    </div>
  </div>
</div>
