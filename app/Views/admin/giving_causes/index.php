<div class="flex justify-between items-center mb-4">
  <p class="text-sm text-slate-500"><?= count($causes) ?> cause<?= count($causes) === 1 ? '' : 's' ?> &mdash; shown as individual links in the "Give Back" mega-menu</p>
  <a href="<?= e(url('admin/giving-causes/create')) ?>" class="btn-gold !px-4 !py-2 text-xs">+ New Cause</a>
</div>

<div class="card overflow-x-auto">
  <table class="table-base">
    <thead>
      <tr>
        <th>Title</th>
        <th>Group</th>
        <th>Slug</th>
        <th>Order</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php $groupLabels = ['support' => 'Support the Future', 'involve' => 'Get Involved', 'impact' => 'Create Impact']; ?>
      <?php foreach ($causes as $cause): ?>
        <tr>
          <td class="font-medium text-primary-navy"><i class="<?= e($cause['icon']) ?> mr-2 text-slate-400"></i><?= e($cause['title']) ?></td>
          <td><span class="badge-blue"><?= e($groupLabels[$cause['column_group']] ?? $cause['column_group']) ?></span></td>
          <td class="text-slate-500 font-mono text-xs"><?= e($cause['slug']) ?></td>
          <td class="text-slate-500"><?= (int) $cause['sort_order'] ?></td>
          <td class="text-right whitespace-nowrap">
            <a href="<?= e(url('give/causes/' . $cause['slug'])) ?>" target="_blank" class="text-slate-400 hover:text-gold mr-3" title="View page"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
            <a href="<?= e(url('admin/giving-causes/' . $cause['id'] . '/edit')) ?>" class="text-sky-600 hover:underline mr-3">Edit</a>
            <form method="POST" action="<?= e(url('admin/giving-causes/' . $cause['id'] . '/delete')) ?>" class="inline" onsubmit="return confirm('Delete this cause? Its menu link and page will stop working.');">
              <?= csrf_field() ?>
              <button type="submit" class="text-red-600 hover:underline">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($causes)): ?>
        <tr><td colspan="5" class="text-center text-slate-400 py-6">No causes yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
