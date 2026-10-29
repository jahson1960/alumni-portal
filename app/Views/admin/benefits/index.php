<div class="flex justify-between items-center mb-4">
  <h2 class="text-sm font-bold text-primary-navy">Benefits</h2>
  <a href="<?= e(url('admin/benefits/create')) ?>" class="btn-gold !px-4 !py-2 text-xs">+ New Benefit</a>
</div>

<?php require dirname(__DIR__, 2) . '/partials/admin_pagination.php'; ?>

<div class="card overflow-x-auto">
  <table class="table-base">
    <thead>
      <tr>
        <th>Title</th>
        <th>Category</th>
        <th>Link</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($benefits as $benefit): ?>
        <tr>
          <td class="font-medium text-primary-navy"><?= e($benefit['title']) ?></td>
          <td><?= e($benefit['category']) ?></td>
          <td class="text-slate-500 truncate max-w-[200px]"><?= e($benefit['link']) ?></td>
          <td class="text-right whitespace-nowrap">
            <a href="<?= e(url('admin/benefits/' . $benefit['id'] . '/edit')) ?>" class="text-sky-600 hover:underline mr-3">Edit</a>
            <form method="POST" action="<?= e(url('admin/benefits/' . $benefit['id'] . '/delete')) ?>" class="inline" onsubmit="return confirm('Delete this benefit?');">
              <?= csrf_field() ?>
              <button type="submit" class="text-red-600 hover:underline">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($benefits)): ?>
        <tr><td colspan="4" class="text-center text-slate-400 py-6">No benefits yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
