<div class="max-w-[1280px] mx-auto px-4 md:px-8 py-8">
  <a href="<?= e(url('knowledge')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to Insights</a>
  <div class="section-header">
    <h1 class="section-title text-base">Submit an Article</h1>
  </div>

  <?php require dirname(__DIR__) . '/partials/errors.php'; ?>

  <form method="POST" action="<?= e(url('knowledge/submit')) ?>" class="card p-6 max-w-2xl space-y-4 mb-10">
    <?= csrf_field() ?>
    <div>
      <label class="form-label" for="title">Title</label>
      <input type="text" id="title" name="title" class="form-input" required>
    </div>
    <div>
      <label class="form-label" for="category">Category</label>
      <select id="category" name="category" class="form-input max-w-xs">
        <?php foreach ($categories as $cat): ?>
          <option value="<?= e($cat) ?>"><?= e(article_category_label($cat)) ?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <div>
      <label class="form-label">Body</label>
      <div data-rich-editor="body"></div>
      <textarea id="body" name="body" class="hidden"></textarea>
    </div>
    <p class="text-xs text-slate-400">Submissions are reviewed by an admin before appearing publicly.</p>
    <button type="submit" class="btn-gold">Submit for Review</button>
  </form>

  <h3 class="section-title text-xs mb-3">My Submissions</h3>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ($myArticles as $article): ?>
      <div class="card p-4">
        <h4 class="text-sm font-bold text-primary-navy mb-1"><?= e($article['title']) ?></h4>
        <span class="badge <?= $article['status'] === 'published' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' ?>"><?= e(ucfirst($article['status'])) ?></span>
      </div>
    <?php endforeach; ?>
    <?php if (empty($myArticles)): ?>
      <p class="text-sm text-slate-400 col-span-full">You haven't submitted anything yet.</p>
    <?php endif; ?>
  </div>
</div>
