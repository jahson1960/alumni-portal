<?php
use App\Core\Auth;
use App\Models\Setting;

$pageTitle = $title ?? 'Admin Panel';
$siteFavicon = Setting::get('site_favicon', '');
$activeNav = $activeNav ?? '';
$adminUser = Auth::user();

$navItems = [
    'dashboard' => ['label' => 'Dashboard', 'icon' => 'fa-gauge', 'href' => url('admin/dashboard')],
    'settings' => ['label' => 'Site Settings', 'icon' => 'fa-sliders', 'href' => url('admin/settings')],
    'page_visibility' => ['label' => 'Page Visibility', 'icon' => 'fa-eye', 'href' => url('admin/page-visibility')],
    'hero_slides' => ['label' => 'Hero Slides', 'icon' => 'fa-images', 'href' => url('admin/hero-slides')],
    'news' => ['label' => 'News & Blog', 'icon' => 'fa-newspaper', 'href' => url('admin/news')],
    'jobs' => ['label' => 'Jobs', 'icon' => 'fa-briefcase', 'href' => url('admin/jobs')],
    'companies' => ['label' => 'Companies', 'icon' => 'fa-building', 'href' => url('admin/companies')],
    'events' => ['label' => 'Events', 'icon' => 'fa-calendar-days', 'href' => url('admin/events')],
    'resources' => ['label' => 'Resources', 'icon' => 'fa-folder-open', 'href' => url('admin/resources')],
    'categories' => ['label' => 'Categories', 'icon' => 'fa-tags', 'href' => url('admin/categories')],
    'articles' => ['label' => 'Article Review', 'icon' => 'fa-book', 'href' => url('admin/articles')],
    'campaigns' => ['label' => 'Giving Campaigns', 'icon' => 'fa-hand-holding-heart', 'href' => url('admin/campaigns')],
    'donation_methods' => ['label' => 'Donation Methods', 'icon' => 'fa-building-columns', 'href' => url('admin/donation-methods')],
    'donation_confirmations' => ['label' => 'Donation Confirmations', 'icon' => 'fa-clipboard-check', 'href' => url('admin/donation-confirmations')],
    'giving_causes' => ['label' => 'Give Back Causes', 'icon' => 'fa-heart', 'href' => url('admin/giving-causes')],
    'benefits' => ['label' => 'Benefits', 'icon' => 'fa-gift', 'href' => url('admin/benefits')],
    'alumni' => ['label' => 'Alumni', 'icon' => 'fa-user-group', 'href' => url('admin/alumni')],
    'alumni_roster' => ['label' => 'Alumni Roster', 'icon' => 'fa-id-card', 'href' => url('admin/alumni-roster')],
    'register_alumni' => ['label' => 'Register Alumni', 'icon' => 'fa-user-plus', 'href' => url('admin/register-alumni')],
];

// Editors only have this one admin-area capability — everything else here is admin-only,
// so an editor who lands on this layout (via AlumniRegistrationController) sees just this link.
if (Auth::isEditor()) {
    $navItems = array_intersect_key($navItems, ['register_alumni' => true]);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?> &middot; Admin</title>
  <?php if ($siteFavicon): ?>
    <link rel="icon" href="<?= e($siteFavicon) ?>">
  <?php endif; ?>
  <meta name="csrf-token" content="<?= e(\App\Core\Csrf::token()) ?>">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= e(versioned_asset('css/app.css')) ?>">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">
  <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
  <script>window.EDITOR_UPLOAD_URL = <?= json_encode(url('admin/upload/editor-image')) ?>;</script>
</head>
<body class="font-sans bg-slate-100 min-h-screen flex">

  <aside class="w-64 bg-primary-navy text-white flex-shrink-0 min-h-screen flex flex-col">
    <div class="px-5 py-5 border-b border-white/10">
      <div class="font-extrabold text-sm tracking-wide">ROME BUSINESS SCHOOL</div>
      <div class="text-[0.65rem] text-gold italic">Alumni Portal &middot; Admin</div>
    </div>
    <nav class="flex-1 px-3 py-4 space-y-1">
      <?php foreach ($navItems as $key => $item): ?>
        <a href="<?= e($item['href']) ?>" class="admin-nav-link <?= $activeNav === $key ? 'active' : '' ?>">
          <i class="fa-solid <?= e($item['icon']) ?> w-4 text-center"></i> <?= e($item['label']) ?>
        </a>
      <?php endforeach; ?>
    </nav>
    <div class="px-3 py-4 border-t border-white/10 space-y-1">
      <a href="<?= e(url('/')) ?>" class="admin-nav-link"><i class="fa-solid fa-arrow-left w-4 text-center"></i> View Site</a>
      <form method="POST" action="<?= e(url('admin/logout')) ?>">
        <?= csrf_field() ?>
        <button type="submit" class="admin-nav-link w-full text-left"><i class="fa-solid fa-right-from-bracket w-4 text-center"></i> Logout</button>
      </form>
    </div>
  </aside>

  <div class="flex-1 min-w-0">
    <header class="bg-white border-b border-slate-200 px-6 py-4 flex justify-between items-center">
      <h1 class="text-lg font-extrabold text-primary-navy"><?= e($pageTitle) ?></h1>
      <div class="flex items-center gap-2 text-sm text-slate-600">
        <?= avatar_html($adminUser ?? ['name' => 'Admin'], 'w-8 h-8') ?>
        <span class="font-medium"><?= e($adminUser['name'] ?? 'Admin') ?></span>
      </div>
    </header>

    <main class="p-6">
      <?php $flashSuccess = flash_get('success'); $flashError = flash_get('error'); ?>
      <?php if ($flashSuccess): ?>
        <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded px-4 py-3 mb-4"><?= e($flashSuccess) ?></div>
      <?php endif; ?>
      <?php if ($flashError): ?>
        <div class="bg-red-50 border border-red-200 text-red-800 text-sm rounded px-4 py-3 mb-4"><?= e($flashError) ?></div>
      <?php endif; ?>

      <?= $content ?>
    </main>
  </div>

  <script src="<?= e(asset('js/admin.js')) ?>"></script>
</body>
</html>
