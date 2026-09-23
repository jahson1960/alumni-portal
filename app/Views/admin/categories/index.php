<div class="grid grid-cols-1 md:grid-cols-2 gap-6">

  <div>
    <h3 class="section-title text-xs mb-3">Job Categories</h3>
    <div class="card p-4 mb-4">
      <form method="POST" action="<?= e(url('admin/categories')) ?>" class="flex gap-2">
        <?= csrf_field() ?>
        <input type="hidden" name="type" value="job">
        <input type="text" name="name" placeholder="e.g. Technology" class="form-input flex-1" required>
        <button type="submit" class="btn-gold !px-4 !py-2 text-xs whitespace-nowrap">+ Add</button>
      </form>
    </div>
    <div class="card overflow-x-auto">
      <table class="table-base">
        <tbody>
          <?php foreach ($jobCategories as $cat): ?>
            <tr>
              <td class="font-medium text-primary-navy"><?= e($cat['name']) ?></td>
              <td class="text-right whitespace-nowrap">
                <a href="<?= e(url('admin/categories/' . $cat['id'] . '/edit')) ?>" class="text-sky-600 hover:underline mr-3">Edit</a>
                <form method="POST" action="<?= e(url('admin/categories/' . $cat['id'] . '/delete')) ?>" class="inline" onsubmit="return confirm('Delete this category? It will be removed from any jobs using it.');">
                  <?= csrf_field() ?>
                  <button type="submit" class="text-red-600 hover:underline">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($jobCategories)): ?>
            <tr><td class="text-center text-slate-400 py-6">No job categories yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

  <div>
    <h3 class="section-title text-xs mb-3">News Categories</h3>
    <div class="card p-4 mb-4">
      <form method="POST" action="<?= e(url('admin/categories')) ?>" class="flex gap-2">
        <?= csrf_field() ?>
        <input type="hidden" name="type" value="news">
        <input type="text" name="name" placeholder="e.g. Alumni Stories" class="form-input flex-1" required>
        <button type="submit" class="btn-gold !px-4 !py-2 text-xs whitespace-nowrap">+ Add</button>
      </form>
    </div>
    <div class="card overflow-x-auto">
      <table class="table-base">
        <tbody>
          <?php foreach ($newsCategories as $cat): ?>
            <tr>
              <td class="font-medium text-primary-navy"><?= e($cat['name']) ?></td>
              <td class="text-right whitespace-nowrap">
                <a href="<?= e(url('admin/categories/' . $cat['id'] . '/edit')) ?>" class="text-sky-600 hover:underline mr-3">Edit</a>
                <form method="POST" action="<?= e(url('admin/categories/' . $cat['id'] . '/delete')) ?>" class="inline" onsubmit="return confirm('Delete this category? It will be removed from any news posts using it.');">
                  <?= csrf_field() ?>
                  <button type="submit" class="text-red-600 hover:underline">Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          <?php if (empty($newsCategories)): ?>
            <tr><td class="text-center text-slate-400 py-6">No news categories yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>
