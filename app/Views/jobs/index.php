<?php
$authUser = \App\Core\Auth::user();
$jobTabIcons = [
    'board' => 'fa-solid fa-briefcase',
    'search' => 'fa-solid fa-magnifying-glass',
    'companies' => 'fa-solid fa-building',
    'saved' => 'fa-solid fa-bookmark',
    'alerts' => 'fa-regular fa-bell',
];
$jobTabHero = [
    'board' => ['Job Board', 'Discover opportunities. Advance your career.'],
    'search' => ['Advanced Search', 'Find the perfect opportunity that matches your skills and experience.'],
    'companies' => ['Companies Hiring', 'Top companies from our network that are actively hiring.'],
    'saved' => ['Saved Jobs', 'Opportunities you have bookmarked for later.'],
    'alerts' => ['Job Alerts', 'Get notified the moment a matching role is posted.'],
];
$jobTabs = [
    'board' => 'Job Board',
    'search' => 'Advanced Search',
    'companies' => 'Companies Hiring',
];
if ($authUser) {
    $jobTabs['saved'] = 'Saved Jobs';
    $jobTabs['alerts'] = 'Job Alerts';
}
$jobTabs = array_filter($jobTabs, fn ($label, $key) => \App\Models\PageTabVisibility::isVisible('jobs', $key), ARRAY_FILTER_USE_BOTH);
[$heroTitle, $heroSubtitle] = $jobTabHero[$tab] ?? $jobTabHero['board'];
$jobsBannerImage = \App\Models\Setting::get('jobs_banner_image', 'https://images.unsplash.com/photo-1519389950473-47ba0277781c?auto=format&fit=crop&w=1600&q=80');
?>
<div class="relative text-white overflow-hidden bg-primary-navy bg-cover bg-center" style="background-image: linear-gradient(90deg, rgba(9,26,46,0.97) 0%, rgba(9,26,46,0.9) 40%, rgba(9,26,46,0.55) 100%), url('<?= e($jobsBannerImage) ?>');">
  <div class="max-w-[1280px] mx-auto px-4 md:px-8 py-10 md:py-14 relative">
    <p class="text-xs font-bold text-gold uppercase tracking-wide mb-2">Careers</p>
    <h1 class="text-3xl md:text-4xl font-extrabold mb-2"><?= e($heroTitle) ?></h1>
    <p class="text-sm md:text-base text-slate-200 max-w-lg"><?= e($heroSubtitle) ?></p>
  </div>
</div>

<div class="max-w-[1280px] mx-auto px-4 md:px-8 -mt-6 md:-mt-7 relative z-10 pb-8">

  <div class="card !rounded-xl !p-2 flex flex-nowrap items-center gap-1 overflow-x-auto overflow-y-hidden mb-6">
    <?php foreach ($jobTabs as $key => $label): ?>
      <a href="<?= e(url('jobs') . ($key === 'board' ? '' : '?tab=' . $key)) ?>" class="flex items-center gap-2 px-4 py-3 text-sm font-semibold whitespace-nowrap border-b-2 -mb-px <?= $tab === $key ? 'text-gold border-gold' : 'text-slate-600 border-transparent hover:text-primary-navy' ?>">
        <i class="<?= e($jobTabIcons[$key] ?? 'fa-solid fa-circle') ?>"></i> <?= e($label) ?>
      </a>
    <?php endforeach; ?>
  </div>

  <?php require __DIR__ . '/tabs/' . $tab . '.php'; ?>
</div>
