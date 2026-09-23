<?php
$isEdit = $job !== null;
$showDraftOption = !$isEdit || ($job['approval_status'] ?? '') === 'draft';
$primaryLabel = ($isEdit && !in_array($job['approval_status'] ?? '', ['rejected', 'draft'], true))
    ? 'Save Changes'
    : ($postingMode === 'approval' ? 'Submit for Review' : 'Publish Job');
$categoryIcons = [
    'banking-finance' => 'bi-bank',
    'consulting' => 'bi-people',
    'marketing' => 'bi-megaphone',
    'operations' => 'bi-gear',
    'technology' => 'bi-laptop',
];
$postingTips = [
    ['icon' => 'bi-file-earmark-text', 'title' => 'Be clear and specific', 'text' => 'Use a specific job title and clearly outline responsibilities.'],
    ['icon' => 'bi-megaphone', 'title' => 'Highlight what matters', 'text' => 'Include must-have skills, experience, and perks.'],
    ['icon' => 'bi-shield-check', 'title' => 'Review before you submit', 'text' => 'Ensure all details are correct before sending for review.'],
    ['icon' => 'bi-clock-history', 'title' => 'Allow time for review', 'text' => 'Job posts are usually reviewed within 1-2 business days.'],
];
?>
<div class="max-w-[1280px] mx-auto px-4 md:px-8 py-8">
  <a href="<?= e(url('jobs')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to Careers</a>

  <h1 class="text-2xl font-extrabold text-primary-navy"><?= $isEdit ? 'Edit Job Post' : 'Post a Job' ?></h1>
  <p class="text-sm text-slate-500 mt-1 mb-6">Share an opportunity with our alumni community and find the right talent.</p>

  <!-- Mobile step progress -->
  <div class="lg:hidden flex items-center mb-6" id="step-progress">
    <?php for ($i = 1; $i <= 4; $i++): ?>
      <div class="step-dot w-7 h-7 rounded-full flex items-center justify-center text-xs font-bold flex-shrink-0 <?= $i === 1 ? 'bg-primary-navy text-white' : 'bg-slate-200 text-slate-500' ?>" data-dot="<?= $i ?>"><?= $i ?></div>
      <?php if ($i < 4): ?><div class="step-connector flex-1 h-0.5 mx-1 <?= $i === 1 ? 'bg-primary-navy' : 'bg-slate-200' ?>" data-connector="<?= $i ?>"></div><?php endif; ?>
    <?php endfor; ?>
  </div>

  <?php require dirname(__DIR__) . '/partials/errors.php'; ?>

  <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6 items-start">
    <form method="POST" action="<?= e($isEdit ? url('jobs/mine/' . $job['id']) : url('jobs/post')) ?>" id="post-job-form">
      <?= csrf_field() ?>

      <!-- Step 1: Job Details -->
      <div class="form-step card p-6 mb-6" data-step="1">
        <div class="flex items-start gap-3 mb-4">
          <span class="w-6 h-6 rounded-full bg-primary-navy text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">1</span>
          <div>
            <h3 class="text-sm font-bold text-primary-navy">Job Details</h3>
            <p class="text-xs text-slate-500 mt-0.5">Provide accurate information to attract the right candidates.</p>
          </div>
        </div>

        <div class="space-y-4">
          <div>
            <label class="form-label" for="title">Job Title <span class="text-red-500">*</span></label>
            <input type="text" id="title" name="title" class="form-input" placeholder="e.g. Senior Product Manager" value="<?= e($job['title'] ?? '') ?>" required>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="form-label" for="company">Company <span class="text-red-500">*</span></label>
              <input type="text" id="company" name="company" class="form-input" placeholder="e.g. Kuda Bank" value="<?= e($job['company'] ?? '') ?>" required>
            </div>
            <div>
              <label class="form-label" for="location">Location <span class="text-red-500">*</span></label>
              <div class="relative">
                <input type="text" id="location" name="location" class="form-input !pl-3 !pr-9" placeholder="e.g. Lagos, Nigeria" value="<?= e($job['location'] ?? '') ?>" required>
                <i class="fa-solid fa-location-dot absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
              </div>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="form-label" for="job_type">Job Type <span class="text-red-500">*</span></label>
              <select id="job_type" name="job_type" class="form-input">
                <?php foreach (\App\Models\Job::JOB_TYPES as $type): ?>
                  <option value="<?= e($type) ?>" <?= ($job['job_type'] ?? 'Full-time') === $type ? 'selected' : '' ?>><?= e($type) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="form-label" for="work_mode">Work Mode <span class="text-red-500">*</span></label>
              <select id="work_mode" name="work_mode" class="form-input">
                <?php foreach (\App\Models\Job::WORK_MODES as $key => $label): ?>
                  <option value="<?= e($key) ?>" <?= ($job['work_mode'] ?? 'onsite') === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
              <label class="form-label" for="experience_level">Experience Level <span class="text-red-500">*</span></label>
              <select id="experience_level" name="experience_level" class="form-input">
                <option value="">Select experience level</option>
                <?php foreach (\App\Models\Job::EXPERIENCE_LEVELS as $key => $label): ?>
                  <option value="<?= e($key) ?>" <?= ($job['experience_level'] ?? '') === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="form-label" for="closing_date">Closing Date</label>
              <input type="date" id="closing_date" name="closing_date" class="form-input" value="<?= e($job['closing_date'] ?? '') ?>">
              <p class="text-xs text-slate-400 mt-1">Leave blank if you'll review applications on a rolling basis.</p>
            </div>
          </div>

          <div>
            <label class="form-label">Salary Range (NGN)</label>
            <div class="flex items-center gap-2">
              <input type="number" id="salary_min" name="salary_min" class="form-input" min="0" step="1000" placeholder="Minimum salary" value="<?= e($job['salary_min'] ?? '') ?>">
              <span class="text-slate-400">&ndash;</span>
              <input type="number" id="salary_max" name="salary_max" class="form-input" min="0" step="1000" placeholder="Maximum salary" value="<?= e($job['salary_max'] ?? '') ?>">
            </div>
            <p class="text-xs text-slate-400 mt-1">Leave blank if you prefer not to disclose.</p>
          </div>
        </div>
      </div>

      <!-- Step 2: Categories -->
      <div class="form-step hidden lg:block card p-6 mb-6" data-step="2">
        <div class="flex items-start gap-3 mb-4">
          <span class="w-6 h-6 rounded-full bg-primary-navy text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">2</span>
          <div>
            <h3 class="text-sm font-bold text-primary-navy">Categories</h3>
            <p class="text-xs text-slate-500 mt-0.5">Select up to 3 categories that best describe this role.</p>
          </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-3 gap-3" id="category-chips">
          <?php foreach ($allCategories as $cat): $checked = in_array($cat['id'], $selectedCategoryIds, true); ?>
            <label class="category-chip flex items-center gap-2.5 text-sm font-medium border rounded-lg px-3 py-2.5 cursor-pointer <?= $checked ? 'border-gold bg-gold/5 text-gold' : 'border-slate-200 text-slate-600 hover:border-slate-300' ?>">
              <input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>" <?= $checked ? 'checked' : '' ?> class="hidden category-checkbox">
              <i class="bi <?= e($categoryIcons[$cat['slug']] ?? 'bi-briefcase') ?>"></i>
              <?= e($cat['name']) ?>
            </label>
          <?php endforeach; ?>
          <?php if (empty($allCategories)): ?>
            <p class="text-xs text-slate-400 col-span-full">No job categories have been set up yet.</p>
          <?php endif; ?>
        </div>
        <p id="category-limit-note" class="hidden text-xs text-amber-600 mt-2">You can select up to 3 categories.</p>
      </div>

      <!-- Step 3: Job Description -->
      <div class="form-step hidden lg:block card p-6 mb-6" data-step="3">
        <div class="flex items-start gap-3 mb-4">
          <span class="w-6 h-6 rounded-full bg-primary-navy text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">3</span>
          <div>
            <h3 class="text-sm font-bold text-primary-navy">Job Description</h3>
            <p class="text-xs text-slate-500 mt-0.5">Provide a clear overview of the role and responsibilities.</p>
          </div>
        </div>

        <div data-rich-editor="description"></div>
        <textarea id="description" name="description" class="hidden"><?= e($job['description'] ?? '') ?></textarea>
        <p class="text-xs text-slate-400 text-right mt-1.5" id="description-char-count">0 / 3000</p>

        <div class="mt-5">
          <label class="form-label">Key Responsibilities <span class="text-slate-400 font-normal">(optional)</span></label>
          <div data-rich-editor="responsibilities"></div>
          <textarea id="responsibilities" name="responsibilities" class="hidden"><?= e($job['responsibilities'] ?? '') ?></textarea>
        </div>

        <div class="mt-5">
          <label class="form-label">Requirements <span class="text-slate-400 font-normal">(optional)</span></label>
          <div data-rich-editor="requirements"></div>
          <textarea id="requirements" name="requirements" class="hidden"><?= e($job['requirements'] ?? '') ?></textarea>
        </div>
      </div>

      <!-- Step 4: How to Apply -->
      <div class="form-step hidden lg:block card p-6 mb-6" data-step="4">
        <div class="flex items-start gap-3 mb-4">
          <span class="w-6 h-6 rounded-full bg-primary-navy text-white text-xs font-bold flex items-center justify-center flex-shrink-0 mt-0.5">4</span>
          <div>
            <h3 class="text-sm font-bold text-primary-navy">How to Apply</h3>
            <p class="text-xs text-slate-500 mt-0.5">Let candidates know how they can apply.</p>
          </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
          <div>
            <label class="form-label" for="apply_type">Apply Method <span class="text-red-500">*</span></label>
            <select id="apply_type" name="apply_type" class="form-input">
              <option value="email" <?= ($job['apply_type'] ?? 'email') === 'email' ? 'selected' : '' ?>>Email (revealed on click)</option>
              <option value="link" <?= ($job['apply_type'] ?? '') === 'link' ? 'selected' : '' ?>>External Link</option>
            </select>
          </div>
          <div>
            <label class="form-label" for="apply_value">Email or URL <span class="text-red-500">*</span></label>
            <input type="text" id="apply_value" name="apply_value" class="form-input" placeholder="careers@company.com or https://careers.company.com" value="<?= e($job['apply_value'] ?? '') ?>">
          </div>
        </div>
      </div>

      <!-- Mobile-only step navigation -->
      <div class="lg:hidden flex items-center justify-between gap-3 mb-6" id="step-nav">
        <button type="button" id="step-back" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-4 !py-2 text-sm invisible">Back</button>
        <?php if ($showDraftOption): ?>
          <button type="submit" name="action" value="draft" class="text-xs text-slate-500 hover:text-gold">Save Draft</button>
        <?php endif; ?>
        <button type="button" id="step-next" class="btn-gold !px-4 !py-2 text-sm">Next</button>
      </div>

      <!-- Info banner + primary actions -->
      <div id="form-footer" class="hidden lg:block">
        <div class="card p-3 mb-4 flex items-start gap-2.5 !bg-sky-50 !border-sky-100">
          <i class="fa-solid fa-circle-info text-sky-500 mt-0.5"></i>
          <p class="text-xs text-sky-800">Jobs are reviewed by an admin before they appear on the public job board.</p>
        </div>
        <div class="flex items-center justify-end gap-3">
          <?php if ($showDraftOption): ?>
            <button type="submit" name="action" value="draft" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50">Save Draft</button>
          <?php endif; ?>
          <button type="submit" name="action" value="post" class="btn-gold flex items-center gap-2"><i class="fa-solid fa-paper-plane"></i> <?= e($primaryLabel) ?></button>
        </div>
      </div>
    </form>

    <!-- Sidebar -->
    <aside class="space-y-6 lg:sticky lg:top-[calc(var(--header-height)+1rem)]">
      <?php if ($postingMode === 'approval'): ?>
        <div class="card p-4 !bg-amber-50 !border-amber-100 flex items-start gap-3">
          <i class="fa-solid fa-shield-halved text-amber-500 mt-0.5"></i>
          <div>
            <h3 class="text-sm font-bold text-primary-navy">All job posts are reviewed by our admin</h3>
            <p class="text-xs text-amber-800 mt-0.5">Your job will be visible on the public job board once approved.</p>
          </div>
        </div>
      <?php endif; ?>

      <div class="card p-4">
        <h3 class="text-sm font-bold text-primary-navy mb-3 flex items-center gap-2"><i class="fa-solid fa-lightbulb text-amber-400"></i> Posting Tips</h3>
        <div class="space-y-4">
          <?php foreach ($postingTips as $tip): ?>
            <div class="flex items-start gap-2.5">
              <i class="fa-solid <?= e($tip['icon']) ?> text-gold mt-0.5"></i>
              <div>
                <p class="text-xs font-bold text-primary-navy"><?= e($tip['title']) ?></p>
                <p class="text-xs text-slate-500 mt-0.5"><?= e($tip['text']) ?></p>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </div>
    </aside>
  </div>
