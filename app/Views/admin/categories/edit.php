<?php $backTo = $category['type'] === 'news' ? ['admin/news-categories', 'News Categories'] : ['admin/job-categories', 'Job Categories']; ?>
<a href="<?= e(url($backTo[0])) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to <?= e($backTo[1]) ?></a>

<?php require dirname(dirname(__DIR__)) . '/partials/errors.php'; ?>

<form method="POST" action="<?= e(url('admin/categories/' . $category['id'])) ?>" class="card p-6 max-w-md space-y-4">
  <?= csrf_field() ?>
  <div>
    <label class="form-label" for="name">Name</label>
    <input type="text" id="name" name="name" class="form-input" value="<?= e($category['name']) ?>" required>
    <p class="text-xs text-slate-400 mt-1"><?= $category['type'] === 'job' ? 'Job' : 'News' ?> category</p>
  </div>
  <button type="submit" class="btn-gold">Save Changes</button>
</form>
