<?php
/** @var array $res */
$isSaved = in_array((int) $res['id'], $savedIds, true);
$difficultyColors = ['beginner' => 'bg-emerald-400', 'intermediate' => 'bg-amber-400', 'advanced' => 'bg-red-400'];
?>
<div class="card p-4 relative">
  <a href="<?= e(url('resources/' . $res['id'])) ?>" class="absolute inset-0 z-0" aria-label="<?= e($res['title']) ?>"></a>
  <div class="flex items-start gap-3 pointer-events-none">
    <div class="w-10 h-10 rounded-lg bg-slate-100 text-primary-navy flex items-center justify-center text-lg flex-shrink-0"><i class="fa-solid <?= e(resource_type_icon($res['resource_type'])) ?>"></i></div>
    <div class="min-w-0 flex-1">
      <h4 class="text-sm font-bold text-primary-navy"><?= e($res['title']) ?></h4>
      <p class="text-xs text-slate-500 mt-0.5"><?= e($res['description']) ?></p>
      <div class="flex flex-wrap items-center gap-3 text-[0.68rem] text-slate-500 mt-2">
        <?php if ($res['resource_type']): ?><span><i class="fa-regular fa-file-lines"></i> <?= e($res['resource_type']) ?></span><?php endif; ?>
        <?php if ($res['read_time_minutes']): ?><span><i class="fa-regular fa-clock"></i> <?= (int) $res['read_time_minutes'] ?> min read</span><?php endif; ?>
        <?php if ($res['difficulty']): ?><span class="flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full <?= e($difficultyColors[$res['difficulty']] ?? 'bg-slate-300') ?>"></span> <?= e(ucfirst($res['difficulty'])) ?></span><?php endif; ?>
      </div>
    </div>
    <div class="flex items-center gap-3 flex-shrink-0 pointer-events-auto relative z-10">
      <?php if (\App\Core\Auth::check()): ?>
        <form method="POST" action="<?= e(url('resources/' . $res['id'] . '/save')) ?>">
          <?= csrf_field() ?>
          <button type="submit" class="text-sm <?= $isSaved ? 'text-gold' : 'text-slate-300 hover:text-gold' ?>" title="<?= $isSaved ? 'Unsave' : 'Save' ?>"><i class="bi <?= $isSaved ? 'bi-bookmark-fill' : 'bi-bookmark' ?>"></i></button>
        </form>
      <?php endif; ?>
      <a href="<?= e(url('resources/' . $res['id'])) ?>" class="text-sm text-slate-300 hover:text-gold" title="Open resource"><i class="fa-solid fa-arrow-up-right-from-square"></i></a>
    </div>
  </div>
</div>