</div>

<script>
  (function () {
    var steps = document.querySelectorAll('.form-step');
    var dots = document.querySelectorAll('.step-dot');
    var connectors = document.querySelectorAll('.step-connector');
    var backBtn = document.getElementById('step-back');
    var nextBtn = document.getElementById('step-next');
    var footer = document.getElementById('form-footer');
    var form = document.getElementById('post-job-form');
    var total = steps.length;
    var current = 1;

    function render() {
      steps.forEach(function (el) {
        el.classList.toggle('hidden', parseInt(el.dataset.step, 10) !== current);
      });
      dots.forEach(function (d) {
        var reached = parseInt(d.dataset.dot, 10) <= current;
        d.classList.toggle('bg-primary-navy', reached);
        d.classList.toggle('text-white', reached);
        d.classList.toggle('bg-slate-200', !reached);
        d.classList.toggle('text-slate-500', !reached);
      });
      connectors.forEach(function (c) {
        var filled = parseInt(c.dataset.connector, 10) < current;
        c.classList.toggle('bg-primary-navy', filled);
        c.classList.toggle('bg-slate-200', !filled);
      });
      if (backBtn) backBtn.classList.toggle('invisible', current === 1);
      if (nextBtn) nextBtn.classList.toggle('hidden', current === total);
      if (footer) footer.classList.toggle('hidden', current !== total);
    }

    if (nextBtn) {
      nextBtn.addEventListener('click', function () {
        if (current < total) {
          current++;
          render();
          form.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    }
    if (backBtn) {
      backBtn.addEventListener('click', function () {
        if (current > 1) {
          current--;
          render();
        }
      });
    }
    render();

    // Categories — toggle chip styling, cap at 3 selections.
    var chips = document.querySelectorAll('.category-chip');
    var limitNote = document.getElementById('category-limit-note');
    function syncChip(chip) {
      var checked = chip.querySelector('.category-checkbox').checked;
      chip.classList.toggle('border-gold', checked);
      chip.classList.toggle('bg-gold/5', checked);
      chip.classList.toggle('text-gold', checked);
      chip.classList.toggle('border-slate-200', !checked);
      chip.classList.toggle('text-slate-600', !checked);
    }
    chips.forEach(function (chip) {
      var checkbox = chip.querySelector('.category-checkbox');
      chip.addEventListener('click', function (e) {
        e.preventDefault();
        var checkedCount = document.querySelectorAll('.category-checkbox:checked').length;
        if (!checkbox.checked && checkedCount >= 3) {
          limitNote.classList.remove('hidden');
          return;
        }
        limitNote.classList.add('hidden');
        checkbox.checked = !checkbox.checked;
        syncChip(chip);
      });
    });

    // Description char counter — wired once the shared rich editor (admin.js) has initialized.
    window.addEventListener('load', function () {
      var quill = window.richEditors && window.richEditors.description;
      var counter = document.getElementById('description-char-count');
      if (!quill || !counter) return;
      var MAX = 3000;
      function updateCount() {
        var len = quill.getText().replace(/\n$/, '').length;
        counter.textContent = len + ' / ' + MAX;
        counter.classList.toggle('text-red-500', len > MAX);
      }
      quill.on('text-change', updateCount);
      updateCount();
    });
  })();
</script>
