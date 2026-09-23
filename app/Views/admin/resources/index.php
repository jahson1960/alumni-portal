<div class="flex justify-between items-center mb-4">
  <p class="text-sm text-slate-500"><?= count($resources) ?> resource<?= count($resources) === 1 ? '' : 's' ?></p>
  <a href="<?= e(url('admin/resources/create')) ?>" class="btn-gold !px-4 !py-2 text-xs">+ New Resource</a>
</div>

<div class="card overflow-x-auto">
  <table class="table-base">
    <thead>
      <tr>
        <th>Title</th>
        <th>Category</th>
        <th>Featured</th>
        <th>Link</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($resources as $resource): ?>
        <tr>
          <td class="font-medium text-primary-navy"><?= e($resource['title']) ?></td>
          <td><?= e($resource['category']) ?></td>
          <td><?php if (!empty($resource['is_featured'])): ?><span class="badge-green">Featured</span><?php else: ?><span class="text-slate-300">&mdash;</span><?php endif; ?></td>
          <td class="text-slate-500 truncate max-w-[200px]"><?= e($resource['link']) ?></td>
          <td class="text-right whitespace-nowrap">
            <a href="<?= e(url('admin/resources/' . $resource['id'] . '/edit')) ?>" class="text-sky-600 hover:underline mr-3">Edit</a>
            <form method="POST" action="<?= e(url('admin/resources/' . $resource['id'] . '/delete')) ?>" class="inline" onsubmit="return confirm('Delete this resource?');">
              <?= csrf_field() ?>
              <button type="submit" class="text-red-600 hover:underline">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($resources)): ?>
        <tr><td colspan="5" class="text-center text-slate-400 py-6">No resources yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
