<?php
$qs = function (array $overrides) use ($search, $activeIndustry) {
    $params = array_merge(['tab' => 'companies', 'q' => $search, 'industry' => $activeIndustry], $overrides);
    $params = array_filter($params, fn ($v) => $v !== null && $v !== '');
    return '?' . http_build_query($params);
};
?>
  <form method="GET" action="<?= e(url('jobs')) ?>" class="flex flex-wrap items-center gap-3 mb-6">
    <input type="hidden" name="tab" value="companies">
    <div class="relative flex-1 min-w-[220px]">
      <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
      <input type="text" name="q" value="<?= e($search) ?>" placeholder="Search company..." class="form-input !pl-10">
    </div>
    <select name="industry" onchange="this.form.submit()" class="form-input max-w-[220px]">
      <option value="">All Industries</option>
      <?php foreach ($industries as $opt): ?>
        <option value="<?= e($opt) ?>" <?= $activeIndustry === $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn-gold !px-5 !py-2.5 text-sm">Search</button>
  </form>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
    <?php foreach ($companies as $company): ?>
      <div class="card p-4 relative">
        <a href="<?= e(url('companies/' . $company['id'])) ?>" class="absolute inset-0 z-0" aria-label="<?= e($company['name']) ?>"></a>
        <div class="relative z-0 pointer-events-none">
          <?php if (!empty($company['logo'])): ?>
            <img src="<?= e($company['logo']) ?>" alt="" class="w-12 h-12 object-contain rounded border border-slate-200 bg-white p-1 mb-3">
          <?php else: ?>
            <div class="w-12 h-12 rounded bg-slate-100 flex items-center justify-center text-primary-navy font-bold mb-3"><?= e(mb_substr($company['name'], 0, 2)) ?></div>
          <?php endif; ?>
          <h4 class="text-sm font-bold text-primary-navy truncate"><?= e($company['name']) ?></h4>
          <?php if (!empty($company['industry'])): ?><p class="text-xs text-slate-500 truncate"><?= e($company['industry']) ?></p><?php endif; ?>
          <?php if ($company['location']): ?><p class="text-xs text-slate-500 truncate mt-0.5"><i class="fa-solid fa-location-dot"></i> <?= e($company['location']) ?></p><?php endif; ?>
          <span class="badge-green mt-2 inline-block"><?= (int) $company['open_job_count'] ?> Open Job<?= (int) $company['open_job_count'] === 1 ? '' : 's' ?></span>
          <div class="mt-3 pointer-events-auto">
            <a href="<?= e(url('companies/' . $company['id'])) ?>" class="btn bg-white border border-gold !text-gold hover:bg-gold hover:!text-white !px-3 !py-1.5 text-xs w-full text-center block">View Jobs</a>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
    <?php if (empty($companies)): ?>
      <p class="text-sm text-slate-400 col-span-full">No companies are actively hiring right now. Check back soon.</p>
    <?php endif; ?>
  </div>

  <?php if ($totalPages > 1): ?>
    <div class="flex items-center justify-center gap-1.5 mt-8">
      <a href="<?= e(url('jobs') . $qs(['page' => max(1, $page - 1)])) ?>" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:text-primary-navy <?= $page <= 1 ? 'pointer-events-none opacity-40' : '' ?>"><i class="fa-solid fa-arrow-left text-xs"></i></a>
      <?php for ($p = 1; $p <= $totalPages; $p++): ?>
        <a href="<?= e(url('jobs') . $qs(['page' => $p])) ?>" class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-semibold <?= $p === $page ? 'bg-gold text-white' : 'border border-slate-200 text-slate-600 hover:bg-slate-50' ?>"><?= $p ?></a>
      <?php endfor; ?>
      <a href="<?= e(url('jobs') . $qs(['page' => min($totalPages, $page + 1)])) ?>" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:text-primary-navy <?= $page >= $totalPages ? 'pointer-events-none opacity-40' : '' ?>"><i class="fa-solid fa-arrow-right text-xs"></i></a>
    </div>
  <?php elseif ($total > 0): ?>
    <p class="text-center text-xs text-slate-400 mt-6"><?= number_format($total) ?> compan<?= $total === 1 ? 'y' : 'ies' ?> hiring</p>
  <?php endif; ?>
