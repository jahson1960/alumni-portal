<div class="flex justify-between items-center mb-4">
  <p class="text-sm text-slate-500"><?= count($companies) ?> compan<?= count($companies) === 1 ? 'y' : 'ies' ?></p>
  <a href="<?= e(url('admin/companies/create')) ?>" class="btn-gold !px-4 !py-2 text-xs">+ New Company</a>
</div>

<div class="card overflow-x-auto">
  <table class="table-base">
    <thead>
      <tr>
        <th>Company</th>
        <th>Location</th>
        <th>Website</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($companies as $company): ?>
        <tr>
          <td class="font-medium text-primary-navy">
            <div class="flex items-center gap-2">
              <?php if (!empty($company['logo'])): ?>
                <img src="<?= e($company['logo']) ?>" alt="" class="w-7 h-7 object-contain rounded border border-slate-200 bg-white p-0.5">
              <?php endif; ?>
              <?= e($company['name']) ?>
            </div>
          </td>
          <td class="text-slate-500"><?= e($company['location']) ?></td>
          <td class="text-slate-500 truncate max-w-[200px]"><?= e($company['website']) ?></td>
          <td class="text-right whitespace-nowrap">
            <a href="<?= e(url('admin/companies/' . $company['id'] . '/edit')) ?>" class="text-sky-600 hover:underline mr-3">Edit</a>
            <form method="POST" action="<?= e(url('admin/companies/' . $company['id'] . '/delete')) ?>" class="inline" onsubmit="return confirm('Delete this company? Jobs linked to it will keep their own copy of the details but lose the link.');">
              <?= csrf_field() ?>
              <button type="submit" class="text-red-600 hover:underline">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($companies)): ?>
        <tr><td colspan="4" class="text-center text-slate-400 py-6">No companies yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
