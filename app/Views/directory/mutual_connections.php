<div class="max-w-[1400px] mx-auto px-4 md:px-8 py-8">
  <a href="<?= e($backUrl) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> <?= e($backLabel) ?></a>

  <h1 class="text-xl font-extrabold text-primary-navy mb-1">Mutual Connections</h1>
  <p class="text-sm text-slate-500 mb-6">Alumni you and <?= e($profile['name']) ?> are both connected with (<?= count($mutuals) ?>).</p>

  <?php if ($mutuals): ?>
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <?php foreach ($mutuals as $person): ?>
        <div class="card p-5">
          <a href="<?= e(url('alumni/' . $person['id'])) ?>"><?= avatar_html($person, 'w-11 h-11') ?></a>
          <a href="<?= e(url('alumni/' . $person['id'])) ?>" class="text-sm font-bold text-primary-navy hover:text-gold truncate block mt-2"><?= e($person['name']) ?></a>
          <p class="text-xs text-slate-500 truncate"><?= e($person['headline'] ?: '') ?></p>
          <?php if ($person['company']): ?><p class="text-xs text-slate-600 font-medium truncate"><?= e($person['company']) ?></p><?php endif; ?>
          <?php if (location_display($person)): ?><p class="text-[0.68rem] text-slate-400 mt-1"><i class="fa-solid fa-location-dot"></i> <?= e(location_display($person)) ?></p><?php endif; ?>
          <a href="<?= e(url('messages/' . $person['id'])) ?>" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !py-1.5 text-xs w-full mt-3"><i class="fa-regular fa-comment-dots"></i> Message</a>
        </div>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="card p-8 text-center text-sm text-slate-400">No mutual connections yet.</div>
  <?php endif; ?>
</div>
