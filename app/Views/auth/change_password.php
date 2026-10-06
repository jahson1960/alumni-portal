<h1 class="text-xl font-extrabold text-primary-navy mb-1">Change Your Password</h1>
<?php if ($forced): ?>
  <p class="text-sm text-slate-500 mb-6">For your security, you need to set a new password before continuing.</p>
<?php else: ?>
  <p class="text-sm text-slate-500 mb-6">Choose a new password for your account.</p>
<?php endif; ?>

<?php require dirname(__DIR__) . '/partials/errors.php'; ?>

<form method="POST" action="<?= e(url('change-password')) ?>" class="space-y-4">
  <?= csrf_field() ?>
  <div>
    <label class="form-label" for="password">New Password</label>
    <div class="relative">
      <input type="password" id="password" name="password" class="form-input !pr-10" required minlength="8">
      <button type="button" class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" data-target="password" tabindex="-1"><i class="fa-regular fa-eye"></i></button>
    </div>
  </div>
  <div>
    <label class="form-label" for="password_confirmation">Confirm New Password</label>
    <div class="relative">
      <input type="password" id="password_confirmation" name="password_confirmation" class="form-input !pr-10" required minlength="8">
      <button type="button" class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" data-target="password_confirmation" tabindex="-1"><i class="fa-regular fa-eye"></i></button>
    </div>
  </div>
  <button type="submit" class="btn-gold w-full">Update Password</button>
</form>
