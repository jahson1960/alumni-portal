<?php
$authUser = \App\Core\Auth::user();
$qs = function (array $overrides) use ($activeCategory, $search, $sort) {
    $params = array_merge(['category' => $activeCategory, 'q' => $search, 'sort' => $sort !== 'recent' ? $sort : null], $overrides);
    $params = array_filter($params, fn ($v) => $v !== null && $v !== '');
    return '?' . http_build_query($params);
};
$categoryIcons = [
    'alumni-stories' => 'bi-mortarboard',
    'campus-news' => 'bi-building',
    'career-tips' => 'bi-briefcase',
    'industry-insights' => 'bi-graph-up-arrow',
];
$activeCategoryName = null;
foreach ($allCategories as $cat) {
    if ($cat['slug'] === $activeCategory) {
        $activeCategoryName = $cat['name'];
    }
}
$gridPosts = $featuredPost ? array_filter($posts, fn ($p) => (int) $p['id'] !== (int) $featuredPost['id']) : $posts;
?>
<div class="max-w-[1280px] mx-auto px-4 md:px-8 py-8">

  <div class="flex flex-wrap items-start justify-between gap-4 mb-2">
    <div>
      <p class="text-xs font-bold text-gold uppercase tracking-wide mb-1">Alumni Network</p>
      <h1 class="text-2xl md:text-3xl font-extrabold text-primary-navy">News &amp; Updates</h1>
      <p class="text-sm text-slate-500 mt-1.5 max-w-xl">Stay informed with the latest news, stories and achievements from our global alumni community.</p>
    </div>
    <div class="hidden sm:flex items-center gap-2 text-xs text-slate-400 pt-1">
      <a href="<?= e(url('/')) ?>" class="hover:text-gold flex items-center gap-1.5"><i class="fa-solid fa-house"></i> Home</a>
      <i class="fa-solid fa-chevron-right text-[0.6rem]"></i>
      <span>Alumni Network</span>
      <i class="fa-solid fa-chevron-right text-[0.6rem]"></i>
      <span class="text-primary-navy font-semibold">News &amp; Blog</span>
    </div>
  </div>

  <?php if ($featuredPost): ?>
    <?php $featCats = \App\Models\Category::forNews((int) $featuredPost['id']); ?>
    <a href="<?= e(url('news/' . $featuredPost['slug'])) ?>" class="relative block rounded-2xl overflow-hidden shadow-sm mt-6 mb-10 group">
      <div class="grid grid-cols-1 md:grid-cols-2">
        <div class="relative h-56 md:h-auto">
          <img src="<?= e($featuredPost['image']) ?>" alt="" class="absolute inset-0 w-full h-full object-cover">
          <span class="badge-gold absolute top-4 left-4">Featured</span>
        </div>
        <div class="bg-primary-navy text-white p-6 md:p-8 flex flex-col justify-center relative overflow-hidden">
          <div class="absolute -right-10 -top-10 w-40 h-40 rounded-full bg-white/5"></div>
          <?php if ($featCats): $pc = palette_classes($featCats[0]['slug']); ?>
            <span class="badge <?= e($pc['bg']) ?> <?= e($pc['text']) ?> w-fit mb-3"><?= e(strtoupper($featCats[0]['name'])) ?></span>
          <?php endif; ?>
          <h2 class="text-xl md:text-2xl font-extrabold leading-snug mb-3 group-hover:text-gold transition-colors"><?= e($featuredPost['title']) ?></h2>
          <p class="text-sm text-slate-300 leading-relaxed mb-5 line-clamp-3"><?= e($featuredPost['excerpt']) ?></p>
          <div class="flex items-center justify-between">
            <div class="flex items-center gap-4 text-xs text-slate-400">
              <span class="flex items-center gap-1.5"><i class="fa-regular fa-calendar"></i> <?= e(format_date($featuredPost['published_at'])) ?></span>
              <span class="flex items-center gap-1.5"><i class="fa-regular fa-user"></i> By <?= e($featuredPost['author']) ?></span>
            </div>
            <span class="w-10 h-10 rounded-full bg-gold text-primary-navy flex items-center justify-center flex-shrink-0 group-hover:bg-white transition-colors"><i class="fa-solid fa-arrow-right"></i></span>
          </div>
        </div>
      </div>
    </a>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6 items-start">

    <!-- Main -->
    <div>
      <div class="flex flex-wrap items-center justify-between gap-3 mb-5">
        <h3 class="text-lg font-extrabold text-primary-navy"><?= e($activeCategoryName ?? 'Latest News') ?></h3>
        <div class="flex flex-wrap items-center gap-2">
          <form method="GET" action="<?= e(url('news')) ?>" class="flex items-center">
            <?php if ($activeCategory !== null): ?><input type="hidden" name="category" value="<?= e($activeCategory) ?>"><?php endif; ?>
            <?php if ($sort !== 'recent'): ?><input type="hidden" name="sort" value="<?= e($sort) ?>"><?php endif; ?>
            <div class="relative">
              <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
              <input type="text" name="q" value="<?= e($search) ?>" placeholder="Search news &amp; blog..." class="form-input !pl-9 text-sm !py-2 w-44 sm:w-56">
            </div>
          </form>
          <form method="GET" action="<?= e(url('news')) ?>" id="sort-form" class="flex items-center">
            <?php if ($activeCategory !== null): ?><input type="hidden" name="category" value="<?= e($activeCategory) ?>"><?php endif; ?>
            <?php if ($search !== ''): ?><input type="hidden" name="q" value="<?= e($search) ?>"><?php endif; ?>
            <select name="sort" onchange="document.getElementById('sort-form').submit()" class="form-input !py-2 text-xs">
              <option value="recent" <?= $sort === 'recent' ? 'selected' : '' ?>>Most Recent</option>
              <option value="oldest" <?= $sort === 'oldest' ? 'selected' : '' ?>>Oldest First</option>
            </select>
          </form>
          <?php if ($activeCategory !== null || $search !== ''): ?>
            <a href="<?= e(url('news')) ?>" class="text-xs font-semibold text-gold hover:underline whitespace-nowrap">View All News &rarr;</a>
          <?php endif; ?>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
        <?php foreach ($gridPosts as $post): $isSaved = in_array((int) $post['id'], $savedIds, true); ?>
          <div class="card overflow-hidden relative">
            <a href="<?= e(url('news/' . $post['slug'])) ?>" class="absolute inset-0 z-0" aria-label="<?= e($post['title']) ?>"></a>
            <div class="pointer-events-none">
              <div class="relative">
                <img src="<?= e($post['image']) ?>" alt="<?= e($post['title']) ?>" class="w-full h-40 object-cover">
                <?php $postCats = \App\Models\Category::forNews((int) $post['id']); ?>
                <?php if ($postCats): $pc = palette_classes($postCats[0]['slug']); ?>
                  <span class="badge <?= e($pc['bg']) ?> <?= e($pc['text']) ?> absolute top-3 left-3"><?= e(strtoupper($postCats[0]['name'])) ?></span>
                <?php endif; ?>
              </div>
              <div class="p-4">
                <h4 class="text-sm font-bold leading-snug mb-1.5 text-primary-navy"><?= e($post['title']) ?></h4>
                <div class="text-[0.68rem] text-slate-400 mb-2 flex items-center gap-1.5"><i class="fa-regular fa-calendar"></i> <?= e(format_date($post['published_at'])) ?></div>
                <p class="text-xs text-slate-500 line-clamp-2 mb-3"><?= e($post['excerpt']) ?></p>
                <div class="flex items-center justify-between pointer-events-auto relative z-10">
                  <span class="text-xs font-semibold text-gold">Read More <i class="fa-solid fa-arrow-right"></i></span>
                  <?php if ($authUser): ?>
                    <form method="POST" action="<?= e(url('news/' . $post['id'] . '/save')) ?>">
                      <?= csrf_field() ?>
                      <button type="submit" class="text-sm <?= $isSaved ? 'text-gold' : 'text-slate-300 hover:text-gold' ?>" title="<?= $isSaved ? 'Unsave' : 'Save article' ?>"><i class="bi <?= $isSaved ? 'bi-bookmark-fill' : 'bi-bookmark' ?>"></i></button>
                    </form>
                  <?php endif; ?>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
        <?php if (empty($gridPosts)): ?>
          <p class="text-sm text-slate-400 col-span-full">No articles found matching those filters.</p>
        <?php endif; ?>
      </div>

      <?php if ($totalPages > 1): ?>
        <div class="flex items-center justify-center gap-1.5 mt-8">
          <a href="<?= e(url('news') . $qs(['page' => max(1, $page - 1)])) ?>" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:text-primary-navy <?= $page <= 1 ? 'pointer-events-none opacity-40' : '' ?>"><i class="fa-solid fa-arrow-left text-xs"></i></a>
          <?php for ($p = 1; $p <= $totalPages; $p++): ?>
            <a href="<?= e(url('news') . $qs(['page' => $p])) ?>" class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-semibold <?= $p === $page ? 'bg-gold text-white' : 'border border-slate-200 text-slate-600 hover:bg-slate-50' ?>"><?= $p ?></a>
          <?php endfor; ?>
          <a href="<?= e(url('news') . $qs(['page' => min($totalPages, $page + 1)])) ?>" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:text-primary-navy <?= $page >= $totalPages ? 'pointer-events-none opacity-40' : '' ?>"><i class="fa-solid fa-arrow-right text-xs"></i></a>
        </div>
      <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <aside class="space-y-6">
      <div class="card p-5">
        <h3 class="text-sm font-extrabold text-primary-navy mb-3">News Categories</h3>
        <div class="space-y-1.5">
          <a href="<?= e(url('news') . $qs(['category' => null, 'page' => null])) ?>" class="flex items-center justify-between gap-2 px-3 py-2.5 rounded-lg text-sm font-semibold <?= $activeCategory === null ? 'bg-gold/10 text-gold' : 'text-slate-600 hover:bg-slate-50' ?>">
            <span class="flex items-center gap-2.5"><i class="bi bi-megaphone"></i> All News</span>
            <span class="text-xs font-bold px-2 py-0.5 rounded-full <?= $activeCategory === null ? 'bg-gold text-white' : 'bg-slate-100 text-slate-500' ?>"><?= number_format($totalCount) ?></span>
          </a>
          <?php foreach ($allCategories as $cat): ?>
            <a href="<?= e(url('news') . $qs(['category' => $cat['slug'], 'page' => null])) ?>" class="flex items-center justify-between gap-2 px-3 py-2.5 rounded-lg text-sm font-semibold <?= $activeCategory === $cat['slug'] ? 'bg-gold/10 text-gold' : 'text-slate-600 hover:bg-slate-50' ?>">
              <span class="flex items-center gap-2.5"><i class="bi <?= e($categoryIcons[$cat['slug']] ?? 'bi-bookmark') ?>"></i> <?= e($cat['name']) ?></span>
              <span class="text-xs font-bold px-2 py-0.5 rounded-full <?= $activeCategory === $cat['slug'] ? 'bg-gold text-white' : 'bg-slate-100 text-slate-500' ?>"><?= number_format($categoryCounts[$cat['slug']] ?? 0) ?></span>
            </a>
          <?php endforeach; ?>
        </div>
      </div>

      <?php if ($recentWidget): ?>
        <div class="card p-5">
          <h3 class="text-sm font-extrabold text-primary-navy mb-3">Most Recent News</h3>
          <div class="space-y-3">
            <?php foreach ($recentWidget as $rp): ?>
              <a href="<?= e(url('news/' . $rp['slug'])) ?>" class="flex items-center gap-3 group">
                <img src="<?= e($rp['image']) ?>" alt="" class="w-14 h-14 rounded-lg object-cover flex-shrink-0">
                <div class="min-w-0">
                  <p class="text-xs font-bold text-primary-navy leading-snug line-clamp-2 group-hover:text-gold transition-colors"><?= e($rp['title']) ?></p>
                  <p class="text-[0.65rem] text-slate-400 mt-1 flex items-center gap-1"><i class="fa-regular fa-calendar"></i> <?= e(format_date($rp['published_at'])) ?></p>
                </div>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <div class="card p-4">
        <div class="w-9 h-9 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center mb-2.5"><i class="bi bi-pencil"></i></div>
        <h3 class="text-sm font-bold text-primary-navy mb-1">Have a story to share?</h3>
        <p class="text-xs text-slate-500 mb-3">Contribute to the community.</p>
        <a href="mailto:<?= e($submitEmail) ?>?subject=<?= rawurlencode('Story Submission') ?>" class="btn bg-white border border-gold !text-gold hover:bg-gold hover:!text-white !px-4 !py-2 text-xs w-full text-center">Submit a Story</a>
      </div>
    </aside>
  </div>
</div>
