<h1 class="text-xl font-extrabold text-primary-navy mb-1">Join the Alumni Network</h1>
<p class="text-sm text-slate-500 mb-6">Create your account to connect with fellow RBSN alumni.</p>

<?php require dirname(__DIR__) . '/partials/errors.php'; ?>

<form method="POST" action="<?= e(url('register')) ?>" class="space-y-4">
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
    <label class="form-label" for="matric_number">Matric Number</label>
    <input type="text" id="matric_number" name="matric_number" class="form-input" value="<?= old('matric_number') ?>" required>
    <p class="text-xs text-slate-400 mt-1">Must match the matric number, graduation year and cohort on file with the school.</p>
  </div>
  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="form-label" for="graduation_year">Graduation Year</label>
      <select id="graduation_year" name="graduation_year" class="form-input" required>
        <option value="">Select year</option>
        <?php foreach ($years as $year): ?>
          <option value="<?= e((string) $year) ?>" <?= old('graduation_year') === e((string) $year) ? 'selected' : '' ?>><?= e((string) $year) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="form-label" for="cohort">Cohort</label>
      <select id="cohort" name="cohort" class="form-input" required>
        <option value="">Select cohort</option>
        <?php foreach ($cohorts as $cohort): ?>
          <option value="<?= e($cohort) ?>" <?= old('cohort') === e($cohort) ? 'selected' : '' ?>><?= e($cohort) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
  </div>
  <div>
    <label class="form-label" for="program">Program</label>
    <select id="program" name="program" class="form-input">
      <option value="">Select program</option>
      <?php foreach ($programs as $program): ?>
        <option value="<?= e($program['name']) ?>" <?= old('program') === e($program['name']) ? 'selected' : '' ?>><?= e($program['name']) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div>
    <label class="form-label" for="password">Password</label>
    <div class="relative">
      <input type="password" id="password" name="password" class="form-input !pr-10" required minlength="8">
      <button type="button" class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" data-target="password" tabindex="-1"><i class="fa-regular fa-eye"></i></button>
    </div>
  </div>
  <div>
    <label class="form-label" for="password_confirmation">Confirm Password</label>
    <div class="relative">
      <input type="password" id="password_confirmation" name="password_confirmation" class="form-input !pr-10" required minlength="8">
      <button type="button" class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" data-target="password_confirmation" tabindex="-1"><i class="fa-regular fa-eye"></i></button>
    </div>
  </div>
  <button type="submit" class="btn-gold w-full">Create Account</button>
</form>

<p class="text-center text-sm text-slate-500 mt-6">
  Already have an account? <a href="<?= e(url('login')) ?>" class="text-gold font-semibold hover:underline">Log In</a>
</p>
