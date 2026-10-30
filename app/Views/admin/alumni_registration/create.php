<?php $tabs = admin_alumni_tabs(); require dirname(dirname(__DIR__)) . '/partials/admin_group_tabs.php'; ?>

<p class="text-sm text-slate-500 mb-6">Register an alumnus on their behalf — e.g. at an in-person event, or when helping someone who can't complete the self-service sign-up. Their matric number must match a record on the <a href="<?= e(url('admin/alumni-roster')) ?>" class="text-gold hover:underline">Alumni Roster</a>; their name, program, cohort and graduation year are pulled from that record automatically.</p>

<?php require dirname(dirname(__DIR__)) . '/partials/errors.php'; ?>

<form method="POST" action="<?= e(url('admin/register-alumni')) ?>" class="card p-6 max-w-xl space-y-4">
  <?= csrf_field() ?>

  <div>
    <label class="form-label" for="email">Email Address</label>
    <input type="email" id="email" name="email" class="form-input" value="<?= old('email') ?>" required>
    <?php require dirname(dirname(__DIR__)) . '/partials/email_check.php'; ?>
  </div>

  <div>
    <label class="form-label" for="matric_number">Matric Number</label>
    <input type="text" id="matric_number" name="matric_number" class="form-input" value="<?= old('matric_number') ?>" required>
    <p class="text-xs text-slate-400 mt-1">Must match a record on the Alumni Roster.</p>
    <?php require dirname(dirname(__DIR__)) . '/partials/roster_lookup_preview.php'; ?>
  </div>

  <div>
    <label class="form-label">Job Categories <span class="text-red-500">*</span></label>
    <p class="text-xs text-slate-400 mb-2">Used to match this alumnus with relevant opportunities on "Jobs for Me". They can change this any time from their profile.</p>
    <?php $selectedIds = []; $scrollable = true; $allowCreate = \App\Core\Auth::isAdmin(); ?>
    <?php require dirname(dirname(__DIR__)) . '/partials/job_category_picker.php'; ?>
  </div>

  <div class="bg-sky-50 border border-sky-100 rounded-lg p-3 text-xs text-sky-800 flex items-start gap-2">
    <i class="fa-solid fa-circle-info mt-0.5"></i>
    <p>A temporary password is generated automatically and emailed to the alumnus. They can change it any time from their profile.</p>
  </div>

  <button type="submit" class="btn-gold">Register Alumni</button>
</form>
