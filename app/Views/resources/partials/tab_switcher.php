<?php
$switcherTabs = array_filter([
    'career' => ['icon' => 'fa-briefcase', 'label' => 'Career Resources', 'desc' => 'Guides and tools for professional growth'],
    'resume-review' => ['icon' => 'fa-file-lines', 'label' => 'Resume Review', 'desc' => 'Get expert feedback on your resume'],
    'interview-prep' => ['icon' => 'fa-people-group', 'label' => 'Interview Prep', 'desc' => 'Prepare and practice for interviews'],
], fn ($t, $key) => \App\Models\PageTabVisibility::isVisible('resources', $key), ARRAY_FILTER_USE_BOTH);
?>
<div class="grid grid-cols-3 gap-2 sm:gap-3 mb-8">
  <?php foreach ($switcherTabs as $key => $t): $isActive = $tab === $key; ?>
    <a href="<?= e(url('resources') . ($key === 'career' ? '' : '?tab=' . $key)) ?>" class="card p-3 sm:p-4 flex flex-col sm:flex-row items-center gap-1.5 sm:gap-3 text-center sm:text-left <?= $isActive ? '!border-gold !bg-gold/5' : '' ?>">
      <div class="w-9 h-9 sm:w-10 sm:h-10 rounded-lg flex items-center justify-center flex-shrink-0 <?= $isActive ? 'bg-gold/15 text-gold' : 'bg-slate-100 text-slate-500' ?>"><i class="fa-solid <?= e($t['icon']) ?> text-sm sm:text-base"></i></div>
      <div class="min-w-0">
        <h4 class="text-[0.7rem] sm:text-sm font-bold leading-tight <?= $isActive ? 'text-gold' : 'text-primary-navy' ?>"><?= e($t['label']) ?></h4>
        <p class="hidden sm:block text-xs text-slate-500 truncate"><?= e($t['desc']) ?></p>
      </div>
    </a>
  <?php endforeach; ?>
</div>
