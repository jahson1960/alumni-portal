<h3 class="section-title text-xs mb-3">Pending Review (<?= count($pending) ?>)</h3>
<div class="card overflow-x-auto mb-8">
  <table class="table-base">
    <thead><tr><th>Title</th><th>Author</th><th>Category</th><th>Submitted</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($pending as $article): ?>
        <tr>
          <td class="font-medium text-primary-navy"><?= e($article['title']) ?></td>
          <td><?= e($article['author_name']) ?></td>
          <td><?= e(article_category_label($article['category'])) ?></td>
          <td class="text-slate-500"><?= e(format_date($article['created_at'])) ?></td>
          <td class="text-right whitespace-nowrap">
            <form method="POST" action="<?= e(url('admin/articles/' . $article['id'] . '/publish')) ?>" class="inline">
              <?= csrf_field() ?>
              <button type="submit" class="text-green-600 hover:underline mr-3">Publish</button>
            </form>
            <form method="POST" action="<?= e(url('admin/articles/' . $article['id'] . '/delete')) ?>" class="inline" onsubmit="return confirm('Delete this submission?');">
              <?= csrf_field() ?>
              <button type="submit" class="text-red-600 hover:underline">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($pending)): ?>
        <tr><td colspan="5" class="text-center text-slate-400 py-6">Nothing pending review.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<h3 class="section-title text-xs mb-3">Published (<?= count($published) ?>)</h3>
<div class="card overflow-x-auto">
  <table class="table-base">
    <thead><tr><th>Title</th><th>Author</th><th>Category</th><th>Published</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($published as $article): ?>
        <tr>
          <td class="font-medium text-primary-navy"><?= e($article['title']) ?></td>
          <td><?= e($article['author_name']) ?></td>
          <td><?= e(article_category_label($article['category'])) ?></td>
          <td class="text-slate-500"><?= e(format_date($article['published_at'])) ?></td>
          <td class="text-right whitespace-nowrap">
            <form method="POST" action="<?= e(url('admin/articles/' . $article['id'] . '/delete')) ?>" class="inline" onsubmit="return confirm('Delete this article?');">
              <?= csrf_field() ?>
              <button type="submit" class="text-red-600 hover:underline">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($published)): ?>
        <tr><td colspan="5" class="text-center text-slate-400 py-6">Nothing published yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
