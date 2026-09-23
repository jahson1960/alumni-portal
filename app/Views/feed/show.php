<div class="max-w-2xl mx-auto px-4 md:px-8 py-8">
  <a href="<?= e(url('feed')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to Feed</a>

  <?php $linkToDetail = false; require dirname(__DIR__) . '/partials/post_card.php'; ?>

  <div id="comments" class="mt-6">
    <h3 class="section-title text-xs mb-3">Comments (<?= count($comments) ?>)</h3>

    <form method="POST" action="<?= e(url('feed/' . $post['id'] . '/comment')) ?>" class="card p-4 mb-4 flex gap-3">
      <?= csrf_field() ?>
      <input type="text" name="content" class="form-input flex-1" placeholder="Write a comment..." required>
      <button type="submit" class="btn-gold !px-4 !py-2 text-xs">Post</button>
    </form>

    <div class="space-y-3">
      <?php foreach ($comments as $comment): ?>
        <div class="card p-4 flex items-start gap-3">
          <?= avatar_html(['name' => $comment['author_name'], 'avatar' => $comment['author_avatar']], 'w-8 h-8') ?>
          <div class="min-w-0 flex-1">
            <div class="flex items-center gap-2">
              <span class="text-xs font-bold text-primary-navy"><?= e($comment['author_name']) ?></span>
              <span class="text-[0.65rem] text-slate-400"><?= e(time_ago($comment['created_at'])) ?></span>
            </div>
            <p class="text-sm text-slate-600 whitespace-pre-line"><?= e($comment['content']) ?></p>
          </div>
          <?php if ((int) $comment['user_id'] === (int) \App\Core\Auth::id() || \App\Core\Auth::isAdmin()): ?>
            <form method="POST" action="<?= e(url('feed/comments/' . $comment['id'] . '/delete')) ?>" class="flex-shrink-0" data-confirm="Delete this comment?">
              <?= csrf_field() ?>
              <button type="submit" class="text-[0.68rem] text-red-500 hover:underline">Delete</button>
            </form>
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
      <?php if (empty($comments)): ?>
        <p class="text-sm text-slate-400">No comments yet. Be the first to reply.</p>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
  (function () {
    document.addEventListener('click', function (e) {
      var btn = e.target.closest('.kebab-btn');
      if (btn) {
        e.stopPropagation();
        var menu = btn.nextElementSibling;
        document.querySelectorAll('.kebab-menu').forEach(function (m) { if (m !== menu) m.classList.add('hidden'); });
        menu.classList.toggle('hidden');
        return;
      }
      document.querySelectorAll('.kebab-menu').forEach(function (m) { m.classList.add('hidden'); });
    });

    // Like button — AJAX, so liking a post never navigates/scrolls the page.
    document.addEventListener('submit', function (e) {
      var form = e.target.closest('.like-form');
      if (!form) return;
      e.preventDefault();
      fetch(form.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: new FormData(form)
      })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          var btn = form.querySelector('.like-btn');
          btn.classList.toggle('text-gold', data.liked);
          btn.classList.toggle('font-semibold', data.liked);
          btn.classList.toggle('hover:text-gold', !data.liked);
          btn.querySelector('.like-count').textContent = data.count;
          btn.querySelector('.like-label').textContent = 'Like' + (data.count === 1 ? '' : 's');
        })
        .catch(function () {});
    });

    // Follow/unfollow button — AJAX, so it never navigates/scrolls the page.
    document.addEventListener('submit', function (e) {
      var form = e.target.closest('.follow-form');
      if (!form) return;
      e.preventDefault();
      var willFollow = form.dataset.following !== '1';
      fetch(form.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: new FormData(form)
      })
        .then(function (r) { return r.json(); })
        .then(function () {
          form.dataset.following = willFollow ? '1' : '0';
          form.action = willFollow ? form.dataset.unfollowUrl : form.dataset.followUrl;
          var btn = form.querySelector('.follow-btn');
          btn.textContent = willFollow ? 'Following' : '+ Follow';
          btn.classList.toggle('border-slate-200', willFollow);
          btn.classList.toggle('!text-slate-500', willFollow);
          btn.classList.toggle('border-gold', !willFollow);
          btn.classList.toggle('!text-gold', !willFollow);
          btn.classList.toggle('hover:bg-gold', !willFollow);
          btn.classList.toggle('hover:!text-white', !willFollow);
        })
        .catch(function () {});
    });

    // Share popup (<details>) — close it when clicking anywhere outside.
    document.addEventListener('click', function (e) {
      document.querySelectorAll('.share-toggle[open]').forEach(function (d) {
        if (!d.contains(e.target)) d.removeAttribute('open');
      });
    });

    // We're already on the dedicated post page — the comment button just jumps to the
    // comments section below (already fully visible) instead of opening the feed's modal.
    var commentTrigger = document.querySelector('.comment-trigger');
    if (commentTrigger) {
      commentTrigger.addEventListener('click', function () {
        var section = document.getElementById('comments');
        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        var input = section.querySelector('input[name="content"]');
        if (input) input.focus();
      });
    }
  })();
</script>
