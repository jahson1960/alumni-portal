<div class="max-w-3xl mx-auto px-4 md:px-8 py-8">
  <a href="<?= e(url('knowledge')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to Insights</a>

  <article class="card p-6 md:p-8">
    <span class="badge-gold mb-3"><?= e(article_category_label($article['category'])) ?></span>
    <h1 class="text-xl md:text-2xl font-extrabold text-primary-navy leading-snug mb-3"><?= e($article['title']) ?></h1>
    <div class="flex items-center gap-2 mb-6">
      <a href="<?= e(url('alumni/' . $article['user_id'])) ?>"><?= avatar_html(['name' => $article['author_name'], 'avatar' => $article['author_avatar']], 'w-8 h-8') ?></a>
      <a href="<?= e(url('alumni/' . $article['user_id'])) ?>" class="text-xs font-semibold text-primary-navy hover:text-gold"><?= e($article['author_name']) ?></a>
      <span class="text-[0.68rem] text-slate-400 ml-2"><?= e(format_date($article['published_at'])) ?> &bull; <?= reading_time_minutes($article['body']) ?> min read</span>
    </div>
    <div class="prose prose-sm md:prose-base max-w-none prose-headings:text-primary-navy prose-a:text-gold"><?= $article['body'] ?></div>
  </article>
</div>
