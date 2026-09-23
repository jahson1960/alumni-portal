<?php
$popularSearches = [
    ['label' => 'Fintech Jobs in Lagos', 'params' => ['q' => 'fintech', 'location' => 'Lagos']],
    ['label' => 'Data Analyst Remote', 'params' => ['q' => 'data analyst', 'work_mode' => ['remote']]],
    ['label' => 'Marketing Manager', 'params' => ['q' => 'marketing manager']],
    ['label' => 'Business Development', 'params' => ['q' => 'business development']],
    ['label' => 'Product Manager', 'params' => ['q' => 'product manager']],
    ['label' => 'Consulting Associate', 'params' => ['q' => 'consultant']],
];
?>
  <div class="flex justify-end items-center gap-4 mb-6">
    <a href="<?= e(url('jobs') . '?tab=search') ?>" class="text-xs font-semibold text-gold hover:underline">Clear All</a>
    <button type="submit" form="advanced-search-form" class="btn-gold !px-5 !py-2.5 text-sm">Search Jobs</button>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-[1fr_280px] gap-6 items-start">
    <form method="GET" action="<?= e(url('jobs')) ?>" id="advanced-search-form" class="card p-6">
      <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">

        <div>
          <label class="form-label">Keywords</label>
          <input type="text" name="q" value="<?= e($filters['q'] ?? '') ?>" placeholder="Job title, skills, company..." class="form-input">
        </div>
        <div>
          <label class="form-label">Industry</label>
          <select name="industry" class="form-input">
            <option value="">Select industry</option>
            <?php foreach ($industries as $opt): ?>
              <option value="<?= e($opt) ?>" <?= ($filters['industry'] ?? '') === $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="form-label">Job Categories</label>
          <select name="category" class="form-input">
            <option value="">Select category</option>
            <?php foreach ($allCategories as $cat): ?>
              <option value="<?= e($cat['slug']) ?>" <?= ($filters['category'] ?? '') === $cat['slug'] ? 'selected' : '' ?>><?= e($cat['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="form-label">Salary Range</label>
          <select name="salary" class="form-input">
            <option value="">Select salary range</option>
            <?php foreach (\App\Models\Job::SALARY_BUCKETS as $key => $bucket): ?>
              <option value="<?= e($key) ?>" <?= ($filters['salary'] ?? '') === $key ? 'selected' : '' ?>><?= e($bucket['label']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div>
          <label class="form-label">Job Type</label>
          <div class="space-y-1.5 mt-1">
            <?php foreach (\App\Models\Job::JOB_TYPES as $type): ?>
              <label class="flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="job_type[]" value="<?= e($type) ?>" <?= in_array($type, $filters['job_type'] ?? [], true) ? 'checked' : '' ?> class="rounded border-slate-300 text-gold focus:ring-gold">
                <?= e($type) ?>
              </label>
            <?php endforeach; ?>
          </div>
        </div>
        <div>
          <label class="form-label">Company</label>
          <input type="text" name="company" value="<?= e($filters['company'] ?? '') ?>" placeholder="Search company..." class="form-input">
        </div>

        <div>
          <label class="form-label">Experience Level</label>
          <select name="experience_level" class="form-input">
            <option value="">Select experience level</option>
            <?php foreach (\App\Models\Job::EXPERIENCE_LEVELS as $key => $label): ?>
              <option value="<?= e($key) ?>" <?= ($filters['experience_level'] ?? '') === $key ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="form-label">Remote</label>
          <div class="flex flex-wrap items-center gap-4 mt-1">
            <?php foreach (\App\Models\Job::WORK_MODES as $key => $label): ?>
              <label class="flex items-center gap-2 text-sm text-slate-700">
                <input type="checkbox" name="work_mode[]" value="<?= e($key) ?>" <?= in_array($key, $filters['work_mode'] ?? [], true) ? 'checked' : '' ?> class="rounded border-slate-300 text-gold focus:ring-gold">
                <?= e($label) ?>
              </label>
            <?php endforeach; ?>
          </div>
        </div>

        <div>
          <label class="form-label">Location</label>
          <input type="text" name="location" value="<?= e($filters['location'] ?? '') ?>" placeholder="Enter city, state or country" class="form-input">
        </div>
        <div>
          <label class="form-label">Sort By</label>
          <select name="sort" class="form-input">
            <option value="recent" <?= ($filters['sort'] ?? 'recent') === 'recent' ? 'selected' : '' ?>>Most Recent</option>
            <option value="salary" <?= ($filters['sort'] ?? '') === 'salary' ? 'selected' : '' ?>>Salary (High to Low)</option>
          </select>
        </div>

        <div>
          <label class="form-label">Posted Within</label>
          <select name="posted_within" class="form-input">
            <option value="">Any time</option>
            <option value="24h" <?= ($filters['posted_within'] ?? '') === '24h' ? 'selected' : '' ?>>Last 24 hours</option>
            <option value="week" <?= ($filters['posted_within'] ?? '') === 'week' ? 'selected' : '' ?>>Last week</option>
            <option value="month" <?= ($filters['posted_within'] ?? '') === 'month' ? 'selected' : '' ?>>Last month</option>
          </select>
        </div>
      </div>
    </form>

    <!-- Sidebar -->
    <aside class="space-y-6">
      <div class="card p-4">
        <h3 class="text-sm font-bold text-primary-navy mb-3">Popular Searches</h3>
        <div class="space-y-2.5">
          <?php foreach ($popularSearches as $preset): ?>
            <a href="<?= e(url('jobs') . '?' . http_build_query($preset['params'])) ?>" class="flex items-center gap-2 text-xs text-slate-600 hover:text-gold">
              <i class="fa-solid fa-magnifying-glass text-slate-400"></i> <?= e($preset['label']) ?>
            </a>
          <?php endforeach; ?>
        </div>
      </div>

      <div class="card p-4 !bg-amber-50 !border-amber-100">
        <div class="flex items-center gap-2 mb-1.5">
          <i class="fa-solid fa-lightbulb text-amber-500"></i>
          <h3 class="text-sm font-bold text-primary-navy">Search Tips</h3>
        </div>
        <p class="text-xs text-slate-600">Use specific keywords and filters to find the most relevant opportunities.</p>
      </div>
    </aside>
  </div>
