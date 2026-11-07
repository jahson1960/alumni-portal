<h1 class="text-xl font-extrabold text-primary-navy mb-1">Select Your Job Categories</h1>
<p class="text-sm text-slate-500 mb-6">Choose the kind of opportunities you're interested in — this is used to match you with relevant jobs on "Jobs for Me". You can change this any time from your profile.</p>

<?php require dirname(__DIR__) . '/partials/errors.php'; ?>

<form method="POST" action="<?= e(url('select-job-categories')) ?>" class="space-y-4">
  <?= csrf_field() ?>
  <?php $selectedIds = []; $scrollable = true; ?>
  <?php require dirname(__DIR__) . '/partials/job_category_picker.php'; ?>
  <button type="submit" class="btn-gold w-full">Continue</button>
</form>
