<h1 class="text-xl font-extrabold text-primary-navy mb-1">Welcome Back</h1>
<p class="text-sm text-slate-500 mb-6">Log in to your RBSN Alumni account.</p>

<?php require dirname(__DIR__) . '/partials/errors.php'; ?>

<form method="POST" action="<?= e(url('login')) ?>" class="space-y-4">
  <?= csrf_field() ?>
  <div>
    <label class="form-label" for="email">Email Address</label>
    <input type="email" id="email" name="email" class="form-input" value="<?= old('email') ?>" required>
  </div>
  <div>
    <label class="form-label" for="password">Password</label>
    <div class="relative">
      <input type="password" id="password" name="password" class="form-input !pr-10" required>
      <button type="button" class="password-toggle absolute right-3 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600" data-target="password" tabindex="-1"><i class="fa-regular fa-eye"></i></button>
    </div>
  </div>
  <button type="submit" class="btn-gold w-full">Log In</button>
</form>

<p class="text-center text-sm text-slate-500 mt-6">
  Don't have an account? <a href="<?= e(url('register')) ?>" class="text-gold font-semibold hover:underline">Join Now</a>
</p>
