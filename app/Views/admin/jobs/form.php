<?php $isEdit = $job !== null; ?>
<a href="<?= e(url('admin/jobs')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to Jobs</a>

<?php require dirname(dirname(__DIR__)) . '/partials/errors.php'; ?>

<form method="POST" action="<?= e($isEdit ? url('admin/jobs/' . $job['id']) : url('admin/jobs')) ?>" enctype="multipart/form-data" class="card p-6 max-w-2xl space-y-4">
  <?= csrf_field() ?>

  <div>
    <label class="form-label" for="title">Job Title</label>
    <input type="text" id="title" name="title" class="form-input" value="<?= e($job['title'] ?? '') ?>" required>
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="form-label" for="location">Location</label>
      <input type="text" id="location" name="location" class="form-input" value="<?= e($job['location'] ?? '') ?>">
    </div>
    <div>
      <label class="form-label" for="job_type">Job Type</label>
      <select id="job_type" name="job_type" class="form-input">
        <?php foreach (\App\Models\Job::JOB_TYPES as $type): ?>
          <option value="<?= e($type) ?>" <?= ($job['job_type'] ?? 'Full-time') === $type ? 'selected' : '' ?>><?= e($type) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="form-label" for="status">Status</label>
      <select id="status" name="status" class="form-input">
        <option value="open" <?= ($job['status'] ?? 'open') === 'open' ? 'selected' : '' ?>>Open</option>
        <option value="closed" <?= ($job['status'] ?? '') === 'closed' ? 'selected' : '' ?>>Closed</option>
      </select>
    </div>
    <div>
      <label class="form-label" for="closing_date">Closing Date</label>
      <input type="date" id="closing_date" name="closing_date" class="form-input" value="<?= e($job['closing_date'] ?? '') ?>">
      <p class="text-xs text-slate-400 mt-1">Job auto-closes and Apply is disabled once this date passes. Leave blank for no deadline.</p>
    </div>
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="form-label" for="experience_level">Experience Level</label>
      <select id="experience_level" name="experience_level" class="form-input">
        <option value="">Not specified</option>
        <?php foreach (\App\Models\Job::EXPERIENCE_LEVELS as $key => $label): ?>
          <option value="<?= e($key) ?>" <?= ($job['experience_level'] ?? '') === $key ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="form-label" for="work_mode">Work Mode</label>
      <select id="work_mode" name="work_mode" class="form-input">
        <?php foreach (\App\Models\Job::WORK_MODES as $key => $label): ?>
          <option value="<?= e($key) ?>" <?= ($job['work_mode'] ?? 'onsite') === $key ? 'selected' : '' ?>><?= e($label) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="form-label" for="salary_min">Salary Min (&#8358;)</label>
      <input type="number" id="salary_min" name="salary_min" class="form-input" min="0" step="1000" value="<?= e($job['salary_min'] ?? '') ?>">
    </div>
    <div>
      <label class="form-label" for="salary_max">Salary Max (&#8358;)</label>
      <input type="number" id="salary_max" name="salary_max" class="form-input" min="0" step="1000" value="<?= e($job['salary_max'] ?? '') ?>">
    </div>
  </div>

  <label class="flex items-center gap-2">
    <input type="checkbox" id="is_featured" name="is_featured" value="1" <?= !empty($job['is_featured']) ? 'checked' : '' ?> class="rounded border-slate-300 text-gold focus:ring-gold">
    <span class="text-sm text-slate-700">Mark as Featured</span>
  </label>

  <div>
    <label class="form-label">Categories</label>
    <div class="flex flex-wrap gap-3">
      <?php foreach ($allCategories as $cat): ?>
        <label class="flex items-center gap-1.5 text-sm text-slate-700 bg-slate-50 border border-slate-200 rounded px-2.5 py-1.5">
          <input type="checkbox" name="categories[]" value="<?= $cat['id'] ?>" <?= in_array($cat['id'], $selectedCategoryIds, true) ? 'checked' : '' ?> class="rounded border-slate-300 text-gold focus:ring-gold">
          <?= e($cat['name']) ?>
        </label>
      <?php endforeach; ?>
      <?php if (empty($allCategories)): ?>
        <p class="text-xs text-slate-400">No job categories yet. <a href="<?= e(url('admin/categories')) ?>" class="text-gold hover:underline">Add one</a>.</p>
      <?php endif; ?>
    </div>
  </div>

  <div>
    <label class="form-label">Description</label>
    <div data-rich-editor="description"></div>
    <textarea id="description" name="description" class="hidden"><?= e($job['description'] ?? '') ?></textarea>
    <p class="text-xs text-slate-400 mt-1">The "About the Role" narrative shown on the Overview tab.</p>
  </div>

  <div>
    <label class="form-label">Key Responsibilities <span class="text-slate-400 font-normal">(optional)</span></label>
    <div data-rich-editor="responsibilities"></div>
    <textarea id="responsibilities" name="responsibilities" class="hidden"><?= e($job['responsibilities'] ?? '') ?></textarea>
    <p class="text-xs text-slate-400 mt-1">A bullet list works best. Shown in its own tab on the job page.</p>
  </div>

  <div>
    <label class="form-label">Requirements <span class="text-slate-400 font-normal">(optional)</span></label>
    <div data-rich-editor="requirements"></div>
    <textarea id="requirements" name="requirements" class="hidden"><?= e($job['requirements'] ?? '') ?></textarea>
    <p class="text-xs text-slate-400 mt-1">A bullet list works best. Shown in its own tab on the job page.</p>
  </div>

  <div class="border-t border-slate-100 pt-4">
    <h3 class="section-title text-xs mb-1">About Company</h3>
    <p class="text-xs text-slate-400 mb-3">Pick a saved company to auto-fill its name, logo, and about section below &mdash; or fill them in manually.</p>

    <div class="mb-4">
      <label class="form-label" for="company_select">Select a Saved Company</label>
      <select id="company_select" class="form-input">
        <option value="">&mdash; Manual entry &mdash;</option>
        <?php foreach ($allCompanies as $c): ?>
          <option value="<?= $c['id'] ?>"
                  data-logo="<?= e($c['logo'] ?? '') ?>"
                  data-about="<?= e(json_encode($c['about'] ?? '')) ?>"
                  <?= !empty($job['company_id']) && (int) $job['company_id'] === (int) $c['id'] ? 'selected' : '' ?>>
            <?= e($c['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
    </div>

    <input type="hidden" id="company_id" name="company_id" value="<?= e($job['company_id'] ?? '') ?>">
    <input type="hidden" id="company_logo_url" name="company_logo_url" value="<?= (!empty($job['company_logo'])) ? e($job['company_logo']) : '' ?>">

    <div class="mb-4">
      <label class="form-label" for="company">Company Name</label>
      <input type="text" id="company" name="company" class="form-input" value="<?= e($job['company'] ?? '') ?>" required>
    </div>

    <div class="mb-4">
      <label class="form-label">Display Style</label>
      <div class="flex gap-4">
        <label class="flex items-center gap-2 text-sm text-slate-700">
          <input type="radio" name="logo_display_mode" value="logo" <?= ($job['logo_display_mode'] ?? 'logo') === 'logo' ? 'checked' : '' ?> class="text-gold focus:ring-gold">
          Use Logo (default)
        </label>
        <label class="flex items-center gap-2 text-sm text-slate-700">
          <input type="radio" name="logo_display_mode" value="badge" <?= ($job['logo_display_mode'] ?? '') === 'badge' ? 'checked' : '' ?> class="text-gold focus:ring-gold">
          Use Badge Colors
        </label>
      </div>
      <p class="text-xs text-slate-400 mt-1">"Use Logo" falls back to the badge automatically if no logo is uploaded.</p>
    </div>

    <div class="mb-4">
      <label class="form-label">Company Logo</label>
      <img id="company_logo_preview" src="<?= e($job['company_logo'] ?? '') ?>" alt="" class="w-16 h-16 object-contain rounded border border-slate-200 mb-2 bg-white p-1 <?= empty($job['company_logo']) ? 'hidden' : '' ?>">
      <input type="file" id="company_logo" name="company_logo" accept=".jpg,.jpeg,.png,.webp" class="text-sm block">
      <p class="text-xs text-slate-400 mt-1">Uploading a new file always overrides the selected company's logo. If none is set, the colored initials badge below is used.</p>
    </div>

    <div class="grid grid-cols-2 gap-4 mb-4">
      <div>
        <label class="form-label" for="company_bg_color">Badge Background Color</label>
        <input type="color" id="company_bg_color" name="company_bg_color" class="form-input h-10" value="<?= e($job['company_bg_color'] ?? '#f8fafc') ?>">
      </div>
      <div>
        <label class="form-label" for="company_text_color">Badge Text Color</label>
        <input type="color" id="company_text_color" name="company_text_color" class="form-input h-10" value="<?= e($job['company_text_color'] ?? '#091a2e') ?>">
      </div>
    </div>

    <div class="mb-4">
      <label class="form-label">About the Company</label>
      <div data-rich-editor="company_about"></div>
      <textarea id="company_about" name="company_about" class="hidden"><?= e($job['company_about'] ?? '') ?></textarea>
    </div>

    <label class="flex items-center gap-2">
      <input type="checkbox" name="save_company" value="1" class="rounded border-slate-300 text-gold focus:ring-gold">
      <span class="text-sm text-slate-700">Save these company details for future use</span>
    </label>
  </div>

  <div class="border-t border-slate-100 pt-4">
    <h3 class="section-title text-xs mb-3">How to Apply</h3>
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="form-label" for="apply_type">Apply Method</label>
        <select id="apply_type" name="apply_type" class="form-input">
          <option value="email" <?= ($job['apply_type'] ?? 'email') === 'email' ? 'selected' : '' ?>>Email (revealed on click)</option>
          <option value="link" <?= ($job['apply_type'] ?? '') === 'link' ? 'selected' : '' ?>>External Link</option>
        </select>
      </div>
      <div>
        <label class="form-label" for="apply_value">Email or URL</label>
        <input type="text" id="apply_value" name="apply_value" class="form-input" placeholder="careers@company.com or https://..." value="<?= e($job['apply_value'] ?? '') ?>" required>
      </div>
    </div>
  </div>

  <button type="submit" class="btn-gold"><?= $isEdit ? 'Save Changes' : 'Create Job Listing' ?></button>
</form>

<script>
  document.getElementById('company_select').addEventListener('change', function () {
    var option = this.options[this.selectedIndex];
    var companyIdField = document.getElementById('company_id');
    var logoUrlField = document.getElementById('company_logo_url');
    var logoPreview = document.getElementById('company_logo_preview');
    var nameField = document.getElementById('company');
    var fileField = document.getElementById('company_logo');

    if (!option.value) {
      companyIdField.value = '';
      return;
    }

    companyIdField.value = option.value;
    nameField.value = option.textContent.trim();

    var logo = option.dataset.logo || '';
    logoUrlField.value = logo;
    fileField.value = '';
    if (logo) {
      logoPreview.src = logo;
      logoPreview.classList.remove('hidden');
    } else {
      logoPreview.classList.add('hidden');
    }

    var about = '';
    try { about = JSON.parse(option.dataset.about); } catch (e) { about = ''; }
    var aboutTextarea = document.getElementById('company_about');
    aboutTextarea.value = about;
    if (window.refreshRichEditor) {
      window.refreshRichEditor('company_about', about);
    }
  });

  document.getElementById('company_logo').addEventListener('change', function () {
    if (this.files && this.files[0]) {
      document.getElementById('company_logo_url').value = '';
      var reader = new FileReader();
      var preview = document.getElementById('company_logo_preview');
      reader.onload = function (e) {
        preview.src = e.target.result;
        preview.classList.remove('hidden');
      };
      reader.readAsDataURL(this.files[0]);
    }
  });
</script>
