<?php $tabs = admin_giving_tabs(); require dirname(__DIR__, 2) . '/partials/admin_group_tabs.php'; ?>

<div class="mb-4">
  <h2 class="text-sm font-bold text-primary-navy">Donation Confirmations</h2>
  <p class="text-xs text-slate-400">Self-reported by alumni after transferring funds. No payment is processed here — use these to reconcile and update each campaign's raised amount from Giving Campaigns.</p>
</div>

<?php require dirname(__DIR__, 2) . '/partials/admin_pagination.php'; ?>

<div class="card overflow-x-auto">
  <table class="table-base">
    <thead><tr><th>Alumnus</th><th>Campaign</th><th>Date</th></tr></thead>
    <tbody>
      <?php foreach ($confirmations as $row): ?>
        <tr>
          <td class="font-medium text-primary-navy"><?= e($row['user_name']) ?><span class="block text-xs text-slate-400 font-normal"><?= e($row['user_email']) ?></span></td>
          <td class="text-slate-600"><?= e($row['campaign_title']) ?></td>
          <td class="text-slate-500"><?= e(format_date($row['created_at'], 'M j, Y g:i A')) ?></td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($confirmations)): ?>
        <tr><td colspan="3" class="text-center text-slate-400 py-6">No donation confirmations yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
