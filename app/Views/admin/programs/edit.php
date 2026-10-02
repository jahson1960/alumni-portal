<a href="<?= e(url('admin/programs')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to Programs</a>

<?php require dirname(dirname(__DIR__)) . '/partials/errors.php'; ?>

<form method="POST" action="<?= e(url('admin/programs/' . $program['id'])) ?>" class="card p-6 max-w-md space-y-4">
  <?= csrf_field() ?>
  <div>
    <label class="form-label" for="name">Name</label>
    <input type="text" id="name" name="name" class="form-input" value="<?= e($program['name']) ?>" required>
  </div>
  <button type="submit" class="btn-gold">Save Changes</button>
</form>
