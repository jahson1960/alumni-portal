<div class="max-w-[900px] mx-auto px-4 md:px-8 py-8">
  <a href="<?= e(url('jobs/post')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to Post a Job</a>

  <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
      <h1 class="text-xl font-extrabold text-primary-navy">Application Templates</h1>
      <p class="text-sm text-slate-500 mt-1">Build a reusable set of application questions once, then attach it to any of your job posts.</p>
    </div>
    <a href="<?= e(url('application-templates/create')) ?>" class="btn-gold !px-4 !py-2.5 text-sm">+ New Template</a>
  </div>

  <?php require dirname(__DIR__) . '/partials/errors.php'; ?>

  <div class="space-y-3">
    <?php foreach ($templates as $template): ?>
      <div class="card p-4 flex items-center justify-between gap-4">
        <div class="min-w-0">
          <h4 class="text-sm font-bold text-primary-navy truncate"><?= e($template['name']) ?></h4>
          <p class="text-xs text-slate-500"><?= $template['field_count'] ?> field<?= $template['field_count'] === 1 ? '' : 's' ?></p>
        </div>
        <div class="flex items-center gap-2 flex-shrink-0">
          <a href="<?= e(url('application-templates/' . $template['id'] . '/edit')) ?>" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-3 !py-1.5 text-xs">Edit</a>
          <form method="POST" action="<?= e(url('application-templates/' . $template['id'] . '/delete')) ?>" data-confirm="Delete this template? Job posts using it will fall back to showing their own description for how to apply.">
            <?= csrf_field() ?>
            <button type="submit" class="btn bg-white border border-slate-200 !text-red-600 hover:bg-red-50 !px-3 !py-1.5 text-xs">Delete</button>
          </form>
        </div>
      </div>
    <?php endforeach; ?>
    <?php if (empty($templates)): ?>
      <p class="text-sm text-slate-400">You haven't created any application templates yet. <a href="<?= e(url('application-templates/create')) ?>" class="text-gold hover:underline">Create one now</a>.</p>
    <?php endif; ?>
  </div>
</div>
