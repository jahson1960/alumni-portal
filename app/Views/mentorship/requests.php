<div class="max-w-[1280px] mx-auto px-4 md:px-8 py-8">
  <a href="<?= e(url('mentorship')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to Mentors</a>
  <div class="section-header">
    <h1 class="section-title text-base">My Mentorship Requests</h1>
  </div>

  <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-3">Incoming (as Mentor) &mdash; <?= count($incoming) ?></h3>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 mb-8">
    <?php foreach ($incoming as $req): ?>
      <div class="card p-5">
        <div class="flex items-center gap-3 mb-2">
          <?= avatar_html($req, 'w-9 h-9') ?>
          <div class="min-w-0">
            <p class="text-sm font-bold text-primary-navy truncate"><?= e($req['name']) ?></p>
            <p class="text-[0.68rem] text-slate-400"><?= e(time_ago($req['created_at'])) ?></p>
          </div>
        </div>
        <?php if ($req['area']): ?><p class="text-xs text-slate-500 mb-1"><strong>Area:</strong> <?= e($req['area']) ?></p><?php endif; ?>
        <?php if ($req['message']): ?><p class="text-xs text-slate-600 mb-3 whitespace-pre-line"><?= e($req['message']) ?></p><?php endif; ?>
        <?php if ($req['status'] === 'pending'): ?>
          <div class="flex gap-3">
            <form method="POST" action="<?= e(url('mentorship/' . $req['id'] . '/accept')) ?>">
              <?= csrf_field() ?>
              <button type="submit" class="text-xs font-semibold text-green-600 hover:underline">Accept</button>
            </form>
            <form method="POST" action="<?= e(url('mentorship/' . $req['id'] . '/decline')) ?>">
              <?= csrf_field() ?>
              <button type="submit" class="text-xs font-semibold text-red-600 hover:underline">Decline</button>
            </form>
          </div>
        <?php else: ?>
          <span class="badge <?= $req['status'] === 'accepted' ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500' ?>"><?= e(ucfirst($req['status'])) ?></span>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
    <?php if (empty($incoming)): ?>
      <p class="text-sm text-slate-400 col-span-full">No incoming mentorship requests.</p>
    <?php endif; ?>
  </div>

  <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wide mb-3">Sent (as Mentee) &mdash; <?= count($sent) ?></h3>
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ($sent as $req): ?>
      <div class="card p-5">
        <div class="flex items-center gap-3 mb-2">
          <?= avatar_html($req, 'w-9 h-9') ?>
          <div class="min-w-0">
            <p class="text-sm font-bold text-primary-navy truncate"><?= e($req['name']) ?></p>
            <p class="text-[0.68rem] text-slate-400"><?= e(time_ago($req['created_at'])) ?></p>
          </div>
        </div>
        <?php if ($req['area']): ?><p class="text-xs text-slate-500 mb-1"><strong>Area:</strong> <?= e($req['area']) ?></p><?php endif; ?>
        <span class="badge <?= $req['status'] === 'accepted' ? 'bg-green-100 text-green-700' : ($req['status'] === 'declined' ? 'bg-slate-100 text-slate-500' : 'bg-amber-100 text-amber-700') ?>"><?= e(ucfirst($req['status'])) ?></span>
      </div>
    <?php endforeach; ?>
    <?php if (empty($sent)): ?>
      <p class="text-sm text-slate-400 col-span-full">You haven't requested mentorship from anyone yet.</p>
    <?php endif; ?>
  </div>
</div>
