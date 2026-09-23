<?php $isEdit = $method !== null; ?>
<a href="<?= e(url('admin/donation-methods')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to Donation Methods</a>

<?php require dirname(dirname(__DIR__)) . '/partials/errors.php'; ?>

<form method="POST" action="<?= e($isEdit ? url('admin/donation-methods/' . $method['id']) : url('admin/donation-methods')) ?>" class="card p-6 max-w-2xl space-y-4">
  <?= csrf_field() ?>

  <div>
    <label class="form-label" for="label">Label</label>
    <input type="text" id="label" name="label" class="form-input" placeholder="e.g. Naira Account" value="<?= e($method['label'] ?? '') ?>" required>
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="form-label" for="bank_name">Bank Name</label>
      <input type="text" id="bank_name" name="bank_name" class="form-input" value="<?= e($method['bank_name'] ?? '') ?>">
    </div>
    <div>
      <label class="form-label" for="account_name">Account Name</label>
      <input type="text" id="account_name" name="account_name" class="form-input" value="<?= e($method['account_name'] ?? '') ?>">
    </div>
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="form-label" for="account_number">Account Number</label>
      <input type="text" id="account_number" name="account_number" class="form-input" value="<?= e($method['account_number'] ?? '') ?>" required>
    </div>
    <div>
      <label class="form-label" for="sort_code">Sort Code (if any)</label>
      <input type="text" id="sort_code" name="sort_code" class="form-input" placeholder="e.g. 04-00-04" value="<?= e($method['sort_code'] ?? '') ?>">
    </div>
  </div>

  <div>
    <label class="form-label" for="sort_order">Display Order</label>
    <input type="number" id="sort_order" name="sort_order" class="form-input max-w-[140px]" value="<?= e($method['sort_order'] ?? '0') ?>">
    <p class="text-xs text-slate-400 mt-1">Lower numbers show first.</p>
  </div>

  <label class="flex items-center gap-2">
    <input type="checkbox" id="is_active" name="is_active" value="1" <?= !isset($method) || !empty($method['is_active']) ? 'checked' : '' ?> class="rounded border-slate-300 text-gold focus:ring-gold">
    <span class="text-sm text-slate-700">Active (shown to alumni)</span>
  </label>

  <button type="submit" class="btn-gold"><?= $isEdit ? 'Save Changes' : 'Create Donation Method' ?></button>
</form>
