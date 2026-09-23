<?php
$authUser = \App\Core\Auth::user();
$qs = function (array $overrides) use ($filters, $sort, $perPage) {
    $params = array_merge($filters, ['sort' => $sort, 'per_page' => $perPage], $overrides);
    $params = array_filter($params, fn ($v) => $v !== null && $v !== '');
    return '?' . http_build_query($params);
};
$activeCategory = $filters['category'] ?? null;
?>
<div class="max-w-[1400px] mx-auto px-4 md:px-8 py-8">

  <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div class="flex items-center gap-4">
      <div class="w-12 h-12 rounded-xl bg-gold/10 text-gold flex items-center justify-center text-xl flex-shrink-0"><i class="fa-solid fa-briefcase"></i></div>
      <div>
        <h1 class="text-2xl font-extrabold text-primary-navy">Alumni Businesses</h1>
        <p class="text-sm text-slate-500">Discover businesses founded or led by our talented alumni.</p>
      </div>
    </div>
    <?php if ($authUser): ?>
      <a href="<?= e(url('businesses/mine')) ?>" class="btn bg-white border border-slate-200 !text-sky-600 hover:bg-slate-50 !px-4 !py-2.5 text-xs">My Businesses <i class="fa-solid fa-arrow-right"></i></a>
    <?php endif; ?>
  </div>

  <div class="flex flex-nowrap items-center gap-5 mb-6 border-b border-slate-200 overflow-x-auto overflow-y-hidden">
    <a href="<?= e(url('businesses') . $qs(['category' => null, 'page' => null])) ?>" class="text-sm font-semibold pb-2.5 border-b-2 -mb-px whitespace-nowrap <?= $activeCategory === null ? 'text-gold border-gold' : 'text-slate-500 border-transparent hover:text-primary-navy' ?>">All Businesses</a>
    <?php foreach ($categories as $cat): ?>
      <a href="<?= e(url('businesses') . $qs(['category' => $cat, 'page' => null])) ?>" class="text-sm font-semibold pb-2.5 border-b-2 -mb-px whitespace-nowrap <?= $activeCategory === $cat ? 'text-gold border-gold' : 'text-slate-500 border-transparent hover:text-primary-navy' ?>"><?= e($cat) ?></a>
    <?php endforeach; ?>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-6 items-start">

    <!-- Filter sidebar -->
    <aside class="card p-5 lg:sticky lg:top-[calc(var(--header-height)+1rem)] lg:max-h-[calc(100vh-var(--header-height)-2rem)] lg:overflow-y-auto">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-bold text-primary-navy flex items-center gap-2"><i class="bi bi-funnel text-slate-400"></i> Refine your search</h3>
        <?php if ($filters): ?>
          <a href="<?= e(url('businesses')) ?>" class="text-xs text-slate-500 hover:text-gold flex items-center gap-1"><i class="bi bi-arrow-clockwise"></i> Clear all</a>
        <?php endif; ?>
      </div>
      <form method="GET" action="<?= e(url('businesses')) ?>" class="space-y-3">
        <input type="hidden" name="sort" value="<?= e($sort) ?>">
        <input type="hidden" name="per_page" value="<?= e($perPage) ?>">

        <div class="relative">
          <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
          <input type="text" name="q" value="<?= e($filters['q'] ?? '') ?>" placeholder="Search business name, founder, industry..." class="form-input !pl-9 text-sm">
        </div>
        <div class="relative">
          <i class="bi bi-briefcase absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm z-10"></i>
          <select name="category" class="form-input !pl-9 text-sm">
            <option value="">Industry</option>
            <?php foreach ($categories as $opt): ?>
              <option value="<?= e($opt) ?>" <?= ($filters['category'] ?? '') === $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="relative">
          <i class="bi bi-geo-alt absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm z-10"></i>
          <select name="location" class="form-input !pl-9 text-sm">
            <option value="">Location</option>
            <?php foreach ($locationOptions as $opt): ?>
              <option value="<?= e($opt) ?>" <?= ($filters['location'] ?? '') === $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="relative">
          <i class="bi bi-building absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm z-10"></i>
          <select name="business_type" class="form-input !pl-9 text-sm">
            <option value="">Business Type</option>
            <?php foreach ($businessTypeOptions as $opt): ?>
              <option value="<?= e($opt) ?>" <?= ($filters['business_type'] ?? '') === $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="relative">
          <i class="bi bi-calendar3 absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm z-10"></i>
          <select name="founded_year" class="form-input !pl-9 text-sm">
            <option value="">Founded Year</option>
            <?php foreach ($foundedYearOptions as $opt): ?>
              <option value="<?= e($opt) ?>" <?= (string) ($filters['founded_year'] ?? '') === (string) $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="pt-2 flex items-center gap-3">
          <button type="submit" class="btn-gold !px-4 !py-2 text-xs flex-1"><i class="bi bi-funnel"></i> Apply Filters</button>
          <?php if ($filters): ?>
            <a href="<?= e(url('businesses')) ?>" class="text-xs text-slate-500 hover:text-gold flex items-center gap-1 whitespace-nowrap"><i class="bi bi-arrow-clockwise"></i> Clear all</a>
          <?php endif; ?>
        </div>
      </form>
    </aside>

    <!-- Main -->
    <div>
      <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <p class="text-sm text-slate-500">Showing <span class="font-semibold text-primary-navy"><?= number_format($total) ?></span> businesses</p>
        <div class="flex items-center gap-3">
          <form method="GET" action="<?= e(url('businesses')) ?>" id="sort-form" class="flex items-center gap-1.5">
            <?php foreach (array_merge($filters, ['per_page' => $perPage]) as $k => $v): ?>
              <input type="hidden" name="<?= e($k) ?>" value="<?= e($v) ?>">
            <?php endforeach; ?>
            <label class="text-xs text-slate-400">Sort by:</label>
            <select name="sort" onchange="document.getElementById('sort-form').submit()" class="form-input !py-1.5 text-xs">
              <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>A&ndash;Z</option>
              <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Newest</option>
              <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Oldest</option>
            </select>
          </form>
          <div class="flex items-center border border-slate-200 rounded-lg overflow-hidden">
            <button type="button" id="grid-view-btn" class="px-2.5 py-1.5 bg-gold text-white"><i class="bi bi-grid-3x3-gap"></i></button>
            <button type="button" id="list-view-btn" class="px-2.5 py-1.5 bg-white text-slate-400 hover:text-primary-navy"><i class="bi bi-list-ul"></i></button>
          </div>
        </div>
      </div>

      <div id="business-results" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($businesses as $biz):
          $isSaved = in_array((int) $biz['id'], $savedIds, true);
          $avatarColors = palette_classes($biz['name']);
        ?>
          <div class="card p-5 relative">
            <a href="<?= e(url('businesses/' . $biz['id'])) ?>" class="absolute inset-0 z-0" aria-label="<?= e($biz['name']) ?>"></a>
            <div class="relative z-10 flex items-start justify-between pointer-events-none">
              <?php if (!empty($biz['logo'])): ?>
                <img src="<?= e($biz['logo']) ?>" alt="" class="w-12 h-12 object-contain rounded-lg border border-slate-200 bg-white p-1 flex-shrink-0">
              <?php else: ?>
                <div class="w-12 h-12 rounded-lg <?= e($avatarColors['bg']) ?> <?= e($avatarColors['text']) ?> flex items-center justify-center font-bold text-sm flex-shrink-0"><?= e(mb_strtoupper(mb_substr($biz['name'], 0, 1)) . mb_strtolower(mb_substr($biz['name'], 1, 1))) ?></div>
              <?php endif; ?>
              <?php if ($authUser): ?>
                <form method="POST" action="<?= e(url('businesses/' . $biz['id'] . '/save')) ?>" class="pointer-events-auto">
                  <?= csrf_field() ?>
                  <button type="submit" class="text-sm <?= $isSaved ? 'text-gold' : 'text-slate-300 hover:text-gold' ?>" title="<?= $isSaved ? 'Unsave' : 'Save' ?>"><i class="bi <?= $isSaved ? 'bi-bookmark-fill' : 'bi-bookmark' ?>"></i></button>
                </form>
              <?php endif; ?>
            </div>

            <div class="relative z-0 pointer-events-none mt-3">
              <h4 class="text-sm font-bold text-primary-navy truncate"><?= e($biz['name']) ?></h4>
              <p class="text-xs text-slate-500 truncate">Founded by <?= e($biz['owner_name']) ?></p>

              <?php if ($biz['category']):
                $badgeColors = palette_classes($biz['category']);
              ?>
                <span class="inline-block mt-2 px-2 py-0.5 rounded text-[10px] font-bold uppercase <?= e($badgeColors['bg']) ?> <?= e($badgeColors['text']) ?>"><?= e($biz['category']) ?></span>
              <?php endif; ?>

              <?php if ($biz['location']): ?>
                <p class="text-xs text-slate-400 mt-2"><i class="bi bi-geo-alt"></i> <?= e($biz['location']) ?></p>
              <?php endif; ?>

              <div class="mt-4 pt-3 border-t border-slate-100 pointer-events-auto">
                <a href="<?= e(url('businesses/' . $biz['id'])) ?>" class="text-xs font-semibold text-gold hover:underline">View Profile <i class="fa-solid fa-arrow-right"></i></a>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if (empty($businesses)): ?>
          <p class="text-sm text-slate-400 col-span-full">No businesses found matching those filters.</p>
        <?php endif; ?>
      </div>

      <?php if ($totalPages > 1): ?>
        <div class="flex flex-wrap items-center justify-between gap-3 mt-6">
          <div class="flex items-center gap-1.5">
            <a href="<?= e(url('businesses') . $qs(['page' => max(1, $page - 1)])) ?>" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:text-primary-navy <?= $page <= 1 ? 'pointer-events-none opacity-40' : '' ?>"><i class="fa-solid fa-arrow-left text-xs"></i></a>
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
              <a href="<?= e(url('businesses') . $qs(['page' => $p])) ?>" class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-semibold <?= $p === $page ? 'bg-gold text-white' : 'border border-slate-200 text-slate-600 hover:bg-slate-50' ?>"><?= $p ?></a>
            <?php endfor; ?>
            <a href="<?= e(url('businesses') . $qs(['page' => min($totalPages, $page + 1)])) ?>" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:text-primary-navy <?= $page >= $totalPages ? 'pointer-events-none opacity-40' : '' ?>"><i class="fa-solid fa-arrow-right text-xs"></i></a>
          </div>
          <form method="GET" action="<?= e(url('businesses')) ?>" id="per-page-form" class="flex items-center gap-1.5">
            <?php foreach ($filters as $k => $v): ?><input type="hidden" name="<?= e($k) ?>" value="<?= e($v) ?>"><?php endforeach; ?>
            <input type="hidden" name="sort" value="<?= e($sort) ?>">
            <span class="text-xs text-slate-400">Show</span>
            <select name="per_page" onchange="document.getElementById('per-page-form').submit()" class="form-input !py-1.5 text-xs">
              <?php foreach ([9, 18, 36] as $opt): ?>
                <option value="<?= $opt ?>" <?= $perPage === $opt ? 'selected' : '' ?>><?= $opt ?></option>
              <?php endforeach; ?>
            </select>
            <span class="text-xs text-slate-400">per page</span>
          </form>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
  (function () {
    var results = document.getElementById('business-results');
    var gridBtn = document.getElementById('grid-view-btn');
    var listBtn = document.getElementById('list-view-btn');
    if (results && gridBtn && listBtn) {
      gridBtn.addEventListener('click', function () {
        results.className = 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4';
        gridBtn.classList.add('bg-gold', 'text-white');
        gridBtn.classList.remove('bg-white', 'text-slate-400');
        listBtn.classList.remove('bg-gold', 'text-white');
        listBtn.classList.add('bg-white', 'text-slate-400');
      });
      listBtn.addEventListener('click', function () {
        results.className = 'grid grid-cols-1 gap-3';
        listBtn.classList.add('bg-gold', 'text-white');
        listBtn.classList.remove('bg-white', 'text-slate-400');
        gridBtn.classList.remove('bg-gold', 'text-white');
        gridBtn.classList.add('bg-white', 'text-slate-400');
      });
    }
  })();
</script>
