<div class="max-w-[1280px] mx-auto px-4 md:px-8 py-8">
  <div class="section-header">
    <h1 class="section-title text-base">Alumni Insights</h1>
    <?php if (\App\Core\Auth::check()): ?>
      <a href="<?= e(url('knowledge/submit')) ?>" class="section-link">Submit an Article &rarr;</a>
    <?php endif; ?>
  </div>

  <form method="GET" action="<?= e(url('knowledge')) ?>" class="sticky top-[var(--header-height)] z-30 bg-slate-50 py-3 mb-1">
    <?php if ($activeCategory !== null): ?>
      <input type="hidden" name="category" value="<?= e($activeCategory) ?>">
    <?php endif; ?>
    <div class="relative max-w-md">
      <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
      <input type="text" name="q" value="<?= e($search) ?>" placeholder="Search articles by title..." class="form-input !pl-10">
    </div>
  </form>

  <div class="flex flex-wrap gap-2 mb-6">
    <a href="<?= e(url('knowledge')) ?>" class="badge <?= $activeCategory === null ? 'bg-primary-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">All</a>
    <?php foreach (['article' => 'Thought Leadership', 'research' => 'Research', 'case_study' => 'Case Studies', 'white_paper' => 'White Papers'] as $catValue => $catLabel): ?>
      <a href="<?= e(url('knowledge?category=' . $catValue)) ?>" class="badge <?= $activeCategory === $catValue ? 'bg-primary-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>"><?= e($catLabel) ?></a>
    <?php endforeach; ?>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-5">
    <?php foreach ($articles as $article): ?>
      <a href="<?= e(url('knowledge/' . $article['id'])) ?>" class="card p-5 block">
        <span class="badge-gold mb-2"><?= e(article_category_label($article['category'])) ?></span>
        <h4 class="text-sm font-bold text-primary-navy leading-snug mb-2"><?= e($article['title']) ?></h4>
        <div class="flex items-center gap-2">
          <?= avatar_html(['name' => $article['author_name'], 'avatar' => $article['author_avatar']], 'w-6 h-6') ?>
          <span class="text-xs text-slate-500"><?= e($article['author_name']) ?></span>
          <span class="text-[0.65rem] text-slate-400 ml-auto"><?= e(format_date($article['published_at'])) ?></span>
        </div>
      </a>
    <?php endforeach; ?>
    <?php if (empty($articles)): ?>
      <p class="text-sm text-slate-400 col-span-full">No articles published yet.</p>
    <?php endif; ?>
  </div>
</div>
