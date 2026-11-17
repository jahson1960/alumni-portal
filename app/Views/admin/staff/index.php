<?php $tabs = admin_staff_tabs(); require dirname(__DIR__, 2) . '/partials/admin_group_tabs.php'; ?>

<div class="flex justify-between items-center mb-4">
  <h2 class="text-sm font-bold text-primary-navy">Staff</h2>
  <a href="<?= e(url('admin/create-staff')) ?>" class="btn-gold !px-4 !py-2 text-xs">+ Create Staff</a>
</div>

<?php require dirname(__DIR__, 2) . '/partials/admin_pagination.php'; ?>

<div class="card overflow-x-auto">
  <table class="table-base">
    <thead>
      <tr>
        <th>Name</th>
        <th>Email</th>
        <th>Role</th>
        <th>Status</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($staff as $member): ?>
        <tr>
          <td class="font-medium text-primary-navy">
            <div class="flex items-center gap-2">
              <?= avatar_html($member, 'w-7 h-7') ?> <?= e($member['name']) ?>
            </div>
          </td>
          <td class="text-slate-500"><?= e($member['email']) ?></td>
          <td>
            <?php if ((int) $member['id'] === (int) \App\Core\Auth::id()): ?>
              <span class="badge bg-indigo-100 text-indigo-700" title="You cannot change your own role"><?= e($roleLabels[$member['role']] ?? ucfirst($member['role'])) ?></span>
            <?php else: ?>
              <form method="POST" action="<?= e(url('admin/staff/' . $member['id'] . '/change-role')) ?>" class="inline"
                    onsubmit="return confirm(<?= e(json_encode('Change ' . $member['name'] . "'s role to ")) ?> + this.role.options[this.role.selectedIndex].text + '?');">
                <?= csrf_field() ?>
                <select name="role" class="form-input !py-1 !text-xs !w-auto" onchange="this.form.requestSubmit()">
                  <?php foreach ($roleLabels as $value => $label): ?>
                    <option value="<?= e($value) ?>" <?= $member['role'] === $value ? 'selected' : '' ?>><?= e($label) ?></option>
                  <?php endforeach; ?>
                </select>
              </form>
            <?php endif; ?>
          </td>
          <td><span class="<?= $member['status'] === 'active' ? 'badge-blue' : 'badge-gold' ?>"><?= e($member['status']) ?></span></td>
          <td class="text-right whitespace-nowrap">
            <form method="POST" action="<?= e(url('admin/staff/' . $member['id'] . '/reset-password')) ?>" class="inline" onsubmit="return confirm('Reset this account&#39;s password? A new temporary password will be generated and emailed to them.');">
              <?= csrf_field() ?>
              <button type="submit" class="text-sky-600 hover:underline mr-3">Reset Password</button>
            </form>
            <?php if ($member['role'] === 'editor'): ?>
              <form method="POST" action="<?= e(url('admin/alumni/' . $member['id'] . '/role')) ?>" class="inline">
                <?= csrf_field() ?>
                <button type="submit" class="text-indigo-600 hover:underline mr-3">Remove Editor Access</button>
              </form>
            <?php endif; ?>
            <?php if ($member['status'] === 'active'): ?>
              <form method="POST" action="<?= e(url('admin/staff/' . $member['id'] . '/suspend')) ?>" class="inline">
                <?= csrf_field() ?>
                <button type="submit" class="text-amber-600 hover:underline mr-3">Suspend</button>
              </form>
            <?php else: ?>
              <form method="POST" action="<?= e(url('admin/staff/' . $member['id'] . '/activate')) ?>" class="inline">
                <?= csrf_field() ?>
                <button type="submit" class="text-green-600 hover:underline mr-3">Activate</button>
              </form>
            <?php endif; ?>
            <form method="POST" action="<?= e(url('admin/staff/' . $member['id'] . '/delete')) ?>" class="inline" onsubmit="return confirm('Delete this staff account? This cannot be undone.');">
              <?= csrf_field() ?>
              <button type="submit" class="text-red-600 hover:underline">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($staff)): ?>
        <tr><td colspan="5" class="text-center text-slate-400 py-6">No staff accounts yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
