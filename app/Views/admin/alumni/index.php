<div class="flex justify-between items-center mb-4">
  <p class="text-sm text-slate-500"><?= count($alumni) ?> alumni account<?= count($alumni) === 1 ? '' : 's' ?></p>
  <a href="<?= e(url('admin/register-alumni')) ?>" class="btn-gold !px-4 !py-2 text-xs">+ Register Alumni</a>
</div>

<div class="card overflow-x-auto">
  <table class="table-base">
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Company</th>
        <th>Status</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($alumni as $alum): ?>
        <tr>
          <td>
            <a href="<?= e(url('admin/alumni/' . $alum['id'])) ?>" class="flex items-center gap-2 font-medium text-primary-navy hover:text-gold">
              <?= avatar_html($alum, 'w-7 h-7') ?> <?= e($alum['name']) ?>
            </a>
          </td>
          <td class="text-slate-500"><?= e($alum['email']) ?></td>
          <td class="text-slate-500"><?= e($alum['company']) ?></td>
          <td><span class="<?= $alum['status'] === 'active' ? 'badge-blue' : 'badge-gold' ?>"><?= e($alum['status']) ?></span></td>
          <td class="text-right whitespace-nowrap">
            <?php if ($alum['status'] === 'active'): ?>
              <form method="POST" action="<?= e(url('admin/alumni/' . $alum['id'] . '/suspend')) ?>" class="inline">
                <?= csrf_field() ?>
                <button type="submit" class="text-amber-600 hover:underline mr-3">Suspend</button>
              </form>
            <?php else: ?>
              <form method="POST" action="<?= e(url('admin/alumni/' . $alum['id'] . '/activate')) ?>" class="inline">
                <?= csrf_field() ?>
                <button type="submit" class="text-green-600 hover:underline mr-3">Activate</button>
              </form>
            <?php endif; ?>
            <form method="POST" action="<?= e(url('admin/alumni/' . $alum['id'] . '/delete')) ?>" class="inline" onsubmit="return confirm('Delete this alumni account? This cannot be undone.');">
              <?= csrf_field() ?>
              <button type="submit" class="text-red-600 hover:underline">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($alumni)): ?>
        <tr><td colspan="5" class="text-center text-slate-400 py-6">No alumni accounts yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
