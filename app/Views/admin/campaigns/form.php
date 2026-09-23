<?php $isEdit = $campaign !== null; ?>
<a href="<?= e(url('admin/campaigns')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to Campaigns</a>

<?php require dirname(dirname(__DIR__)) . '/partials/errors.php'; ?>

<form method="POST" action="<?= e($isEdit ? url('admin/campaigns/' . $campaign['id']) : url('admin/campaigns')) ?>" enctype="multipart/form-data" class="card p-6 max-w-xl space-y-4">
  <?= csrf_field() ?>

  <div>
    <label class="form-label" for="title">Title</label>
    <input type="text" id="title" name="title" class="form-input" value="<?= e($campaign['title'] ?? '') ?>" required>
  </div>

  <div>
    <label class="form-label" for="description">Description</label>
    <textarea id="description" name="description" rows="4" class="form-textarea"><?= e($campaign['description'] ?? '') ?></textarea>
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="form-label" for="goal_amount">Goal Amount (&#8358;)</label>
      <input type="number" id="goal_amount" name="goal_amount" class="form-input" min="0" step="1000" value="<?= e($campaign['goal_amount'] ?? '0') ?>">
    </div>
    <div>
      <label class="form-label" for="raised_amount">Raised So Far (&#8358;)</label>
      <input type="number" id="raised_amount" name="raised_amount" class="form-input" min="0" step="1000" value="<?= e($campaign['raised_amount'] ?? '0') ?>">
    </div>
  </div>

  <div>
    <label class="form-label" for="status">Status</label>
    <select id="status" name="status" class="form-input max-w-xs">
      <option value="active" <?= ($campaign['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
      <option value="closed" <?= ($campaign['status'] ?? '') === 'closed' ? 'selected' : '' ?>>Closed</option>
    </select>
  </div>

  <div>
    <label class="form-label">Image</label>
    <?php if (!empty($campaign['image'])): ?>
      <img src="<?= e($campaign['image']) ?>" alt="" class="w-32 h-20 object-cover rounded mb-2">
    <?php endif; ?>
    <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" class="text-sm block">
  </div>

  <button type="submit" class="btn-gold"><?= $isEdit ? 'Save Changes' : 'Create Campaign' ?></button>
</form>
