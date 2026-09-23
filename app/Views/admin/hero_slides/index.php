<div class="flex justify-between items-center mb-4">
  <p class="text-sm text-slate-500"><?= count($slides) ?> slide<?= count($slides) === 1 ? '' : 's' ?></p>
  <a href="<?= e(url('admin/hero-slides/create')) ?>" class="btn-gold !px-4 !py-2 text-xs">+ Add Slide</a>
</div>

<div class="space-y-3">
  <?php foreach ($slides as $slide): ?>
    <div class="card p-4 flex items-center gap-4">
      <?php if (!empty($slide['image'])): ?>
        <img src="<?= e($slide['image']) ?>" alt="" class="w-24 h-14 object-cover rounded flex-shrink-0">
      <?php else: ?>
        <div class="w-24 h-14 rounded bg-slate-100 flex items-center justify-center text-slate-400 text-xs flex-shrink-0">No image</div>
      <?php endif; ?>
      <div class="min-w-0 flex-1">
        <div class="flex items-center gap-2">
          <h4 class="text-sm font-bold text-primary-navy truncate"><?= e($slide['title']) ?> <?= e($slide['highlight']) ?></h4>
          <span class="<?= $slide['enabled'] ? 'badge-green' : 'badge-gold' ?>"><?= $slide['enabled'] ? 'Active' : 'Inactive' ?></span>
        </div>
        <p class="text-xs text-slate-500 truncate"><?= e($slide['subtitle']) ?></p>
        <p class="text-[0.68rem] text-slate-400 mt-1">Order <?= (int) $slide['sort_order'] ?> &bull; Align: <?= e(ucfirst($slide['content_align'])) ?></p>
      </div>
      <div class="flex gap-3 flex-shrink-0 items-center">
        <form method="POST" action="<?= e(url('admin/hero-slides/' . $slide['id'] . '/toggle')) ?>">
          <?= csrf_field() ?>
          <button type="submit" class="text-xs <?= $slide['enabled'] ? 'text-slate-500 hover:text-slate-700' : 'text-emerald-600 hover:underline' ?>"><?= $slide['enabled'] ? 'Deactivate' : 'Activate' ?></button>
        </form>
        <a href="<?= e(url('admin/hero-slides/' . $slide['id'] . '/edit')) ?>" class="text-xs text-sky-600 hover:underline">Edit</a>
        <form method="POST" action="<?= e(url('admin/hero-slides/' . $slide['id'] . '/delete')) ?>" onsubmit="return confirm('Delete this slide?');">
          <?= csrf_field() ?>
          <button type="submit" class="text-xs text-red-600 hover:underline">Delete</button>
        </form>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if (empty($slides)): ?>
    <p class="text-sm text-slate-400">No hero slides yet. The homepage hero will be hidden until you add one.</p>
  <?php endif; ?>
</div>
