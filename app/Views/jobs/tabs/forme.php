<?php $authUser = \App\Core\Auth::user(); ?>

<?php if (!$authUser): ?>
  <div class="card p-10 text-center max-w-lg mx-auto">
    <div class="w-14 h-14 rounded-full bg-gold/10 text-gold flex items-center justify-center text-2xl mx-auto mb-4"><i class="fa-solid fa-compass"></i></div>
    <h3 class="text-base font-bold text-primary-navy mb-1.5">Jobs picked for you</h3>
    <p class="text-sm text-slate-500 mb-5">Log in to see opportunities matched to the job categories you're interested in.</p>
    <div class="flex items-center justify-center gap-3">
      <a href="<?= e(url('login')) ?>" class="btn-gold !px-5 !py-2.5 text-sm">Log In</a>
      <a href="<?= e(url('register')) ?>" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-5 !py-2.5 text-sm">Create Account</a>
    </div>
  </div>

<?php elseif (!$hasCategories): ?>
  <div class="card p-10 text-center max-w-lg mx-auto">
    <div class="w-14 h-14 rounded-full bg-gold/10 text-gold flex items-center justify-center text-2xl mx-auto mb-4"><i class="fa-solid fa-compass"></i></div>
    <h3 class="text-base font-bold text-primary-navy mb-1.5">Tell us what you're interested in</h3>
    <p class="text-sm text-slate-500 mb-5">Select your job categories and we'll match you with relevant opportunities every time you visit.</p>
    <a href="<?= e(url('profile/edit')) ?>#job_categories" class="btn-gold !px-5 !py-2.5 text-sm inline-flex items-center gap-2">Choose Your Categories <i class="fa-solid fa-arrow-right"></i></a>
  </div>

<?php else: ?>

  <div class="card p-5 mb-6 !bg-gradient-to-br !from-primary-navy !to-slate-900 !border-0 text-white">
    <div class="flex flex-wrap items-center justify-between gap-4">
      <div>
        <p class="text-xs font-bold text-gold uppercase tracking-wide mb-1.5">Matched to your interests</p>
        <div class="flex flex-wrap gap-1.5">
          <?php foreach ($myCategories as $cat): ?>
            <span class="badge bg-white/15 text-white"><?= e($cat['name']) ?></span>
          <?php endforeach; ?>
        </div>
      </div>
      <a href="<?= e(url('profile/edit')) ?>#job_categories" class="btn bg-white !text-primary-navy hover:bg-slate-100 !px-4 !py-2 text-xs flex items-center gap-2 flex-shrink-0"><i class="fa-solid fa-pen"></i> Edit Categories</a>
    </div>
  </div>

  <p class="text-sm text-slate-500 mb-4">Showing <?= number_format($totalCount) ?> job<?= $totalCount === 1 ? '' : 's' ?> matching your categories</p>

  <div class="space-y-4">
    <?php foreach ($jobs as $job): $isSaved = in_array($job['id'], $savedJobIds, true); $hasApplied = in_array((int) $job['id'], $appliedJobIds, true); ?>
      <div class="card p-4 relative">
        <a href="<?= e(url('jobs/' . $job['id'])) ?>" class="absolute inset-0 z-0" aria-label="<?= e($job['title']) ?>"></a>
        <form method="POST" action="<?= e(url('jobs/' . $job['id'] . '/save')) ?>" class="absolute top-4 right-4 z-10">
          <?= csrf_field() ?>
          <button type="submit" class="text-sm <?= $isSaved ? 'text-gold' : 'text-slate-300 hover:text-gold' ?>" title="<?= $isSaved ? 'Unsave' : 'Save job' ?>"><i class="fa-solid fa-bookmark"></i></button>
        </form>
        <div class="relative z-0 pointer-events-none flex gap-4">
          <?= job_logo_html($job, 'w-14 h-14') ?>
          <div class="min-w-0 flex-1">
            <h4 class="text-base font-bold text-primary-navy truncate"><?= e($job['title']) ?></h4>
            <p class="text-sm text-slate-500 truncate mb-2"><?= e($job['company']) ?></p>
            <?php $jobCats = \App\Models\Category::forJob((int) $job['id']); ?>
            <div class="flex flex-wrap items-center gap-1.5 mb-2">
              <?php if ($job['is_featured']): ?><span class="badge-gold">FEATURED</span><?php endif; ?>
              <?php foreach ($jobCats as $jobCat): $pc = palette_classes($jobCat['slug']); ?>
                <span class="badge <?= e($pc['bg']) ?> <?= e($pc['text']) ?>"><?= e(strtoupper($jobCat['name'])) ?></span>
              <?php endforeach; ?>
              <span class="badge bg-slate-100 text-slate-600"><?= e(strtoupper($job['job_type'])) ?></span>
              <?php if ($hasApplied): ?><span class="badge-green">APPLIED</span><?php endif; ?>
            </div>
            <div class="flex flex-wrap items-center gap-3 text-xs text-slate-500">
              <span><i class="fa-solid fa-location-dot"></i> <?= e($job['location']) ?></span>
              <span class="ml-auto"><?= e(time_ago($job['posted_at'])) ?></span>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
    <?php if (empty($jobs)): ?>
      <div class="card p-10 text-center">
        <div class="w-12 h-12 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-xl mx-auto mb-3"><i class="fa-regular fa-compass"></i></div>
        <p class="text-sm text-slate-500 mb-1">No open positions match your categories right now.</p>
        <p class="text-xs text-slate-400">Check back soon, or <a href="<?= e(url('jobs')) ?>" class="text-gold hover:underline">browse the full Job Board</a>.</p>
      </div>
    <?php endif; ?>
  </div>

<?php endif; ?>
