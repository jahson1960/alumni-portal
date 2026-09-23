<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
  <div>
    <h1 class="text-2xl font-extrabold text-primary-navy">Career Resources</h1>
    <p class="text-sm text-slate-500 mt-1">Tools, guides and templates to help you grow your career and build your future.</p>
  </div>
  <div class="flex items-center gap-2 flex-shrink-0 w-full sm:w-auto">
    <form method="GET" action="<?= e(url('resources')) ?>" class="flex-1 sm:flex-initial">
      <input type="hidden" name="tab" value="career">
      <div class="relative">
        <input type="text" name="q" value="<?= e($filters['q'] ?? '') ?>" placeholder="Search resources..." class="form-input !pr-9 w-full sm:w-64">
        <button type="submit" class="absolute right-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"><i class="fa-solid fa-magnifying-glass"></i></button>
      </div>
    </form>
    <a href="#type-filter" class="sm:hidden w-[42px] h-[42px] rounded-lg border border-slate-200 flex items-center justify-center text-slate-500 hover:text-gold hover:border-gold flex-shrink-0" title="Filters"><i class="fa-solid fa-sliders"></i></a>
  </div>
</div>

<?php require __DIR__ . '/../partials/tab_switcher.php'; ?>

<?php if (!empty($featured)): ?>
  <div class="flex items-center justify-between mb-3">
    <h3 class="text-sm font-bold text-primary-navy flex items-center gap-2"><i class="fa-solid fa-star text-amber-400"></i> Featured Resources</h3>
    <a href="<?= e(url('resources') . '?tab=career&featured=1') ?>" class="text-xs font-semibold text-gold hover:underline">View all</a>
  </div>
  <div id="featured-carousel" class="flex overflow-x-auto snap-x snap-mandatory gap-4 pb-1 -mx-4 px-4 md:mx-0 md:px-0 md:grid md:grid-cols-3 md:overflow-visible" style="scrollbar-width:none;">
    <?php
    $gradients = ['bg-gradient-to-br from-emerald-100 to-emerald-50', 'bg-gradient-to-br from-sky-100 to-sky-50', 'bg-gradient-to-br from-violet-100 to-violet-50'];
    foreach ($featured as $i => $res): $isSaved = in_array((int) $res['id'], $savedIds, true);
    ?>
      <div class="featured-slide snap-center flex-shrink-0 w-[82%] sm:w-[60%] md:w-auto" data-index="<?= $i ?>">
        <div class="card overflow-hidden relative h-full">
          <a href="<?= e(url('resources/' . $res['id'])) ?>" class="absolute inset-0 z-0" aria-label="<?= e($res['title']) ?>"></a>
          <div class="pointer-events-none h-28 flex items-center justify-center <?= e($gradients[$i % count($gradients)]) ?>">
            <i class="fa-solid <?= e(resource_type_icon($res['resource_type'])) ?> text-3xl text-white/80"></i>
          </div>
          <div class="p-4 pointer-events-none">
            <span class="badge-green mb-2 inline-block">FEATURED</span>
            <h4 class="text-sm font-bold text-primary-navy mb-1"><?= e($res['title']) ?></h4>
            <p class="text-xs text-slate-500 mb-3 line-clamp-2"><?= e($res['description']) ?></p>
            <div class="flex items-center justify-between pointer-events-auto relative z-10">
              <span class="flex items-center gap-3 text-[0.68rem] text-slate-500">
                <span><i class="fa-regular fa-file-lines"></i> <?= e($res['resource_type'] ?: 'Guide') ?></span>
                <?php if ($res['read_time_minutes']): ?><span><i class="fa-regular fa-clock"></i> <?= (int) $res['read_time_minutes'] ?> min read</span><?php endif; ?>
              </span>
              <?php if (\App\Core\Auth::check()): ?>
                <form method="POST" action="<?= e(url('resources/' . $res['id'] . '/save')) ?>">
                  <?= csrf_field() ?>
                  <button type="submit" class="flex items-center gap-1 text-xs <?= $isSaved ? 'text-gold' : 'text-slate-400 hover:text-gold' ?>"><i class="bi <?= $isSaved ? 'bi-bookmark-fill' : 'bi-bookmark' ?>"></i> <?= $isSaved ? 'Saved' : 'Save' ?></button>
                </form>
              <?php endif; ?>
            </div>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
  <?php if (count($featured) > 1): ?>
    <div class="flex items-center justify-center gap-1.5 mt-3 mb-5 md:hidden" id="featured-dots">
      <?php foreach ($featured as $i => $res): ?>
        <span class="featured-dot h-1.5 rounded-full transition-all cursor-pointer <?= $i === 0 ? 'bg-gold w-4' : 'bg-slate-300 w-1.5' ?>" data-dot="<?= $i ?>"></span>
      <?php endforeach; ?>
    </div>
  <?php else: ?>
    <div class="mb-8"></div>
  <?php endif; ?>
<?php endif; ?>

