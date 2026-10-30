<?php $tabs = admin_staff_tabs(); require dirname(dirname(__DIR__)) . '/partials/admin_group_tabs.php'; ?>

<p class="text-sm text-slate-500 mb-6">Create an Admin, Editor, Recruiter, or Advisor account directly — no alumni roster record needed. A temporary password is generated automatically and emailed to them; they can change it any time after logging in.</p>

<?php require dirname(dirname(__DIR__)) . '/partials/errors.php'; ?>

<form method="POST" action="<?= e(url('admin/create-staff')) ?>" class="card p-6 max-w-xl space-y-4">
  <?= csrf_field() ?>

  <div>
    <label class="form-label" for="name">Full Name</label>
    <input type="text" id="name" name="name" class="form-input" value="<?= old('name') ?>" required>
  </div>

  <div>
    <label class="form-label" for="email">Email Address</label>
    <input type="email" id="email" name="email" class="form-input" value="<?= old('email') ?>" required>
  </div>

  <div>
    <label class="form-label" for="role">Role</label>
    <select id="role" name="role" class="form-input" required>
      <option value="">Select a role&hellip;</option>
      <?php foreach ($roleLabels as $key => $label): ?>
        <option value="<?= e($key) ?>"><?= e($label) ?></option>
      <?php endforeach; ?>
    </select>
    <p class="text-xs text-slate-400 mt-1">
      <strong>Admin</strong> — full admin-panel access.
      <strong>Editor</strong> — can register alumni on the public site's behalf; no other admin access.
      <strong>Recruiter</strong> — a non-alumni account scoped to Jobs and Messages only (no Alumni Directory, Connections, Community Feed, or Mentorship).
      <strong>Advisor</strong> — a non-alumni account you can assign to specific alumni; can view their profile, message them, and keep private advising notes, plus has the same alumni-social access as a regular alumnus.
    </p>
  </div>

  <div class="bg-sky-50 border border-sky-100 rounded-lg p-3 text-xs text-sky-800 flex items-start gap-2">
    <i class="fa-solid fa-circle-info mt-0.5"></i>
    <p>A temporary password is generated automatically and emailed to them. They can change it any time from their profile.</p>
  </div>

  <button type="submit" class="btn-gold">Create Account</button>
</form>
