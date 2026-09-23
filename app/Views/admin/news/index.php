<div class="flex justify-between items-center mb-4">
  <p class="text-sm text-slate-500"><?= count($posts) ?> post<?= count($posts) === 1 ? '' : 's' ?></p>
  <a href="<?= e(url('admin/news/create')) ?>" class="btn-gold !px-4 !py-2 text-xs">+ New Post</a>
</div>

<div class="card overflow-x-auto">
  <table class="table-base">
    <thead>
      <tr>
        <th>Title</th>
        <th>Categories</th>
        <th>Status</th>
        <th>Published</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($posts as $post): ?>
        <tr>
          <td class="font-medium text-primary-navy"><?= e($post['title']) ?></td>
          <td class="text-slate-500 text-xs"><?= e(implode(', ', array_column(\App\Models\Category::forNews((int) $post['id']), 'name')) ?: '—') ?></td>
          <td>
            <span class="<?= $post['status'] === 'published' ? 'badge-blue' : 'badge-gold' ?>"><?= e($post['status']) ?></span>
          </td>
          <td class="text-slate-500"><?= e(format_date($post['published_at'])) ?></td>
          <td class="text-right whitespace-nowrap">
            <a href="<?= e(url('admin/news/' . $post['id'] . '/edit')) ?>" class="text-sky-600 hover:underline mr-3">Edit</a>
            <form method="POST" action="<?= e(url('admin/news/' . $post['id'] . '/delete')) ?>" class="inline" onsubmit="return confirm('Delete this news post?');">
              <?= csrf_field() ?>
              <button type="submit" class="text-red-600 hover:underline">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($posts)): ?>
        <tr><td colspan="5" class="text-center text-slate-400 py-6">No news posts yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
