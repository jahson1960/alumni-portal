<div class="flex justify-between items-center mb-4">
  <div>
    <p class="text-sm text-slate-500"><?= count($methods) ?> donation method<?= count($methods) === 1 ? '' : 's' ?></p>
    <p class="text-xs text-slate-400">Shown to every alumnus who clicks "Give to this Campaign" on the Give Back page, for any campaign.</p>
  </div>
  <a href="<?= e(url('admin/donation-methods/create')) ?>" class="btn-gold !px-4 !py-2 text-xs flex-shrink-0">+ New Method</a>
</div>

<div class="card overflow-x-auto">
  <table class="table-base">
    <thead><tr><th>Label</th><th>Bank</th><th>Account Number</th><th>Sort Code</th><th>Active</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($methods as $method): ?>
        <tr>
          <td class="font-medium text-primary-navy"><?= e($method['label']) ?></td>
          <td class="text-slate-500"><?= e($method['bank_name'] ?: '—') ?></td>
          <td class="text-slate-500"><?= e($method['account_number']) ?></td>
          <td class="text-slate-500"><?= e($method['sort_code'] ?: '—') ?></td>
          <td><?php if ($method['is_active']): ?><span class="badge-green">Active</span><?php else: ?><span class="text-slate-300">&mdash;</span><?php endif; ?></td>
          <td class="text-right whitespace-nowrap">
            <a href="<?= e(url('admin/donation-methods/' . $method['id'] . '/edit')) ?>" class="text-sky-600 hover:underline mr-3">Edit</a>
            <form method="POST" action="<?= e(url('admin/donation-methods/' . $method['id'] . '/delete')) ?>" class="inline" onsubmit="return confirm('Delete this donation method?');">
              <?= csrf_field() ?>
              <button type="submit" class="text-red-600 hover:underline">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($methods)): ?>
        <tr><td colspan="6" class="text-center text-slate-400 py-6">No donation methods yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
