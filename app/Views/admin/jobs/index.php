<div class="flex justify-between items-center mb-4">
  <p class="text-sm text-slate-500"><?= count($jobs) ?> listing<?= count($jobs) === 1 ? '' : 's' ?></p>
  <div class="flex items-center gap-3">
    <?php if ($pendingCount > 0): ?>
      <a href="<?= e(url('admin/jobs/pending')) ?>" class="btn bg-amber-100 text-amber-700 hover:bg-amber-200 !px-4 !py-2 text-xs">
        <i class="fa-solid fa-hourglass-half"></i> <?= $pendingCount ?> Pending Approval
      </a>
    <?php endif; ?>
    <a href="<?= e(url('admin/jobs/create')) ?>" class="btn-gold !px-4 !py-2 text-xs">+ New Job</a>
  </div>
</div>

<div class="card overflow-x-auto">
  <table class="table-base">
    <thead>
      <tr>
        <th>Title</th>
        <th>Company</th>
        <th>Location</th>
        <th>Posted By</th>
        <th>Status</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($jobs as $job): ?>
        <tr>
          <td class="font-medium text-primary-navy">
            <div class="flex items-center gap-2">
              <?= job_logo_html($job, 'w-7 h-7') ?>
              <?= e($job['title']) ?> <?= $job['is_featured'] ? '<span class="badge-gold ml-1">Featured</span>' : '' ?>
            </div>
          </td>
          <td><?= e($job['company']) ?></td>
          <td class="text-slate-500"><?= e($job['location']) ?></td>
          <td class="text-slate-500"><?= !empty($job['posted_by']) ? 'Alumni' : 'Admin' ?></td>
          <td class="whitespace-nowrap">
            <span class="<?= $job['status'] === 'open' ? 'badge-blue' : 'badge-gold' ?>"><?= e($job['status']) ?></span>
            <?php if ($job['approval_status'] === 'pending'): ?>
              <span class="badge bg-amber-100 text-amber-700">Pending</span>
            <?php elseif ($job['approval_status'] === 'rejected'): ?>
              <span class="badge bg-red-100 text-red-700">Rejected</span>
            <?php endif; ?>
          </td>
          <td class="text-right whitespace-nowrap">
            <a href="<?= e(url('admin/jobs/' . $job['id'] . '/edit')) ?>" class="text-sky-600 hover:underline mr-3">Edit</a>
            <form method="POST" action="<?= e(url('admin/jobs/' . $job['id'] . '/delete')) ?>" class="inline" onsubmit="return confirm('Delete this job listing?');">
              <?= csrf_field() ?>
              <button type="submit" class="text-red-600 hover:underline">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($jobs)): ?>
        <tr><td colspan="6" class="text-center text-slate-400 py-6">No job listings yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
