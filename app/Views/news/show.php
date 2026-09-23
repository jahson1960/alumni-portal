<?php
$postUrl = url('news/' . $post['slug']);
$primaryCategory = $categories[0] ?? null;
?>
<div class="max-w-[1280px] mx-auto px-4 md:px-8 py-8">

  <div class="flex items-center gap-2 text-xs text-slate-400 mb-5 flex-wrap">
    <a href="<?= e(url('/')) ?>" class="hover:text-gold flex items-center gap-1.5"><i class="fa-solid fa-house"></i> Home</a>
    <i class="fa-solid fa-chevron-right text-[0.6rem]"></i>
    <span>Alumni Network</span>
    <i class="fa-solid fa-chevron-right text-[0.6rem]"></i>
    <a href="<?= e(url('news')) ?>" class="hover:text-gold">News &amp; Blog</a>
    <i class="fa-solid fa-chevron-right text-[0.6rem]"></i>
    <span class="text-primary-navy font-semibold truncate max-w-[220px] sm:max-w-none"><?= e($post['title']) ?></span>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6 items-start">

    <!-- Main -->
    <div>
      <div class="relative rounded-2xl overflow-hidden">
        <img src="<?= e($post['image']) ?>" alt="<?= e($post['title']) ?>" class="w-full h-56 md:h-80 object-cover">
        <?php if ($primaryCategory): ?>
          <span class="badge-gold absolute bottom-4 left-4"><?= e(strtoupper($primaryCategory['name'])) ?></span>
        <?php endif; ?>
      </div>

      <h1 class="text-2xl md:text-3xl font-extrabold text-primary-navy leading-snug mt-6 mb-3"><?= e($post['title']) ?></h1>

      <div class="flex flex-wrap items-center gap-x-3 gap-y-1.5 text-xs text-slate-500 mb-6">
        <span class="flex items-center gap-1.5"><i class="fa-regular fa-calendar"></i> <?= e(format_date($post['published_at'])) ?></span>
        <span class="text-slate-300">|</span>
        <span class="flex items-center gap-1.5"><i class="fa-regular fa-user"></i> By <?= e($post['author']) ?></span>
        <?php if ($primaryCategory): $pc = palette_classes($primaryCategory['slug']); ?>
          <span class="text-slate-300">|</span>
          <span class="badge <?= e($pc['bg']) ?> <?= e($pc['text']) ?>"><?= e($primaryCategory['name']) ?></span>
        <?php endif; ?>
      </div>

      <div class="prose prose-sm md:prose-base max-w-none prose-headings:text-primary-navy prose-a:text-gold prose-blockquote:not-italic prose-blockquote:border-l-4 prose-blockquote:border-gold prose-blockquote:bg-slate-50 prose-blockquote:rounded-r-lg prose-blockquote:py-3 prose-blockquote:px-5 prose-blockquote:text-primary-navy prose-blockquote:font-medium">
        <?= $post['body'] ?>
      </div>

      <?php if ($tags): ?>
        <div class="flex flex-wrap gap-2 mt-8 pt-6 border-t border-slate-100">
          <?php foreach ($tags as $tag): ?>
            <span class="text-xs font-semibold text-sky-700 bg-sky-50 rounded-full px-3 py-1.5"><?= e($tag) ?></span>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <aside class="space-y-6">
      <div class="card p-5">
        <h3 class="text-sm font-extrabold text-primary-navy mb-4 flex items-center gap-2"><i class="fa-regular fa-file-lines text-gold"></i> Quick Information</h3>
        <div class="space-y-4">
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center flex-shrink-0"><i class="fa-regular fa-calendar"></i></div>
            <div>
              <p class="text-xs text-slate-400">Date Published</p>
              <p class="text-sm font-semibold text-primary-navy"><?= e(format_date($post['published_at'])) ?></p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center flex-shrink-0"><i class="fa-regular fa-user"></i></div>
            <div>
              <p class="text-xs text-slate-400">Author</p>
              <p class="text-sm font-semibold text-primary-navy"><?= e($post['author']) ?></p>
            </div>
          </div>
          <?php if ($primaryCategory): ?>
            <div class="flex items-start gap-3">
              <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center flex-shrink-0"><i class="fa-regular fa-bookmark"></i></div>
              <div>
                <p class="text-xs text-slate-400">Category</p>
                <p class="text-sm font-semibold text-primary-navy"><?= e($primaryCategory['name']) ?></p>
              </div>
            </div>
          <?php endif; ?>
          <?php if (!empty($post['region'])): ?>
            <div class="flex items-start gap-3">
              <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-earth-africa"></i></div>
              <div>
                <p class="text-xs text-slate-400">Region</p>
                <p class="text-sm font-semibold text-primary-navy"><?= e($post['region']) ?></p>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </div>

      <div class="card p-5">
        <h3 class="text-sm font-extrabold text-primary-navy mb-4 flex items-center gap-2"><i class="fa-solid fa-share-nodes text-gold"></i> Share This Article</h3>
        <div class="flex items-center gap-2.5">
          <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($postUrl) ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-[#0a66c2] text-white flex items-center justify-center hover:opacity-85" title="Share on LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
          <a href="https://twitter.com/intent/tweet?url=<?= urlencode($postUrl) ?>&text=<?= urlencode($post['title']) ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-black text-white flex items-center justify-center hover:opacity-85" title="Share on X"><i class="fa-brands fa-x-twitter"></i></a>
          <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($postUrl) ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-[#1877f2] text-white flex items-center justify-center hover:opacity-85" title="Share on Facebook"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="https://wa.me/?text=<?= urlencode($post['title'] . ' ' . $postUrl) ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-[#25d366] text-white flex items-center justify-center hover:opacity-85" title="Share on WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
          <button type="button" id="copy-news-link" data-url="<?= e($postUrl) ?>" class="w-9 h-9 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-slate-200" title="Copy link"><i class="fa-solid fa-link"></i></button>
        </div>
        <p id="copy-news-link-msg" class="hidden text-xs text-emerald-600 mt-2">Link copied!</p>
      </div>

      <?php if ($related): ?>
        <div class="card p-5">
          <h3 class="text-sm font-extrabold text-primary-navy mb-4 flex items-center gap-2"><i class="fa-regular fa-newspaper text-gold"></i> Related News</h3>
          <div class="space-y-3">
            <?php foreach ($related as $rp): ?>
              <a href="<?= e(url('news/' . $rp['slug'])) ?>" class="flex items-center gap-3 group">
                <img src="<?= e($rp['image']) ?>" alt="" class="w-14 h-14 rounded-lg object-cover flex-shrink-0">
                <div class="min-w-0 flex-1">
                  <p class="text-xs font-bold text-primary-navy leading-snug line-clamp-2 group-hover:text-gold transition-colors"><?= e($rp['title']) ?></p>
                  <p class="text-[0.65rem] text-slate-400 mt-1 flex items-center gap-1"><i class="fa-regular fa-calendar"></i> <?= e(format_date($rp['published_at'])) ?></p>
                </div>
                <i class="fa-solid fa-chevron-right text-slate-300 text-xs flex-shrink-0"></i>
              </a>
            <?php endforeach; ?>
          </div>
        </div>
      <?php endif; ?>

      <a href="<?= e(url('news')) ?>" class="btn bg-white border border-gold !text-gold hover:bg-gold hover:!text-white !py-3 text-sm w-full text-center flex items-center justify-center gap-2"><i class="fa-solid fa-arrow-left"></i> Back to News &amp; Blog</a>
    </aside>
  </div>
</div>

<script>
  (function () {
    var copyBtn = document.getElementById('copy-news-link');
    var copyMsg = document.getElementById('copy-news-link-msg');
    if (!copyBtn) return;
    copyBtn.addEventListener('click', function () {
      var url = copyBtn.dataset.url;
      var done = function () {
        if (!copyMsg) return;
        copyMsg.classList.remove('hidden');
        setTimeout(function () { copyMsg.classList.add('hidden'); }, 2000);
      };
      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(url).then(done).catch(function () {});
      } else {
        var input = document.createElement('input');
        input.value = url;
        document.body.appendChild(input);
        input.select();
        document.execCommand('copy');
        document.body.removeChild(input);
        done();
      }
    });
  })();
</script>
