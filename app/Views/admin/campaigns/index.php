<div class="flex justify-between items-center mb-4">
  <p class="text-sm text-slate-500"><?= count($campaigns) ?> campaign<?= count($campaigns) === 1 ? '' : 's' ?></p>
  <a href="<?= e(url('admin/campaigns/create')) ?>" class="btn-gold !px-4 !py-2 text-xs">+ New Campaign</a>
</div>

<div class="card overflow-x-auto">
  <table class="table-base">
    <thead><tr><th>Title</th><th>Progress</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($campaigns as $campaign): $pct = $campaign['goal_amount'] > 0 ? min(100, round($campaign['raised_amount'] / $campaign['goal_amount'] * 100)) : 0; ?>
        <tr>
          <td class="font-medium text-primary-navy"><?= e($campaign['title']) ?></td>
          <td class="text-slate-500">&#8358;<?= number_format((float) $campaign['raised_amount']) ?> / &#8358;<?= number_format((float) $campaign['goal_amount']) ?> (<?= $pct ?>%)</td>
          <td><span class="<?= $campaign['status'] === 'active' ? 'badge-blue' : 'badge-gold' ?>"><?= e($campaign['status']) ?></span></td>
          <td class="text-right whitespace-nowrap">
            <a href="<?= e(url('admin/campaigns/' . $campaign['id'] . '/edit')) ?>" class="text-sky-600 hover:underline mr-3">Edit</a>
            <form method="POST" action="<?= e(url('admin/campaigns/' . $campaign['id'] . '/delete')) ?>" class="inline" onsubmit="return confirm('Delete this campaign?');">
              <?= csrf_field() ?>
              <button type="submit" class="text-red-600 hover:underline">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($campaigns)): ?>
        <tr><td colspan="4" class="text-center text-slate-400 py-6">No campaigns yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
