<a href="<?= e(url('admin/jobs')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to Jobs</a>

<h3 class="section-title text-xs mb-3">Awaiting Review (<?= count($jobs) ?>)</h3>
<div class="card overflow-x-auto">
  <table class="table-base">
    <thead><tr><th>Title</th><th>Company</th><th>Posted By</th><th>Submitted</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($jobs as $job): ?>
        <tr>
          <td class="font-medium text-primary-navy"><a href="<?= e(url('jobs/' . $job['id'])) ?>" target="_blank" class="hover:text-gold"><?= e($job['title']) ?></a></td>
          <td><?= e($job['company']) ?></td>
          <td><?= e($job['poster_name'] ?? 'Unknown') ?></td>
          <td class="text-slate-500"><?= e(time_ago($job['created_at'])) ?></td>
          <td class="text-right whitespace-nowrap">
            <form method="POST" action="<?= e(url('admin/jobs/' . $job['id'] . '/approve')) ?>" class="inline">
              <?= csrf_field() ?>
              <button type="submit" class="text-green-600 hover:underline mr-3">Approve</button>
            </form>
            <form method="POST" action="<?= e(url('admin/jobs/' . $job['id'] . '/reject')) ?>" class="inline" onsubmit="return confirm('Reject this job posting?');">
              <?= csrf_field() ?>
              <button type="submit" class="text-red-600 hover:underline mr-3">Reject</button>
            </form>
            <a href="<?= e(url('admin/jobs/' . $job['id'] . '/edit')) ?>" class="text-sky-600 hover:underline">Edit</a>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($jobs)): ?>
        <tr><td colspan="5" class="text-center text-slate-400 py-6">Nothing pending review.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
