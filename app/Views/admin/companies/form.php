<?php $isEdit = $company !== null; ?>
<a href="<?= e(url('admin/companies')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to Companies</a>

<?php require dirname(dirname(__DIR__)) . '/partials/errors.php'; ?>

<form method="POST" action="<?= e($isEdit ? url('admin/companies/' . $company['id']) : url('admin/companies')) ?>" enctype="multipart/form-data" class="card p-6 max-w-2xl space-y-4">
  <?= csrf_field() ?>

  <div>
    <label class="form-label" for="name">Company Name</label>
    <input type="text" id="name" name="name" class="form-input" value="<?= e($company['name'] ?? '') ?>" required>
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="form-label" for="website">Website</label>
      <input type="url" id="website" name="website" class="form-input" placeholder="https://..." value="<?= e($company['website'] ?? '') ?>">
    </div>
    <div>
      <label class="form-label" for="location">Location</label>
      <input type="text" id="location" name="location" class="form-input" value="<?= e($company['location'] ?? '') ?>">
    </div>
  </div>

  <div>
    <label class="form-label" for="industry">Industry</label>
    <input type="text" id="industry" name="industry" class="form-input" placeholder="e.g. Technology, Banking & Finance" value="<?= e($company['industry'] ?? '') ?>">
  </div>

  <div>
    <label class="form-label">Logo</label>
    <?php if (!empty($company['logo'])): ?>
      <img src="<?= e($company['logo']) ?>" alt="" class="w-16 h-16 object-contain rounded border border-slate-200 mb-2 bg-white p-1">
    <?php endif; ?>
    <input type="file" name="logo" accept=".jpg,.jpeg,.png,.webp" class="text-sm block">
  </div>

  <div>
    <label class="form-label">About the Company</label>
    <div data-rich-editor="about"></div>
    <textarea id="about" name="about" class="hidden"><?= e($company['about'] ?? '') ?></textarea>
  </div>

  <button type="submit" class="btn-gold"><?= $isEdit ? 'Save Changes' : 'Add Company' ?></button>
</form>
