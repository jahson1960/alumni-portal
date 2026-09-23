<div class="max-w-3xl mx-auto px-4 md:px-8 py-8">
  <a href="<?= e(url('jobs')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to Jobs</a>

  <div class="card p-6 md:p-8 mb-6">
    <div class="flex items-center gap-4 mb-4">
      <?php if (!empty($company['logo'])): ?>
        <img src="<?= e($company['logo']) ?>" alt="" class="w-16 h-16 object-contain rounded border border-slate-200 bg-white p-1">
      <?php else: ?>
        <div class="w-16 h-16 rounded bg-slate-100 flex items-center justify-center text-primary-navy font-bold"><?= e(mb_substr($company['name'], 0, 2)) ?></div>
      <?php endif; ?>
      <div>
        <h1 class="text-xl font-extrabold text-primary-navy"><?= e($company['name']) ?></h1>
        <?php if ($company['location']): ?><p class="text-sm text-slate-500"><i class="fa-solid fa-location-dot"></i> <?= e($company['location']) ?></p><?php endif; ?>
      </div>
      <?php if ($company['website']): ?>
        <a href="<?= e($company['website']) ?>" target="_blank" rel="noopener" class="btn bg-slate-100 text-slate-600 hover:bg-slate-200 !px-4 !py-2 text-xs ml-auto">Visit Website</a>
      <?php endif; ?>
    </div>
    <?php if ($company['about']): ?>
      <div class="prose prose-sm max-w-none prose-headings:text-primary-navy prose-a:text-gold"><?= $company['about'] ?></div>
    <?php endif; ?>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
    <div>
      <h3 class="section-title text-xs mb-3">Open Positions (<?= count($openJobs) ?>)</h3>
      <div class="space-y-3">
        <?php foreach ($openJobs as $job): ?>
          <a href="<?= e(url('jobs/' . $job['id'])) ?>" class="card p-4 block">
            <h4 class="text-sm font-bold text-primary-navy"><?= e($job['title']) ?></h4>
            <p class="text-xs text-slate-500"><i class="fa-solid fa-location-dot"></i> <?= e($job['location']) ?> &bull; <?= e($job['job_type']) ?></p>
          </a>
        <?php endforeach; ?>
        <?php if (empty($openJobs)): ?>
          <p class="text-sm text-slate-400">No open positions right now.</p>
        <?php endif; ?>
      </div>
    </div>

    <div>
      <h3 class="section-title text-xs mb-3">Alumni Here (<?= count($alumniHere) ?>)</h3>
      <div class="space-y-3">
        <?php foreach ($alumniHere as $person): ?>
          <a href="<?= e(url('alumni/' . $person['id'])) ?>" class="card p-4 flex items-center gap-3">
            <?= avatar_html($person, 'w-9 h-9') ?>
            <div class="min-w-0">
              <p class="text-sm font-bold text-primary-navy truncate"><?= e($person['name']) ?></p>
              <p class="text-xs text-slate-500 truncate"><?= e($person['headline'] ?: '') ?></p>
            </div>
          </a>
        <?php endforeach; ?>
        <?php if (empty($alumniHere)): ?>
          <p class="text-sm text-slate-400">No alumni listed at this company yet.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>
