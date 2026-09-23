<?php
$practiceTools = [
    ['icon' => 'bi-patch-question', 'color' => 'bg-emerald-100 text-emerald-600', 'title' => 'Common Interview Questions', 'desc' => 'Browse frequently asked questions by industry and role.', 'href' => url('resources') . '?' . http_build_query(['tab' => 'interview-prep']) . '#resource-list'],
    ['icon' => 'bi-bookmark-star', 'color' => 'bg-sky-100 text-sky-600', 'title' => 'Mock Interview', 'desc' => 'Practice with alumni mentors and get real-time feedback.', 'href' => url('mentorship')],
    ['icon' => 'bi-camera-video', 'color' => 'bg-amber-100 text-amber-600', 'title' => 'Video Tips', 'desc' => 'Watch expert tips to ace your interviews.', 'href' => url('resources') . '?' . http_build_query(['tab' => 'interview-prep', 'type' => 'Video']) . '#resource-list'],
];
?>
<div class="mb-6">
  <h1 class="text-2xl font-extrabold text-primary-navy">Interview Prep</h1>
  <p class="text-sm text-slate-500 mt-1">Prepare with confidence using expert resources and practice tools.</p>
</div>

<?php require __DIR__ . '/../partials/tab_switcher.php'; ?>

<h3 class="text-sm font-bold text-primary-navy mb-3">Practice Tools</h3>
<div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
  <?php foreach ($practiceTools as $tool): ?>
    <a href="<?= e($tool['href']) ?>" class="card p-4 flex items-center gap-3 hover:border-gold/50">
      <div class="w-10 h-10 rounded-lg <?= e($tool['color']) ?> flex items-center justify-center flex-shrink-0"><i class="bi <?= e($tool['icon']) ?>"></i></div>
      <div class="min-w-0 flex-1">
        <h4 class="text-sm font-bold text-primary-navy"><?= e($tool['title']) ?></h4>
        <p class="text-xs text-slate-500 mt-0.5"><?= e($tool['desc']) ?></p>
      </div>
      <i class="fa-solid fa-chevron-right text-slate-300 text-xs flex-shrink-0"></i>
    </a>
  <?php endforeach; ?>
</div>

<div class="grid grid-cols-1 lg:grid-cols-[1fr_280px] gap-6 items-start">
  <div>
    <h3 class="text-sm font-bold text-primary-navy mb-3">Resource Library</h3>
    <div class="space-y-3 mb-4">
      <?php foreach (\App\Models\Resource::INTERVIEW_CATEGORIES as $cat): $active = ($filters['category'] ?? '') === $cat; ?>
        <a href="<?= e(url('resources') . '?' . http_build_query(['tab' => 'interview-prep', 'category' => $cat]) . '#resource-list') ?>" class="card p-3.5 flex items-center justify-between <?= $active ? '!border-gold' : '' ?>">
          <span class="flex items-center gap-2.5 text-sm font-semibold <?= $active ? 'text-gold' : 'text-primary-navy' ?>"><i class="bi <?= e(resource_category_icon($cat)) ?>"></i> <?= e($cat) ?></span>
          <span class="badge bg-slate-100 text-slate-500"><?= (int) ($categoryCounts[$cat] ?? 0) ?></span>
        </a>
      <?php endforeach; ?>
    </div>

    <div id="resource-list" class="scroll-mt-24">
      <div class="flex items-center justify-between mb-3">
        <p class="text-xs text-slate-400"><?= count($resources) ?> resource<?= count($resources) === 1 ? '' : 's' ?></p>
        <?php if (!empty($filters)): ?>
          <a href="<?= e(url('resources?tab=interview-prep')) ?>" class="text-xs text-slate-500 hover:text-gold flex items-center gap-1"><i class="bi bi-arrow-clockwise"></i> Clear filters</a>
        <?php endif; ?>
      </div>
      <div class="space-y-3">
        <?php foreach ($resources as $res): ?>
          <?php require __DIR__ . '/../partials/resource_row.php'; ?>
        <?php endforeach; ?>
        <?php if (empty($resources)): ?>
          <p class="text-sm text-slate-400">No resources found matching those filters.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <aside class="card p-4 !bg-emerald-50 !border-emerald-100">
    <div class="flex items-center gap-2 mb-1.5">
      <i class="fa-solid fa-bullseye text-emerald-500"></i>
      <h3 class="text-sm font-bold text-primary-navy">Practice makes perfect!</h3>
    </div>
    <p class="text-xs text-slate-600">The more you practice, the more confident you'll be.</p>
  </aside>
</div>
