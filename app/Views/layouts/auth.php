<?php $pageTitle = $title ?? 'Rome Business School Nigeria - Alumni Network'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?></title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= e(versioned_asset('css/app.css')) ?>">
</head>
<body class="font-sans bg-primary-navy min-h-screen flex items-center justify-center p-4">
  <div class="w-full max-w-md">
    <a href="<?= e(url('/')) ?>" class="flex flex-col items-center gap-1 mb-6">
      <img src="<?= e(upload_url('branding/rbs-logo.png')) ?>" alt="Rome Business School Logo" width="56" height="40">
      <span class="text-white font-extrabold text-xs tracking-wide text-center">ROME BUSINESS SCHOOL NIGERIA</span>
    </a>
    <div class="card p-8">
      <?php $flashSuccess = flash_get('success'); $flashError = flash_get('error'); ?>
      <?php if ($flashSuccess): ?>
        <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded px-4 py-3 mb-4"><?= e($flashSuccess) ?></div>
      <?php endif; ?>
      <?php if ($flashError): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 text-sm rounded px-4 py-3 mb-4"><?= e($flashError) ?></div>
      <?php endif; ?>
      <?= $content ?>
    </div>
  </div>
</body>
</html>
