<div class="card !rounded-xl !p-2 flex flex-nowrap items-center gap-1 overflow-x-auto overflow-y-hidden mb-6">
  <?php foreach ($tabs as $groupTab): ?>
    <a href="<?= e($groupTab['href']) ?>" class="flex items-center gap-2 px-4 py-3 text-sm font-semibold whitespace-nowrap border-b-2 -mb-px <?= ($activeNav ?? '') === $groupTab['key'] ? 'text-gold border-gold' : 'text-slate-600 border-transparent hover:text-primary-navy' ?>">
      <i class="<?= e($groupTab['icon']) ?>"></i> <?= e($groupTab['label']) ?>
    </a>
  <?php endforeach; ?>
</div>
