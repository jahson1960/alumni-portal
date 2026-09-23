<div class="flex justify-between items-center mb-4">
  <div>
    <p class="text-sm text-slate-500"><?= count($rows) ?> roster entr<?= count($rows) === 1 ? 'y' : 'ies' ?></p>
    <p class="text-xs text-slate-400">A matric number, graduation year and cohort must match a row here before someone can register. Each row can only be claimed by one account.</p>
  </div>
  <a href="<?= e(url('admin/alumni-roster/create')) ?>" class="btn-gold !px-4 !py-2 text-xs flex-shrink-0">+ New Entry</a>
</div>

<div class="card overflow-x-auto">
  <table class="table-base">
    <thead><tr><th>Matric Number</th><th>Full Name</th><th>Graduation Year</th><th>Cohort</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($rows as $row): ?>
        <tr>
          <td class="font-medium text-primary-navy"><?= e($row['matric_number']) ?></td>
          <td class="text-slate-500"><?= e($row['full_name'] ?: '—') ?></td>
          <td class="text-slate-500"><?= e($row['graduation_year']) ?></td>
          <td class="text-slate-500"><?= e($row['cohort']) ?></td>
          <td><?php if ($row['claimed_by_user_id']): ?><span class="badge-blue">Claimed</span><?php else: ?><span class="badge bg-slate-100 text-slate-500">Available</span><?php endif; ?></td>
          <td class="text-right whitespace-nowrap">
            <a href="<?= e(url('admin/alumni-roster/' . $row['id'] . '/edit')) ?>" class="text-sky-600 hover:underline mr-3">Edit</a>
            <form method="POST" action="<?= e(url('admin/alumni-roster/' . $row['id'] . '/delete')) ?>" class="inline" onsubmit="return confirm('Delete this roster entry?');">
              <?= csrf_field() ?>
              <button type="submit" class="text-red-600 hover:underline">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($rows)): ?>
        <tr><td colspan="6" class="text-center text-slate-400 py-6">No roster entries yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
