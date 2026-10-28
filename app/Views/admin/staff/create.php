<p class="text-sm text-slate-500 mb-6">Create an Admin, Editor, or Recruiter account directly — no alumni roster record needed. A temporary password is generated automatically and shown once after creation, so you can share it; the account holder can change it any time after logging in.</p>

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
    </p>
  </div>

  <div class="bg-sky-50 border border-sky-100 rounded-lg p-3 text-xs text-sky-800 flex items-start gap-2">
    <i class="fa-solid fa-circle-info mt-0.5"></i>
    <p>A temporary password is generated automatically and shown once after creation, so you can share it with them. They can change it any time from their profile.</p>
  </div>

  <button type="submit" class="btn-gold">Create Account</button>
</form>
