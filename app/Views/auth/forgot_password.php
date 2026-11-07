<h1 class="text-xl font-extrabold text-primary-navy mb-1">Forgot Password</h1>
<p class="text-sm text-slate-500 mb-6">Enter your account email and we'll send you a link to reset your password.</p>

<?php require dirname(__DIR__) . '/partials/errors.php'; ?>

<form method="POST" action="<?= e(url('forgot-password')) ?>" class="space-y-4">
  <?= csrf_field() ?>
  <div>
    <label class="form-label" for="email">Email Address</label>
    <input type="email" id="email" name="email" class="form-input" value="<?= old('email') ?>" required>
  </div>
  <button type="submit" class="btn-gold w-full">Send Reset Link</button>
</form>

<p class="text-center text-sm text-slate-500 mt-6">
  <a href="<?= e(url('login')) ?>" class="text-gold font-semibold hover:underline">Back to Login</a>
</p>
