<p class="text-sm text-slate-500 mb-4">Profiles flagged by alumni via the "Report Profile" action on a profile page.</p>

<div class="card overflow-x-auto">
  <table class="table-base">
    <thead>
      <tr>
        <th>Reported Profile</th>
        <th>Reason</th>
        <th>Notes</th>
        <th>Reported By</th>
        <th>Status</th>
        <th>Date</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($reports as $report): ?>
        <tr>
          <td class="font-medium text-primary-navy"><a href="<?= e(url('alumni/' . $report['reported_id'])) ?>" class="hover:text-gold"><?= e($report['reported_name']) ?></a></td>
          <td class="text-sm text-slate-600"><?= e($report['reason']) ?></td>
          <td class="text-xs text-slate-500 max-w-xs truncate" title="<?= e((string) $report['notes']) ?>"><?= e($report['notes'] ?: '—') ?></td>
          <td class="text-sm text-slate-600"><?= e($report['reporter_name']) ?></td>
          <td>
            <?php if ($report['status'] === 'resolved'): ?>
              <span class="badge-green">Resolved</span>
            <?php elseif ($report['status'] === 'dismissed'): ?>
              <span class="badge bg-slate-100 text-slate-500">Dismissed</span>
            <?php else: ?>
              <span class="badge-gold">Pending</span>
            <?php endif; ?>
          </td>
          <td class="text-xs text-slate-400"><?= e(format_date($report['created_at'], 'M j, Y')) ?></td>
          <td class="text-right whitespace-nowrap">
            <?php if ($report['status'] === 'pending'): ?>
              <form method="POST" action="<?= e(url('admin/profile-reports/' . $report['id'] . '/resolve')) ?>" class="inline">
                <?= csrf_field() ?>
                <button type="submit" class="text-xs text-green-600 hover:underline mr-3">Mark Resolved</button>
              </form>
              <form method="POST" action="<?= e(url('admin/profile-reports/' . $report['id'] . '/dismiss')) ?>" class="inline">
                <?= csrf_field() ?>
                <button type="submit" class="text-xs text-slate-500 hover:underline">Dismiss</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$reports): ?>
        <tr><td colspan="7" class="text-center text-sm text-slate-400 py-6">No profile reports yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
