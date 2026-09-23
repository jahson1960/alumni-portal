<?php
$difficultyColors = ['beginner' => 'bg-emerald-400', 'intermediate' => 'bg-amber-400', 'advanced' => 'bg-red-400'];
$hasExternalLink = !empty($resource['link']) && $resource['link'] !== '#';
?>
<div class="max-w-3xl mx-auto px-4 md:px-8 py-8">
  <a href="<?= e($backUrl) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> <?= e($backLabel) ?></a>

  <div class="card p-6 md:p-8">
    <div class="flex gap-4 items-start mb-4">
      <div class="w-12 h-12 rounded-lg bg-slate-100 text-primary-navy flex items-center justify-center text-xl flex-shrink-0"><i class="fa-solid <?= e(resource_type_icon($resource['resource_type'])) ?>"></i></div>
      <div class="flex-1 min-w-0">
        <h1 class="text-xl font-extrabold text-primary-navy"><?= e($resource['title']) ?></h1>
        <p class="text-sm text-slate-500 mt-0.5 flex items-center gap-1.5"><i class="bi <?= e(resource_category_icon($resource['category'])) ?>"></i> <?= e($resource['category']) ?></p>
      </div>
      <?php if (\App\Core\Auth::check()): ?>
        <form method="POST" action="<?= e(url('resources/' . $resource['id'] . '/save')) ?>" class="flex-shrink-0">
          <?= csrf_field() ?>
          <button type="submit" class="btn <?= $isSaved ? 'bg-gold text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?> !px-3 !py-2 text-xs"><i class="bi <?= $isSaved ? 'bi-bookmark-fill' : 'bi-bookmark' ?>"></i> <?= $isSaved ? 'Saved' : 'Save' ?></button>
        </form>
      <?php endif; ?>
    </div>

    <div class="flex flex-wrap gap-4 text-xs text-slate-500 mb-6 border-b border-slate-100 pb-6">
      <?php if ($resource['resource_type']): ?><span><i class="fa-regular fa-file-lines"></i> <?= e($resource['resource_type']) ?></span><?php endif; ?>
      <?php if ($resource['read_time_minutes']): ?><span><i class="fa-regular fa-clock"></i> <?= (int) $resource['read_time_minutes'] ?> min read</span><?php endif; ?>
      <?php if ($resource['difficulty']): ?><span class="flex items-center gap-1.5"><span class="w-1.5 h-1.5 rounded-full <?= e($difficultyColors[$resource['difficulty']] ?? 'bg-slate-300') ?>"></span> <?= e(ucfirst($resource['difficulty'])) ?></span><?php endif; ?>
      <?php if (!empty($resource['is_featured'])): ?><span class="text-amber-500"><i class="fa-solid fa-star"></i> Featured</span><?php endif; ?>
    </div>

    <h3 class="section-title text-xs mb-2">Description</h3>
    <p class="text-sm text-slate-600 leading-relaxed mb-6"><?= e($resource['description']) ?></p>

    <?php if ($hasExternalLink): ?>
      <a href="<?= e($resource['link']) ?>" target="_blank" rel="noopener noreferrer" class="btn-gold inline-flex items-center gap-2"><i class="fa-solid fa-arrow-up-right-from-square"></i> Open Resource</a>
    <?php endif; ?>
  </div>
</div>
