<?php
$prefix = $prefix ?? '';
$perPageOptions = [30, 50, 100, 200];
$rangeStart = $totalCount > 0 ? ($page - 1) * $perPage + 1 : 0;
$rangeEnd = min($page * $perPage, $totalCount);
?>
<div class="flex flex-wrap items-center justify-between gap-3 mb-3">
  <div class="flex items-center gap-2 text-xs text-slate-500">
    <label for="<?= e($prefix) ?>per-page-select">Rows per page</label>
    <select id="<?= e($prefix) ?>per-page-select" class="form-input !py-1 !text-xs !w-auto" onchange="window.location = this.value">
      <?php foreach ($perPageOptions as $opt): ?>
        <option value="<?= e(admin_qs(['per_page' => $opt, 'page' => 1], $prefix)) ?>" <?= $perPage === $opt ? 'selected' : '' ?>><?= $opt ?></option>
      <?php endforeach; ?>
    </select>
    <span>&middot; <?= $totalCount > 0 ? 'Showing ' . number_format($rangeStart) . '&ndash;' . number_format($rangeEnd) . ' of ' . number_format($totalCount) : 'No results' ?></span>
  </div>

  <?php if ($totalPages > 1): ?>
    <div class="flex items-center gap-1.5">
      <a href="<?= e(admin_qs(['page' => max(1, $page - 1)], $prefix)) ?>" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:text-primary-navy <?= $page <= 1 ? 'pointer-events-none opacity-40' : '' ?>"><i class="fa-solid fa-arrow-left text-xs"></i></a>
      <?php for ($p = max(1, $page - 2); $p <= min($totalPages, $page + 2); $p++): ?>
        <a href="<?= e(admin_qs(['page' => $p], $prefix)) ?>" class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-semibold <?= $p === $page ? 'bg-gold text-white' : 'border border-slate-200 text-slate-600 hover:bg-slate-50' ?>"><?= $p ?></a>
      <?php endfor; ?>
      <a href="<?= e(admin_qs(['page' => min($totalPages, $page + 1)], $prefix)) ?>" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:text-primary-navy <?= $page >= $totalPages ? 'pointer-events-none opacity-40' : '' ?>"><i class="fa-solid fa-arrow-right text-xs"></i></a>
    </div>
  <?php endif; ?>
</div>
