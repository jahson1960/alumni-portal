<?php
$qs = function (array $overrides) use ($filters) {
    $params = array_merge($filters, $overrides);
    $params = array_filter($params, fn ($v) => $v !== null && $v !== '' && $v !== []);
    return '?' . http_build_query($params);
};
$toggleJobType = function (string $type) use ($filters, $qs) {
    $active = $filters['job_type'] ?? [];
    $active = in_array($type, $active, true) ? array_values(array_diff($active, [$type])) : array_merge($active, [$type]);
    return $qs(['job_type' => $active]);
};
$toggleExperience = function (string $level) use ($filters, $qs) {
    return $qs(['experience_level' => ($filters['experience_level'] ?? '') === $level ? '' : $level]);
};
$toggleWorkMode = function (string $mode) use ($filters, $qs) {
    $active = $filters['work_mode'] ?? [];
    $active = in_array($mode, $active, true) ? array_values(array_diff($active, [$mode])) : array_merge($active, [$mode]);
    return $qs(['work_mode' => $active]);
};
$toggleLocation = function (string $loc) use ($filters, $qs) {
    return $qs(['location' => ($filters['location'] ?? '') === $loc ? '' : $loc]);
};
$sort = $filters['sort'] ?? 'recent';
$exploreImage = \App\Models\Setting::get('jobs_explore_image', 'https://images.unsplash.com/photo-1477959858617-67f85cf4f1df?auto=format&fit=crop&w=800&q=80');
?>
  <?php if (\App\Core\Auth::check()): ?>
    <div class="flex items-center justify-end gap-4 mb-3">
      <a href="<?= e(url('jobs/mine')) ?>" class="section-link">My Job Posts</a>
      <a href="<?= e(url('jobs/post')) ?>" class="btn-gold !px-3 !py-1.5 text-xs">+ Post a Job</a>
    </div>
  <?php endif; ?>

  <form method="GET" action="<?= e(url('jobs')) ?>" class="mb-4">
    <div class="flex flex-wrap items-center gap-2">
      <?php if ($activeCategory !== null): ?><input type="hidden" name="category" value="<?= e($activeCategory) ?>"><?php endif; ?>
      <div class="relative flex-1 min-w-[220px]">
        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
        <input type="text" name="q" value="<?= e($search) ?>" placeholder="Search job title, keyword, company..." class="form-input !pl-10">
      </div>
      <div class="relative flex-1 min-w-[220px]">
        <i class="fa-solid fa-location-dot absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
        <input type="text" name="location" value="<?= e($location) ?>" placeholder="Location (e.g. Lagos, Nigeria)" class="form-input !pl-10">
      </div>
      <button type="submit" class="btn-gold !px-6 !py-2.5 text-sm">Search Jobs</button>
      <a href="<?= e(url('jobs') . '?' . http_build_query(array_merge($filters, ['tab' => 'search']))) ?>" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-4 !py-2.5 text-sm flex items-center gap-2"><i class="fa-solid fa-sliders"></i> Filters</a>
    </div>
  </form>

  <?php if ($allCategories): ?>
    <div class="flex flex-wrap gap-2 mb-6">
      <a href="<?= e(url('jobs')) ?>" class="rounded-full px-4 py-1.5 text-xs font-semibold <?= $activeCategory === null ? 'bg-primary-navy text-white' : 'bg-sky-50 text-sky-700 hover:bg-sky-100' ?>">All Jobs</a>
      <?php foreach ($allCategories as $cat): ?>
        <a href="<?= e(url('jobs?category=' . urlencode($cat['slug']))) ?>" class="rounded-full px-4 py-1.5 text-xs font-semibold <?= $activeCategory === $cat['slug'] ? 'bg-primary-navy text-white' : 'bg-sky-50 text-sky-700 hover:bg-sky-100' ?>"><?= e($cat['name']) ?></a>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6 items-start">

    <!-- Main -->
    <div>
      <div class="flex items-center justify-between gap-3 mb-4">
        <p class="text-sm text-slate-500">Showing <?= number_format($totalCount) ?> job<?= $totalCount === 1 ? '' : 's' ?></p>
        <div class="flex items-center gap-2">
          <label class="text-xs text-slate-500">Sort by:</label>
          <select onchange="window.location.href=this.value" class="form-input !py-2 text-xs">
            <option value="<?= e(url('jobs') . $qs(['sort' => null])) ?>" <?= $sort === 'recent' ? 'selected' : '' ?>>Most Recent</option>
            <option value="<?= e(url('jobs') . $qs(['sort' => 'salary'])) ?>" <?= $sort === 'salary' ? 'selected' : '' ?>>Salary (High to Low)</option>
          </select>
        </div>
      </div>

      <div class="space-y-4">
        <?php foreach ($jobs as $job): $isSaved = in_array($job['id'], $savedJobIds, true); $hasApplied = in_array((int) $job['id'], $appliedJobIds, true); ?>
          <div class="card p-4 relative">
            <a href="<?= e(url('jobs/' . $job['id'])) ?>" class="absolute inset-0 z-0" aria-label="<?= e($job['title']) ?>"></a>
            <?php if (\App\Core\Auth::check()): ?>
              <form method="POST" action="<?= e(url('jobs/' . $job['id'] . '/save')) ?>" class="absolute top-4 right-4 z-10">
                <?= csrf_field() ?>
                <button type="submit" class="text-sm <?= $isSaved ? 'text-gold' : 'text-slate-300 hover:text-gold' ?>" title="<?= $isSaved ? 'Unsave' : 'Save job' ?>"><i class="fa-solid fa-bookmark"></i></button>
              </form>
            <?php endif; ?>
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
          <p class="text-sm text-slate-400">No open positions match those filters. <a href="<?= e(url('jobs')) ?>" class="text-gold hover:underline">Clear filters</a>.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Sidebar -->
    <aside class="space-y-6">
      <div class="card p-5 !bg-amber-50 !border-amber-100">
        <div class="w-11 h-11 rounded-lg bg-white text-gold flex items-center justify-center mb-3 text-lg"><i class="fa-solid fa-briefcase"></i></div>
        <h3 class="text-sm font-extrabold text-primary-navy mb-1.5">Build Your Future</h3>
        <p class="text-xs text-slate-600 mb-4 leading-relaxed">Access exclusive job opportunities, network with top employers and take the next step in your career journey.</p>
        <?php if (!\App\Core\Auth::check()): ?>
          <a href="<?= e(url('register')) ?>" class="btn-gold !px-4 !py-2.5 text-xs w-full text-center flex items-center justify-center gap-2">Create Your Profile <i class="fa-solid fa-arrow-right"></i></a>
        <?php elseif (empty($resumeUrl)): ?>
          <a href="<?= e(url('profile/edit')) ?>#resume" class="btn-gold !px-4 !py-2.5 text-xs w-full text-center flex items-center justify-center gap-2">Upload Your CV <i class="fa-solid fa-arrow-right"></i></a>
        <?php else: ?>
          <a href="<?= e(url('profile/edit')) ?>" class="btn-gold !px-4 !py-2.5 text-xs w-full text-center flex items-center justify-center gap-2">Update Your Profile <i class="fa-solid fa-arrow-right"></i></a>
        <?php endif; ?>
      </div>

      <div class="card p-5">
        <h3 class="text-sm font-extrabold text-primary-navy mb-1 flex items-center gap-2"><i class="fa-solid fa-sliders text-gold"></i> Quick Filters</h3>

        <details class="group border-b border-slate-100 py-3" open>
          <summary class="flex items-center justify-between cursor-pointer list-none marker:hidden [&::-webkit-details-marker]:hidden text-xs font-extrabold text-slate-500 uppercase tracking-wide">
            Job Type
            <i class="fa-solid fa-chevron-down text-slate-400 text-[0.65rem] transition-transform group-open:rotate-180"></i>
          </summary>
          <div class="space-y-1.5 mt-2.5">
            <?php foreach (\App\Models\Job::JOB_TYPES as $type): $active = in_array($type, $filters['job_type'] ?? [], true); $count = $jobTypeCounts[$type] ?? 0; ?>
              <a href="<?= e(url('jobs') . $toggleJobType($type)) ?>" class="flex items-center justify-between gap-2 text-sm py-0.5 <?= $active ? 'text-primary-navy font-semibold' : 'text-slate-600 hover:text-primary-navy' ?>">
                <span class="flex items-center gap-2">
                  <span class="w-4 h-4 rounded border flex items-center justify-center flex-shrink-0 <?= $active ? 'bg-gold border-gold' : 'border-slate-300' ?>"><?php if ($active): ?><i class="fa-solid fa-check text-white text-[0.55rem]"></i><?php endif; ?></span>
                  <?= e($type) ?>
                </span>
                <span class="text-xs text-slate-400">(<?= (int) $count ?>)</span>
              </a>
            <?php endforeach; ?>
          </div>
        </details>

        <details class="group border-b border-slate-100 py-3" open>
          <summary class="flex items-center justify-between cursor-pointer list-none marker:hidden [&::-webkit-details-marker]:hidden text-xs font-extrabold text-slate-500 uppercase tracking-wide">
            Experience Level
            <i class="fa-solid fa-chevron-down text-slate-400 text-[0.65rem] transition-transform group-open:rotate-180"></i>
          </summary>
          <div class="space-y-1.5 mt-2.5">
            <?php foreach (\App\Models\Job::EXPERIENCE_LEVELS as $key => $label): $active = ($filters['experience_level'] ?? '') === $key; $count = $experienceCounts[$key] ?? 0; ?>
              <a href="<?= e(url('jobs') . $toggleExperience($key)) ?>" class="flex items-center justify-between gap-2 text-sm py-0.5 <?= $active ? 'text-primary-navy font-semibold' : 'text-slate-600 hover:text-primary-navy' ?>">
                <span class="flex items-center gap-2">
                  <span class="w-4 h-4 rounded border flex items-center justify-center flex-shrink-0 <?= $active ? 'bg-gold border-gold' : 'border-slate-300' ?>"><?php if ($active): ?><i class="fa-solid fa-check text-white text-[0.55rem]"></i><?php endif; ?></span>
                  <?= e($label) ?>
                </span>
                <span class="text-xs text-slate-400">(<?= (int) $count ?>)</span>
              </a>
            <?php endforeach; ?>
          </div>
        </details>

        <details class="group pt-3" open>
          <summary class="flex items-center justify-between cursor-pointer list-none marker:hidden [&::-webkit-details-marker]:hidden text-xs font-extrabold text-slate-500 uppercase tracking-wide">
            Location
            <i class="fa-solid fa-chevron-down text-slate-400 text-[0.65rem] transition-transform group-open:rotate-180"></i>
          </summary>
          <div class="space-y-1.5 mt-2.5">
            <?php foreach ($locationBreakdown['top'] as $row): $active = ($filters['location'] ?? '') === $row['location']; ?>
              <a href="<?= e(url('jobs') . $toggleLocation($row['location'])) ?>" class="flex items-center justify-between gap-2 text-sm py-0.5 <?= $active ? 'text-primary-navy font-semibold' : 'text-slate-600 hover:text-primary-navy' ?>">
                <span class="flex items-center gap-2">
                  <span class="w-4 h-4 rounded border flex items-center justify-center flex-shrink-0 <?= $active ? 'bg-gold border-gold' : 'border-slate-300' ?>"><?php if ($active): ?><i class="fa-solid fa-check text-white text-[0.55rem]"></i><?php endif; ?></span>
                  <?= e($row['location']) ?>
                </span>
                <span class="text-xs text-slate-400">(<?= (int) $row['cnt'] ?>)</span>
              </a>
            <?php endforeach; ?>
            <?php if ($locationBreakdown['remote'] > 0): $active = in_array('remote', $filters['work_mode'] ?? [], true); ?>
              <a href="<?= e(url('jobs') . $toggleWorkMode('remote')) ?>" class="flex items-center justify-between gap-2 text-sm py-0.5 <?= $active ? 'text-primary-navy font-semibold' : 'text-slate-600 hover:text-primary-navy' ?>">
                <span class="flex items-center gap-2">
                  <span class="w-4 h-4 rounded border flex items-center justify-center flex-shrink-0 <?= $active ? 'bg-gold border-gold' : 'border-slate-300' ?>"><?php if ($active): ?><i class="fa-solid fa-check text-white text-[0.55rem]"></i><?php endif; ?></span>
                  Remote
                </span>
                <span class="text-xs text-slate-400">(<?= (int) $locationBreakdown['remote'] ?>)</span>
              </a>
            <?php endif; ?>
            <?php if ($locationBreakdown['other'] > 0): ?>
              <div class="flex items-center justify-between gap-2 text-sm py-0.5 text-slate-400">
                <span class="flex items-center gap-2"><span class="w-4 h-4 rounded border border-slate-200 flex-shrink-0"></span> Other</span>
                <span class="text-xs">(<?= (int) $locationBreakdown['other'] ?>)</span>
              </div>
            <?php endif; ?>
          </div>
        </details>
      </div>

      <a href="<?= e(url('jobs') . '?tab=companies') ?>" class="relative block rounded-xl overflow-hidden text-white p-5 bg-cover bg-center group" style="background-image: linear-gradient(180deg, rgba(9,26,46,0.55) 0%, rgba(9,26,46,0.92) 100%), url('<?= e($exploreImage) ?>');">
        <div class="w-11 h-11 rounded-lg bg-white/15 backdrop-blur-sm flex items-center justify-center mb-16 text-lg"><i class="fa-solid fa-building"></i></div>
        <h3 class="text-base font-extrabold mb-1.5 leading-snug">Explore Career Opportunities</h3>
        <p class="text-xs text-slate-200 mb-4 leading-relaxed">Connect with leading companies and grow your network.</p>
        <span class="btn bg-white !text-primary-navy group-hover:bg-slate-100 !px-4 !py-2 text-xs inline-flex items-center gap-2">View Companies <i class="fa-solid fa-arrow-right"></i></span>
      </a>

      <?php if ($latestEmployers): ?>
        <div class="card p-5">
          <h3 class="text-sm font-extrabold text-primary-navy mb-4 flex items-center gap-2"><i class="fa-regular fa-building text-gold"></i> Latest Employer</h3>
          <div class="grid grid-cols-3 gap-2.5 mb-3">
            <?php foreach ($latestEmployers as $company): ?>
              <a href="<?= e(url('companies/' . $company['id'])) ?>" class="w-full aspect-square rounded-lg border border-slate-100 flex items-center justify-center p-2 hover:border-gold" title="<?= e($company['name']) ?>">
                <?php if (!empty($company['logo'])): ?>
                  <img src="<?= e($company['logo']) ?>" alt="<?= e($company['name']) ?>" class="max-w-full max-h-full object-contain">
                <?php else: ?>
                  <span class="text-xs font-bold text-primary-navy"><?= e(mb_substr($company['name'], 0, 2)) ?></span>
                <?php endif; ?>
              </a>
            <?php endforeach; ?>
          </div>
          <a href="<?= e(url('jobs') . '?tab=companies') ?>" class="text-xs font-semibold text-gold hover:underline">View All Companies &rarr;</a>
        </div>
      <?php endif; ?>
    </aside>
  </div>
