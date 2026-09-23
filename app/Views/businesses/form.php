<?php $isEdit = $business !== null; ?>
<div class="max-w-[1280px] mx-auto px-4 md:px-8 py-8">
  <a href="<?= e(url('businesses/mine')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to My Businesses</a>

  <?php require dirname(__DIR__) . '/partials/errors.php'; ?>

  <form method="POST" action="<?= e($isEdit ? url('businesses/' . $business['id']) : url('businesses')) ?>" enctype="multipart/form-data" class="card p-6 max-w-xl space-y-4">
    <?= csrf_field() ?>

    <div>
      <label class="form-label" for="name">Business Name</label>
      <input type="text" id="name" name="name" class="form-input" value="<?= e($business['name'] ?? '') ?>" required>
    </div>

    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="form-label" for="category">Category</label>
        <input type="text" id="category" name="category" class="form-input" placeholder="e.g. Technology" value="<?= e($business['category'] ?? '') ?>">
      </div>
      <div>
        <label class="form-label" for="business_type">Business Type</label>
        <input type="text" id="business_type" name="business_type" class="form-input" placeholder="e.g. Startup, SME" value="<?= e($business['business_type'] ?? '') ?>">
      </div>
    </div>

    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="form-label" for="location">Location</label>
        <input type="text" id="location" name="location" class="form-input" value="<?= e($business['location'] ?? '') ?>">
      </div>
      <div>
        <label class="form-label" for="founded_year">Founded Year</label>
        <input type="number" id="founded_year" name="founded_year" class="form-input" min="1900" max="2100" placeholder="e.g. 2020" value="<?= e($business['founded_year'] ?? '') ?>">
      </div>
    </div>

    <div>
      <label class="form-label" for="website">Website</label>
      <input type="url" id="website" name="website" class="form-input" placeholder="https://..." value="<?= e($business['website'] ?? '') ?>">
    </div>

    <div>
      <label class="form-label">Logo</label>
      <?php if (!empty($business['logo'])): ?>
        <img src="<?= e($business['logo']) ?>" alt="" class="w-16 h-16 object-contain rounded border border-slate-200 mb-2 bg-white p-1">
      <?php endif; ?>
      <input type="file" name="logo" accept=".jpg,.jpeg,.png,.webp" class="text-sm block">
    </div>

    <div>
      <label class="form-label" for="description">Description</label>
      <textarea id="description" name="description" rows="4" class="form-textarea"><?= e($business['description'] ?? '') ?></textarea>
    </div>

    <button type="submit" class="btn-gold"><?= $isEdit ? 'Save Changes' : 'Add Business' ?></button>
  </form>
</div>
