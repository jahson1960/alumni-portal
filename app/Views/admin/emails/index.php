<?php
$emailTabs = [
    'queue' => ['label' => 'Queue', 'icon' => 'fa-solid fa-inbox'],
    'settings' => ['label' => 'Delivery Settings', 'icon' => 'fa-solid fa-sliders'],
    'connection' => ['label' => 'SMTP Connection', 'icon' => 'fa-solid fa-plug'],
];
?>
<div class="card !rounded-xl !p-2 flex flex-nowrap items-center gap-1 overflow-x-auto overflow-y-hidden mb-6">
  <?php foreach ($emailTabs as $key => $meta): ?>
    <a href="<?= e(url('admin/emails') . ($key === 'queue' ? '' : '?tab=' . $key)) ?>" class="flex items-center gap-2 px-4 py-3 text-sm font-semibold whitespace-nowrap border-b-2 -mb-px <?= $tab === $key ? 'text-gold border-gold' : 'text-slate-600 border-transparent hover:text-primary-navy' ?>">
      <i class="<?= e($meta['icon']) ?>"></i> <?= e($meta['label']) ?>
    </a>
  <?php endforeach; ?>
</div>

<?php require __DIR__ . '/tabs/' . $tab . '.php'; ?>
