<?php
$authUser = \App\Core\Auth::user();
$qs = function (array $overrides) use ($filters, $sort, $perPage, $view) {
    $params = array_merge($filters, ['sort' => $sort, 'per_page' => $perPage, 'view' => $view], $overrides);
    $params = array_filter($params, fn ($v) => $v !== null && $v !== '');
    return '?' . http_build_query($params);
};
?>
<div class="max-w-[1400px] mx-auto px-4 md:px-8 py-8">

  <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
      <h1 class="text-2xl font-extrabold text-primary-navy">Alumni Directory</h1>
      <div class="w-10 h-1 bg-gold rounded-full my-2"></div>
      <p class="text-sm text-slate-500">Connect, collaborate, and grow with our global alumni community.</p>
    </div>
    <div class="flex flex-wrap items-center gap-3">
      <a href="<?= e(url('directory')) ?>" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-4 !py-2.5 text-xs"><i class="bi bi-people"></i> Browse all alumni</a>
      <div class="card !border-gold/40 px-4 py-2.5 flex items-center gap-3">
        <div class="w-9 h-9 rounded-full bg-gold/10 text-gold flex items-center justify-center flex-shrink-0"><i class="bi bi-people-fill"></i></div>
        <div>
          <p class="text-lg font-extrabold text-primary-navy leading-none"><?= number_format($total) ?></p>
          <p class="text-[0.65rem] text-slate-500">Alumni found</p>
        </div>
      </div>
    </div>
  </div>

  <button type="button" id="sidebar-toggle" class="lg:hidden btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !py-2.5 text-sm w-full flex items-center justify-center gap-2 mb-4">
    <i class="bi bi-funnel"></i> <span>Filters</span>
    <?php if ($filters): ?><span class="w-2 h-2 rounded-full bg-gold"></span><?php endif; ?>
    <i class="bi bi-chevron-down transition-transform" id="sidebar-toggle-icon"></i>
  </button>

  <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-6 items-start">

    <!-- Filter sidebar -->
    <aside id="sidebar-collapsible" class="hidden lg:block card p-5 lg:sticky lg:top-[calc(var(--header-height)+1rem)]">
      <div class="flex items-center justify-between mb-4">
        <h3 class="text-sm font-bold text-primary-navy flex items-center gap-2"><i class="bi bi-funnel text-slate-400"></i> Refine your search</h3>
        <?php if ($filters): ?>
          <a href="<?= e(url('directory') . ($view !== 'all' ? '?view=' . $view : '')) ?>" class="text-xs text-slate-500 hover:text-gold flex items-center gap-1"><i class="bi bi-arrow-clockwise"></i> Clear all</a>
        <?php endif; ?>
      </div>
      <form method="GET" action="<?= e(url('directory')) ?>" class="space-y-3">
        <input type="hidden" name="sort" value="<?= e($sort) ?>">
        <input type="hidden" name="per_page" value="<?= e($perPage) ?>">
        <input type="hidden" name="view" value="<?= e($view) ?>">

        <div class="relative">
          <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
          <input type="text" name="q" value="<?= e($filters['q'] ?? '') ?>" placeholder="Search by name, company, headline, programme, or class year..." class="form-input !pl-9 text-sm">
        </div>
        <?php if ($view === 'all' || $view === 'saved'): ?>
          <div class="relative">
            <i class="bi bi-star absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
            <input type="text" name="skills" value="<?= e($filters['skills'] ?? '') ?>" placeholder="Search by skill, expertise, or interest..." class="form-input !pl-9 text-sm">
          </div>
        <?php endif; ?>

        <?php if (!in_array($view, ['programme', 'cohort'], true)): ?>
          <div class="relative">
            <i class="bi bi-book absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm z-10"></i>
            <select name="program" class="form-input !pl-9 text-sm">
              <option value="">Programme</option>
              <?php foreach ($programOptions as $opt): ?>
                <option value="<?= e($opt) ?>" <?= ($filters['program'] ?? '') === $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        <?php endif; ?>
        <?php if ($view !== 'cohort'): ?>
          <div class="relative">
            <i class="bi bi-mortarboard absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm z-10"></i>
            <select name="graduation_year" class="form-input !pl-9 text-sm">
              <option value="">Graduation Year</option>
              <?php foreach ($yearOptions as $opt): ?>
                <option value="<?= e($opt) ?>" <?= (string) ($filters['graduation_year'] ?? '') === (string) $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        <?php endif; ?>
        <div class="relative">
          <i class="bi bi-briefcase absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm z-10"></i>
          <select name="industry" class="form-input !pl-9 text-sm">
            <option value="">Industry</option>
            <?php foreach ($industryOptions as $opt): ?>
              <option value="<?= e($opt) ?>" <?= ($filters['industry'] ?? '') === $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="relative">
          <i class="bi bi-building absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm z-10"></i>
          <select name="company" class="form-input !pl-9 text-sm">
            <option value="">Company</option>
            <?php foreach ($companyOptions as $opt): ?>
              <option value="<?= e($opt) ?>" <?= ($filters['company'] ?? '') === $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <?php if ($view !== 'country'): ?>
          <div class="relative">
            <i class="bi bi-globe absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm z-10"></i>
            <select name="country" class="form-input !pl-9 text-sm">
              <option value="">Country</option>
              <?php foreach ($countryOptions as $opt): ?>
                <option value="<?= e($opt) ?>" <?= ($filters['country'] ?? '') === $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
              <?php endforeach; ?>
            </select>
          </div>
        <?php endif; ?>
        <div class="relative">
          <i class="bi bi-geo-alt absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm z-10"></i>
          <select name="city" class="form-input !pl-9 text-sm">
            <option value="">City</option>
            <?php foreach ($cityOptions as $opt): ?>
              <option value="<?= e($opt) ?>" <?= ($filters['city'] ?? '') === $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="pt-2">
          <button type="submit" class="btn-gold !px-4 !py-2 text-xs w-full"><i class="bi bi-funnel"></i> Apply Filters</button>
        </div>
      </form>
    </aside>

    <!-- Main -->
    <div>
      <div class="flex flex-wrap items-center justify-between gap-3 mb-4 border-b border-slate-200">
        <div class="flex items-center gap-5 overflow-x-auto overflow-y-hidden min-w-0">
          <a href="<?= e(url('directory') . $qs(['view' => 'all', 'sort' => null, 'page' => null])) ?>" class="text-sm font-semibold pb-2.5 border-b-2 -mb-px whitespace-nowrap <?= $view === 'all' ? 'text-gold border-gold' : 'text-slate-500 border-transparent hover:text-primary-navy' ?>">All Alumni</a>
          <a href="<?= e(url('directory') . $qs(['view' => 'programme', 'sort' => null, 'page' => null, 'program' => null])) ?>" class="text-sm font-semibold pb-2.5 border-b-2 -mb-px whitespace-nowrap <?= $view === 'programme' ? 'text-gold border-gold' : 'text-slate-500 border-transparent hover:text-primary-navy' ?>">By Programme</a>
          <a href="<?= e(url('directory') . $qs(['view' => 'country', 'sort' => null, 'page' => null, 'country' => null])) ?>" class="text-sm font-semibold pb-2.5 border-b-2 -mb-px whitespace-nowrap <?= $view === 'country' ? 'text-gold border-gold' : 'text-slate-500 border-transparent hover:text-primary-navy' ?>">By Country</a>
          <a href="<?= e(url('directory') . $qs(['view' => 'cohort', 'sort' => null, 'page' => null, 'program' => null, 'graduation_year' => null])) ?>" class="text-sm font-semibold pb-2.5 border-b-2 -mb-px whitespace-nowrap <?= $view === 'cohort' ? 'text-gold border-gold' : 'text-slate-500 border-transparent hover:text-primary-navy' ?>">By Cohort</a>
          <?php if ($authUser): ?>
            <a href="<?= e(url('directory') . $qs(['view' => 'saved', 'sort' => null, 'page' => null])) ?>" class="text-sm font-semibold pb-2.5 border-b-2 -mb-px whitespace-nowrap flex items-center gap-1.5 <?= $view === 'saved' ? 'text-gold border-gold' : 'text-slate-500 border-transparent hover:text-primary-navy' ?>"><i class="bi bi-bookmark-fill"></i> Saved</a>
          <?php endif; ?>
        </div>
        <div class="flex items-center gap-3 pb-2.5">
          <form method="GET" action="<?= e(url('directory')) ?>" id="sort-form" class="flex items-center gap-1.5">
            <?php foreach (array_merge($filters, ['per_page' => $perPage, 'view' => $view]) as $k => $v): ?>
              <input type="hidden" name="<?= e($k) ?>" value="<?= e($v) ?>">
            <?php endforeach; ?>
            <label class="text-xs text-slate-400">Sort by:</label>
            <select name="sort" onchange="document.getElementById('sort-form').submit()" class="form-input !py-1.5 text-xs">
              <?php if ($view === 'all' || $view === 'saved' || $isDrilledIn): ?>
                <option value="recent" <?= $sort === 'recent' ? 'selected' : '' ?>>Recently Joined</option>
                <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>Name (A–Z)</option>
                <option value="name_desc" <?= $sort === 'name_desc' ? 'selected' : '' ?>>Name (Z–A)</option>
              <?php else: ?>
                <option value="most" <?= $sort === 'most' ? 'selected' : '' ?>>Most Alumni</option>
                <option value="fewest" <?= $sort === 'fewest' ? 'selected' : '' ?>>Fewest Alumni</option>
                <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>Name (A–Z)</option>
              <?php endif; ?>
            </select>
          </form>
          <div class="flex items-center border border-slate-200 rounded-lg overflow-hidden">
            <button type="button" id="grid-view-btn" class="px-2.5 py-1.5 bg-gold text-white"><i class="bi bi-grid-3x3-gap"></i></button>
            <button type="button" id="list-view-btn" class="px-2.5 py-1.5 bg-white text-slate-400 hover:text-primary-navy"><i class="bi bi-list-ul"></i></button>
          </div>
        </div>
      </div>

      <?php
      $renderCard = function (array $person) use ($authUser, $savedIds) {
        $isSelf = $authUser && (int) $authUser['id'] === (int) $person['id'];
        $isSaved = in_array((int) $person['id'], $savedIds, true);
        ?>
        <div class="directory-card card p-5 relative">
          <a href="<?= e(url('alumni/' . $person['id'])) ?>" class="absolute inset-0 z-0" aria-label="<?= e($person['name']) ?>"></a>
          <div class="relative z-10 flex items-start justify-between pointer-events-none">
            <?= avatar_html($person, 'w-12 h-12') ?>
            <?php if ($authUser && !$isSelf): ?>
              <div class="relative pointer-events-auto">
                <button type="button" class="kebab-btn text-slate-400 hover:text-primary-navy px-1"><i class="bi bi-three-dots-vertical"></i></button>
                <div class="kebab-menu hidden absolute right-0 top-full mt-1 z-20 bg-white border border-slate-200 rounded-lg shadow-lg w-40 py-1">
                  <a href="<?= e(url('alumni/' . $person['id'])) ?>" class="block px-3 py-2 text-xs text-slate-700 hover:bg-slate-50">View Profile</a>
                  <form method="POST" action="<?= e(url('connections/' . $person['id'] . '/request')) ?>">
                    <?= csrf_field() ?>
                    <button type="submit" class="w-full text-left px-3 py-2 text-xs text-slate-700 hover:bg-slate-50">Connect</button>
                  </form>
                </div>
              </div>
            <?php endif; ?>
          </div>

          <div class="relative z-10 pointer-events-none mt-3">
            <h4 class="text-sm font-bold text-primary-navy truncate"><?= e($person['name']) ?></h4>
            <p class="text-xs text-slate-500 truncate"><?= e($person['headline'] ?: '') ?></p>
            <?php if ($person['company']): ?><p class="text-xs text-slate-600 font-medium truncate"><?= e($person['company']) ?></p><?php endif; ?>

            <div class="flex flex-col gap-1 mt-2 text-[0.7rem] text-slate-500">
              <?php if ($person['program'] || $person['graduation_year']): ?>
                <span><i class="bi bi-mortarboard text-slate-400"></i> <?= e(trim(($person['program'] ?? '') . ($person['graduation_year'] ? ' — ' . $person['graduation_year'] : ''))) ?></span>
              <?php endif; ?>
              <?php if (location_display($person)): ?>
                <span><i class="bi bi-geo-alt text-slate-400"></i> <?= e(location_display($person)) ?></span>
              <?php endif; ?>
            </div>

            <?php
            $tags = array_slice(array_filter(array_map('trim', explode(',', (string) ($person['skills'] ?? $person['expertise_areas'] ?? '')))), 0, 3);
            ?>
            <?php if ($tags): ?>
              <div class="flex flex-wrap gap-1.5 mt-3">
                <?php foreach ($tags as $tag): ?>
                  <span class="text-[0.65rem] font-semibold text-slate-600 border border-slate-200 rounded-full px-2 py-0.5"><?= e($tag) ?></span>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <div class="flex items-center justify-between mt-4 pt-3 border-t border-slate-100 pointer-events-auto">
              <a href="<?= e(url('alumni/' . $person['id'])) ?>" class="btn bg-white border border-gold !text-gold hover:bg-gold hover:!text-white !px-3 !py-1.5 text-xs">View Profile <i class="fa-solid fa-arrow-right"></i></a>
              <?php if ($authUser && !$isSelf): ?>
                <form method="POST" action="<?= e(url('alumni/' . $person['id'] . '/save')) ?>" class="save-alumni-form">
                  <?= csrf_field() ?>
                  <button type="submit" class="save-alumni-btn text-sm <?= $isSaved ? 'text-gold' : 'text-slate-300 hover:text-gold' ?>" title="<?= $isSaved ? 'Unsave' : 'Save' ?>"><i class="bi <?= $isSaved ? 'bi-bookmark-fill' : 'bi-bookmark' ?>"></i></button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        </div>
        <?php
      };

      $renderPagination = function () use ($totalPages, $page, $qs, $perPage, $filters, $sort, $view) {
        if ($totalPages <= 1) {
            return;
        }
        ?>
        <div class="flex flex-wrap items-center justify-between gap-3 mt-6">
          <div class="flex items-center gap-1.5 overflow-x-auto overflow-y-hidden max-w-full">
            <a href="<?= e(url('directory') . $qs(['page' => max(1, $page - 1)])) ?>" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:text-primary-navy flex-shrink-0 <?= $page <= 1 ? 'pointer-events-none opacity-40' : '' ?>"><i class="fa-solid fa-arrow-left text-xs"></i></a>
            <?php for ($p = 1; $p <= $totalPages; $p++): ?>
              <a href="<?= e(url('directory') . $qs(['page' => $p])) ?>" class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-semibold flex-shrink-0 <?= $p === $page ? 'bg-gold text-white' : 'border border-slate-200 text-slate-600 hover:bg-slate-50' ?>"><?= $p ?></a>
            <?php endfor; ?>
            <a href="<?= e(url('directory') . $qs(['page' => min($totalPages, $page + 1)])) ?>" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:text-primary-navy flex-shrink-0 <?= $page >= $totalPages ? 'pointer-events-none opacity-40' : '' ?>"><i class="fa-solid fa-arrow-right text-xs"></i></a>
          </div>
          <form method="GET" action="<?= e(url('directory')) ?>" id="per-page-form" class="flex items-center gap-1.5">
            <?php foreach ($filters as $k => $v): ?><input type="hidden" name="<?= e($k) ?>" value="<?= e($v) ?>"><?php endforeach; ?>
            <input type="hidden" name="sort" value="<?= e($sort) ?>">
            <input type="hidden" name="view" value="<?= e($view) ?>">
            <span class="text-xs text-slate-400">Show</span>
            <select name="per_page" onchange="document.getElementById('per-page-form').submit()" class="form-input !py-1.5 text-xs">
              <?php foreach ([12, 24, 48] as $opt): ?>
                <option value="<?= $opt ?>" <?= $perPage === $opt ? 'selected' : '' ?>><?= $opt ?></option>
              <?php endforeach; ?>
            </select>
            <span class="text-xs text-slate-400">per page</span>
          </form>
        </div>
        <?php
      };
      ?>

      <?php if ($view === 'all' || $view === 'saved' || $isDrilledIn): ?>
        <div id="directory-results" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
          <?php foreach ($alumni as $person): $renderCard($person); ?><?php endforeach; ?>
          <?php if (empty($alumni)): ?>
            <p class="text-sm text-slate-400 col-span-full"><?= $view === 'saved' ? "You haven't saved any alumni yet. Use the bookmark icon on a profile card to save it here." : 'No alumni found matching those filters.' ?></p>
          <?php endif; ?>
        </div>
        <?php $renderPagination(); ?>

      <?php elseif ($view === 'programme'): ?>
        <h3 class="text-sm font-bold text-primary-navy mb-4">Browse alumni by programme</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <?php foreach ($groups as $group): ?>
            <div class="card p-5">
              <div class="w-11 h-11 rounded-full bg-gold/10 text-gold flex items-center justify-center text-lg mb-3"><i class="bi bi-mortarboard"></i></div>
              <h4 class="text-sm font-bold text-primary-navy mb-0.5"><?= e($group['label']) ?></h4>
              <p class="text-xs font-semibold text-gold mb-2"><?= $group['count'] ?> alumni</p>
              <p class="text-xs text-slate-500 mb-3 leading-relaxed"><?= e(programme_description($group['label'])) ?></p>
              <a href="<?= e(url('directory?view=programme&program=' . urlencode($group['label']))) ?>" class="text-xs font-semibold text-gold hover:underline">View Alumni &rarr;</a>
            </div>
          <?php endforeach; ?>
          <?php if (empty($groups)): ?>
            <p class="text-sm text-slate-400 col-span-full">No programme data available yet.</p>
          <?php endif; ?>
        </div>
        <?php $renderPagination(); ?>

      <?php elseif ($view === 'country'): ?>
        <h3 class="text-sm font-bold text-primary-navy mb-4">Browse alumni by country</h3>
        <div class="grid grid-cols-1 lg:grid-cols-[1fr_1fr] gap-6">
          <div class="card p-3">
            <div id="world-map" style="height: 340px;"></div>
          </div>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 content-start">
            <?php foreach ($groups as $group): $iso = country_iso2($group['label']); ?>
              <a href="<?= e(url('directory?view=country&country=' . urlencode($group['label']))) ?>" class="card p-4 flex items-center gap-3">
                <span class="flex-shrink-0 flex items-center justify-center w-6 h-[18px]"><?= $iso ? country_flag_html($iso) : '<span class="text-xl leading-none">🌍</span>' ?></span>
                <div class="min-w-0">
                  <p class="text-sm font-bold text-primary-navy truncate"><?= e($group['label']) ?></p>
                  <p class="text-xs text-slate-500"><?= $group['count'] ?> alumni</p>
                </div>
              </a>
            <?php endforeach; ?>
            <?php if (empty($groups)): ?>
              <p class="text-sm text-slate-400 col-span-full">No country data available yet.</p>
            <?php endif; ?>
          </div>
        </div>
        <?php $renderPagination(); ?>

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/css/jsvectormap.min.css">
        <script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/js/jsvectormap.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/jsvectormap@1.5.3/dist/maps/world.js"></script>
        <script>
          (function () {
            var counts = <?= json_encode($mapCounts) ?>;
            var names = <?= json_encode(array_reduce($groups, function ($acc, $g) {
                $iso = country_iso2($g['label']);
                if ($iso) {
                    $acc[$iso] = $g['label'];
                }
                return $acc;
            }, [])) ?>;
            var values = {};
            Object.keys(counts).forEach(function (k) { values[k] = counts[k]; });

            // Colors follow the admin-configured "Map" theme color (see Site Settings > Theme Colors),
            // injected as --map-* CSS variables in the page <head>.
            var themeVars = getComputedStyle(document.documentElement);
            function themeColor(name, fallback) {
              var v = themeVars.getPropertyValue(name).trim();
              return v || fallback;
            }

            new jsVectorMap({
              selector: '#world-map',
              map: 'world',
              zoomButtons: false,
              zoomOnScroll: false,
              regionStyle: {
                initial: { fill: themeColor('--map-50', '#f1e2bf'), stroke: themeColor('--map-100', '#e8d3a3') },
                hover: { fill: themeColor('--map-700', '#b87e1d'), cursor: 'pointer' }
              },
              series: {
                regions: [{
                  values: values,
                  scale: [themeColor('--map-200', '#f2d999'), themeColor('--map-900', '#8a5c14')],
                  normalizeFunction: 'polynomial'
                }]
              },
              onRegionTooltipShow: function (event, tooltip, code) {
                var name = names[code] || tooltip.text();
                tooltip.text(values[code] ? (name + ': ' + values[code] + ' alumni') : name);
              }
            });
          })();
        </script>

      <?php else: ?>
        <h3 class="text-sm font-bold text-primary-navy mb-4">Browse alumni by cohort</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
          <?php foreach ($groups as $group): ?>
            <div class="card p-5">
              <div class="w-11 h-11 rounded-full bg-gold/10 text-gold flex items-center justify-center text-lg mb-3"><i class="bi bi-people"></i></div>
              <h4 class="text-sm font-bold text-primary-navy mb-0.5"><?= e($group['program']) ?></h4>
              <p class="text-xs text-slate-500 mb-2">Class of <?= e($group['year']) ?></p>
              <p class="text-xs font-semibold text-gold mb-3"><?= $group['count'] ?> alumni</p>
              <a href="<?= e(url('directory?view=cohort&program=' . urlencode($group['program']) . '&graduation_year=' . urlencode($group['year']))) ?>" class="text-xs font-semibold text-gold hover:underline">View Alumni &rarr;</a>
            </div>
          <?php endforeach; ?>
          <?php if (empty($groups)): ?>
            <p class="text-sm text-slate-400 col-span-full">No cohort data available yet.</p>
          <?php endif; ?>
        </div>
        <?php $renderPagination(); ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
  (function () {
    var sidebarToggle = document.getElementById('sidebar-toggle');
    var sidebarIcon = document.getElementById('sidebar-toggle-icon');
    var sidebar = document.getElementById('sidebar-collapsible');
    if (sidebarToggle && sidebar) {
      sidebarToggle.addEventListener('click', function () {
        var isOpen = sidebar.classList.toggle('hidden') === false;
        sidebarIcon.classList.toggle('rotate-180', isOpen);
      });
    }

    document.querySelectorAll('.save-alumni-form').forEach(function (form) {
      form.addEventListener('submit', function (e) {
        e.preventDefault();
        var btn = form.querySelector('.save-alumni-btn');
        var icon = btn.querySelector('i');
        fetch(form.action, {
          method: 'POST',
          headers: { 'X-Requested-With': 'XMLHttpRequest' },
          body: new FormData(form)
        })
          .then(function (r) { return r.json(); })
          .then(function (data) {
            btn.classList.toggle('text-gold', data.saved);
            btn.classList.toggle('text-slate-300', !data.saved);
            btn.classList.toggle('hover:text-gold', !data.saved);
            btn.title = data.saved ? 'Unsave' : 'Save';
            icon.classList.toggle('bi-bookmark-fill', data.saved);
            icon.classList.toggle('bi-bookmark', !data.saved);
          });
      });
    });

    document.querySelectorAll('.kebab-btn').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.stopPropagation();
        var menu = btn.nextElementSibling;
        document.querySelectorAll('.kebab-menu').forEach(function (m) { if (m !== menu) m.classList.add('hidden'); });
        menu.classList.toggle('hidden');
      });
    });
    document.addEventListener('click', function () {
      document.querySelectorAll('.kebab-menu').forEach(function (m) { m.classList.add('hidden'); });
    });

    var results = document.getElementById('directory-results');
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

