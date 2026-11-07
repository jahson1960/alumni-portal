<?php $tabs = admin_alumni_tabs(); require dirname(__DIR__, 2) . '/partials/admin_group_tabs.php'; ?>

<div class="flex justify-between items-center mb-4">
  <div>
    <h2 class="text-sm font-bold text-primary-navy"><?= e(term_plural()) ?> Roster</h2>
    <p class="text-xs text-slate-400">A matric number must match a row here before someone can register — their name, program, cohort, and matriculation/graduation year are pulled from that row automatically. Each row can only be claimed by one account.</p>
  </div>
  <div class="flex items-center gap-2 flex-shrink-0">
    <a href="<?= e(url('admin/alumni-roster/import')) ?>" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-4 !py-2 text-xs">Import Roster</a>
    <a href="<?= e(url('admin/alumni-roster/create')) ?>" class="btn-gold !px-4 !py-2 text-xs">+ New Entry</a>
  </div>
</div>

<?php require dirname(dirname(__DIR__)) . '/partials/errors.php'; ?>

<form method="GET" action="<?= e(url('admin/alumni-roster')) ?>" class="card p-4 mb-4 flex flex-wrap items-end gap-3">
  <div>
    <label class="form-label" for="filter_cohort">Cohort</label>
    <select id="filter_cohort" name="cohort" class="form-input !w-auto">
      <option value="">All Cohorts</option>
      <?php foreach ($cohorts as $c): ?>
        <option value="<?= e($c) ?>" <?= $filters['cohort'] === $c ? 'selected' : '' ?>><?= e($c) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div>
    <label class="form-label" for="filter_program">Program</label>
    <select id="filter_program" name="program" class="form-input !w-auto">
      <option value="">All Programs</option>
      <?php foreach ($programs as $p): ?>
        <option value="<?= e($p) ?>" <?= $filters['program'] === $p ? 'selected' : '' ?>><?= e($p) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div>
    <label class="form-label" for="filter_matriculation_year">Matriculation Year</label>
    <select id="filter_matriculation_year" name="matriculation_year" class="form-input !w-auto">
      <option value="">All Years</option>
      <?php foreach ($matriculationYears as $y): ?>
        <option value="<?= e((string) $y) ?>" <?= $filters['matriculation_year'] === (string) $y ? 'selected' : '' ?>><?= e((string) $y) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div>
    <label class="form-label" for="filter_graduation_year">Graduation Year</label>
    <select id="filter_graduation_year" name="graduation_year" class="form-input !w-auto">
      <option value="">All Years</option>
      <?php foreach ($graduationYears as $y): ?>
        <option value="<?= e((string) $y) ?>" <?= $filters['graduation_year'] === (string) $y ? 'selected' : '' ?>><?= e((string) $y) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <button type="submit" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-4 !py-2 text-xs">Filter</button>
  <?php if ($hasActiveFilters): ?>
    <a href="<?= e(url('admin/alumni-roster')) ?>" class="text-xs text-slate-500 hover:text-gold">Clear filters</a>
  <?php endif; ?>

  <?php if ($hasActiveFilters && $matchingUnclaimedCount > 0): ?>
    <form method="POST" action="<?= e(url('admin/alumni-roster/register-filtered')) ?>" class="ml-auto"
          onsubmit="return confirm('Register all ' + <?= (int) $matchingUnclaimedCount ?> + ' matching, unclaimed roster entries with an email on file? A password will be generated and emailed to each.');">
      <?= csrf_field() ?>
      <input type="hidden" name="cohort" value="<?= e($filters['cohort']) ?>">
      <input type="hidden" name="program" value="<?= e($filters['program']) ?>">
      <input type="hidden" name="graduation_year" value="<?= e($filters['graduation_year']) ?>">
      <input type="hidden" name="matriculation_year" value="<?= e($filters['matriculation_year']) ?>">
      <button type="submit" class="btn-gold !px-4 !py-2 text-xs">Register All Matching (<?= (int) $matchingUnclaimedCount ?>)</button>
    </form>
  <?php endif; ?>
</form>

<?php require dirname(__DIR__, 2) . '/partials/admin_pagination.php'; ?>

<div class="card overflow-x-auto">
  <table class="table-base">
    <thead><tr><th>Matric Number</th><th>Full Name</th><th>Email</th><th>Program</th><th>Matric. Year</th><th>Graduation Year</th><th>Cohort</th><th>Status</th><th></th></tr></thead>
    <tbody>
      <?php foreach ($rows as $row): ?>
        <tr>
          <td class="font-medium text-primary-navy"><?= e($row['matric_number']) ?></td>
          <td class="text-slate-500"><?= e($row['full_name'] ?: '—') ?></td>
          <td class="text-slate-500"><?= e($row['email'] ?: '—') ?></td>
          <td class="text-slate-500"><?= e($row['program'] ?: '—') ?></td>
          <td class="text-slate-500"><?= e($row['matriculation_year'] ?: '—') ?></td>
          <td class="text-slate-500"><?= e($row['graduation_year'] ?: '—') ?></td>
          <td class="text-slate-500"><?= e($row['cohort']) ?></td>
          <td><?php if ($row['claimed_by_user_id']): ?><span class="badge-blue">Claimed</span><?php else: ?><span class="badge bg-slate-100 text-slate-500">Available</span><?php endif; ?></td>
          <td class="text-right whitespace-nowrap">
            <?php if (!$row['claimed_by_user_id']): ?>
              <?php if ($row['email']): ?>
                <form method="POST" action="<?= e(url('admin/alumni-roster/' . $row['id'] . '/register')) ?>" class="inline" onsubmit="return confirm('Register ' + <?= json_encode($row['full_name']) ?> + '? A password will be generated and emailed to ' + <?= json_encode($row['email']) ?> + '.');">
                  <?= csrf_field() ?>
                  <button type="submit" class="text-green-600 hover:underline mr-3">Register</button>
                </form>
              <?php else: ?>
                <span class="text-slate-300 mr-3" title="Add an email to this row to register it">Register</span>
              <?php endif; ?>
            <?php endif; ?>
            <a href="<?= e(url('admin/alumni-roster/' . $row['id'] . '/edit')) ?>" class="text-sky-600 hover:underline mr-3">Edit</a>
            <form method="POST" action="<?= e(url('admin/alumni-roster/' . $row['id'] . '/delete')) ?>" class="inline" onsubmit="return confirm('Delete this roster entry?');">
              <?= csrf_field() ?>
              <button type="submit" class="text-red-600 hover:underline">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($rows)): ?>
        <tr><td colspan="9" class="text-center text-slate-400 py-6">No roster entries match.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