<div class="grid grid-cols-1 lg:grid-cols-[240px_1fr] gap-6 items-start">
  <aside class="card p-2">
    <h3 class="text-xs font-bold text-slate-500 uppercase tracking-wide px-3 pt-2 pb-1">Browse by Category</h3>
    <a href="<?= e(url('resources?tab=career') . '#resource-list') ?>" class="flex items-center justify-between gap-2 px-3 py-2.5 rounded-lg text-sm font-semibold <?= empty($filters['category']) ? 'bg-gold/10 text-gold' : 'text-slate-600 hover:bg-slate-50' ?>">
      <span class="flex items-center gap-2.5"><i class="bi bi-grid"></i> All Resources</span>
      <span class="text-xs font-bold <?= empty($filters['category']) ? 'text-gold' : 'text-slate-400' ?>"><?= (int) $totalCount ?></span>
    </a>
    <?php foreach (\App\Models\Resource::CAREER_CATEGORIES as $cat): $active = ($filters['category'] ?? '') === $cat; ?>
      <a href="<?= e(url('resources') . '?' . http_build_query(['tab' => 'career', 'category' => $cat]) . '#resource-list') ?>" class="flex items-center justify-between gap-2 px-3 py-2.5 rounded-lg text-sm font-semibold <?= $active ? 'bg-gold/10 text-gold' : 'text-slate-600 hover:bg-slate-50' ?>">
        <span class="flex items-center gap-2.5"><i class="bi <?= e(resource_category_icon($cat)) ?>"></i> <?= e($cat) ?></span>
        <span class="text-xs font-bold <?= $active ? 'text-gold' : 'text-slate-400' ?>"><?= (int) ($categoryCounts[$cat] ?? 0) ?></span>
      </a>
    <?php endforeach; ?>
  </aside>

  <div id="resource-list" class="scroll-mt-24">
    <div id="type-filter" class="flex items-center justify-end mb-3 scroll-mt-20">
      <form method="GET" action="<?= e(url('resources')) ?>" class="flex items-center gap-2">
        <input type="hidden" name="tab" value="career">
        <?php if (!empty($filters['q'])): ?><input type="hidden" name="q" value="<?= e($filters['q']) ?>"><?php endif; ?>
        <?php if (!empty($filters['category'])): ?><input type="hidden" name="category" value="<?= e($filters['category']) ?>"><?php endif; ?>
        <label class="text-xs text-slate-400">Filter by:</label>
        <select name="type" onchange="this.form.submit()" class="form-input !py-1.5 text-xs">
          <option value="">All Types</option>
          <?php foreach (\App\Models\Resource::TYPES as $type): ?>
            <option value="<?= e($type) ?>" <?= ($filters['type'] ?? '') === $type ? 'selected' : '' ?>><?= e($type) ?></option>
          <?php endforeach; ?>
        </select>
      </form>
    </div>

    <div class="space-y-3">
      <?php foreach ($resources as $res): ?>
        <?php require __DIR__ . '/../partials/resource_row.php'; ?>
      <?php endforeach; ?>
      <?php if (empty($resources)): ?>
        <p class="text-sm text-slate-400">No resources found matching those filters.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php if (!empty($featured) && count($featured) > 1): ?>
<script>
  (function () {
    var carousel = document.getElementById('featured-carousel');
    var slides = document.querySelectorAll('.featured-slide');
    var dots = document.querySelectorAll('.featured-dot');
    if (!carousel || !slides.length || !dots.length) return;

    var isDragging = false, dragMoved = false, startX = 0, startScrollLeft = 0;
    carousel.addEventListener('pointerdown', function (e) {
      if (e.pointerType === 'touch') return;
      isDragging = true;
      dragMoved = false;
      startX = e.clientX;
      startScrollLeft = carousel.scrollLeft;
      carousel.setPointerCapture(e.pointerId);
    });
    carousel.addEventListener('pointermove', function (e) {
      if (!isDragging) return;
      var dx = e.clientX - startX;
      if (Math.abs(dx) > 5) dragMoved = true;
      carousel.scrollLeft = startScrollLeft - dx;
    });
    ['pointerup', 'pointercancel', 'pointerleave'].forEach(function (evt) {
      carousel.addEventListener(evt, function () { isDragging = false; });
    });
    carousel.addEventListener('click', function (e) {
      if (dragMoved) {
        e.preventDefault();
        e.stopPropagation();
      }
    }, true);
    carousel.classList.add('cursor-grab', 'active:cursor-grabbing');

    dots.forEach(function (dot) {
      dot.addEventListener('click', function () {
        var target = document.querySelector('.featured-slide[data-index="' + dot.dataset.dot + '"]');
        if (target) target.scrollIntoView({ behavior: 'smooth', block: 'nearest', inline: 'center' });
      });
    });

    if (!('IntersectionObserver' in window)) return;

    var observer = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        var idx = entry.target.dataset.index;
        dots.forEach(function (d) {
          var active = d.dataset.dot === idx;
          d.classList.toggle('bg-gold', active);
          d.classList.toggle('w-4', active);
          d.classList.toggle('bg-slate-300', !active);
          d.classList.toggle('w-1.5', !active);
        });
      });
    }, { root: carousel, threshold: 0.6 });

    slides.forEach(function (s) { observer.observe(s); });
  })();
</script>
<?php endif; ?>