<script>
  (function () {
    // Live online/offline status for every alumni avatar dot on this page.
    var pollUrl = <?= json_encode(url('directory/online-status')) ?>;

    function applyStatus(id, online) {
      document.querySelectorAll('[data-online-dot="' + id + '"]').forEach(function (dot) {
        dot.classList.toggle('bg-green-500', online);
        dot.classList.toggle('bg-slate-300', !online);
        dot.title = online ? 'Online' : 'Offline';
      });
    }

    function poll() {
      var ids = new Set();
      document.querySelectorAll('[data-online-dot]').forEach(function (el) { ids.add(el.dataset.onlineDot); });
      if (ids.size === 0) return;

      fetch(pollUrl + '?ids=' + Array.from(ids).join(','))
        .then(function (r) { return r.json(); })
        .then(function (data) {
          Object.keys(data).forEach(function (id) { applyStatus(id, data[id]); });
        })
        .catch(function () {});
    }

    setInterval(poll, 20000);
  })();
</script>

<script>
  (function () {
    // Switching tabs, sorting, paging, or filtering here is a real navigation (a fresh page
    // load), which browsers scroll to the top of by default — jarring if you were scrolled
    // down looking at results. Remember the scroll position before leaving, restore it after.
    var STORAGE_KEY = 'directoryScrollY';

    document.addEventListener('click', function (e) {
      var link = e.target.closest('a[href]');
      if (link && link.href.indexOf('directory') !== -1) {
        sessionStorage.setItem(STORAGE_KEY, String(window.scrollY));
      }
    });

    document.querySelectorAll('form[action*="directory"]').forEach(function (form) {
      form.addEventListener('submit', function () {
        sessionStorage.setItem(STORAGE_KEY, String(window.scrollY));
      });
    });

    var saved = sessionStorage.getItem(STORAGE_KEY);
    if (saved !== null) {
      sessionStorage.removeItem(STORAGE_KEY);
      window.scrollTo(0, parseInt(saved, 10));
    }
  })();
</script>
