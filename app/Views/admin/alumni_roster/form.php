<?php $isEdit = $row !== null; ?>
<a href="<?= e(url('admin/alumni-roster')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to Alumni Roster</a>

<?php require dirname(dirname(__DIR__)) . '/partials/errors.php'; ?>

<form method="POST" action="<?= e($isEdit ? url('admin/alumni-roster/' . $row['id']) : url('admin/alumni-roster')) ?>" class="card p-6 max-w-2xl space-y-4">
  <?= csrf_field() ?>

  <div>
    <label class="form-label" for="matric_number">Matric Number</label>
    <input type="text" id="matric_number" name="matric_number" class="form-input" value="<?= e($row['matric_number'] ?? '') ?>" required>
  </div>

  <div>
    <label class="form-label" for="full_name">Full Name (optional, for your reference)</label>
    <input type="text" id="full_name" name="full_name" class="form-input" value="<?= e($row['full_name'] ?? '') ?>">
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="form-label" for="graduation_year">Graduation Year</label>
      <input type="number" id="graduation_year" name="graduation_year" class="form-input" min="1990" max="2100" value="<?= e($row['graduation_year'] ?? '') ?>" required>
    </div>
    <div>
      <label class="form-label" for="cohort">Cohort</label>
      <input type="text" id="cohort" name="cohort" class="form-input" placeholder="e.g. MBA 2019" value="<?= e($row['cohort'] ?? '') ?>" required>
    </div>
  </div>

  <?php if ($isEdit && $row['claimed_by_user_id']): ?>
    <p class="text-xs text-amber-600 bg-amber-50 border border-amber-100 rounded-lg p-3"><i class="fa-solid fa-circle-info"></i> This entry has already been claimed by a registered account.</p>
  <?php endif; ?>

  <button type="submit" class="btn-gold"><?= $isEdit ? 'Save Changes' : 'Add to Roster' ?></button>
</form>
