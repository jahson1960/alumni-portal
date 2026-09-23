<div class="mb-4 pb-4 border-b border-slate-100">
  <div class="flex items-center gap-2.5 mb-2">
    <?= avatar_html(['name' => $post['author_name'], 'avatar' => $post['author_avatar']], 'w-9 h-9') ?>
    <div class="min-w-0">
      <p class="text-sm font-bold text-primary-navy truncate"><?= e($post['author_name']) ?></p>
      <p class="text-[0.68rem] text-slate-400"><?= e(time_ago($post['created_at'])) ?></p>
    </div>
  </div>
  <p class="text-sm text-slate-700 whitespace-pre-line"><?= e($post['content']) ?></p>
  <?php if (!empty($post['image'])): ?>
    <img src="<?= e($post['image']) ?>" alt="" class="w-full max-h-72 object-cover rounded-lg mt-2">
  <?php endif; ?>
  <div class="flex items-center gap-4 text-xs text-slate-500 mt-3">
    <span><i class="fa-solid fa-heart <?= $post['viewer_has_liked'] ? 'text-gold' : '' ?>"></i> <?= (int) $post['like_count'] ?></span>
    <span><i class="fa-solid fa-comment"></i> <span data-modal-comment-count><?= (int) $post['comment_count'] ?></span></span>
  </div>
</div>

<div class="space-y-3">
  <?php foreach ($comments as $comment): ?>
    <div class="flex items-start gap-2.5">
      <?= avatar_html(['name' => $comment['author_name'], 'avatar' => $comment['author_avatar']], 'w-8 h-8') ?>
      <div class="min-w-0 flex-1">
        <div class="bg-slate-100 rounded-2xl px-3 py-2 inline-block max-w-full">
          <p class="text-xs font-bold text-primary-navy"><?= e($comment['author_name']) ?></p>
          <p class="text-sm text-slate-700 whitespace-pre-line"><?= e($comment['content']) ?></p>
        </div>
        <div class="flex items-center gap-3 mt-1 px-1">
          <span class="text-[0.65rem] text-slate-400"><?= e(time_ago($comment['created_at'])) ?></span>
          <?php if ((int) $comment['user_id'] === (int) \App\Core\Auth::id() || \App\Core\Auth::isAdmin()): ?>
            <form method="POST" action="<?= e(url('feed/comments/' . $comment['id'] . '/delete')) ?>" class="comment-delete-form" data-post-id="<?= (int) $post['id'] ?>">
              <?= csrf_field() ?>
              <button type="submit" class="text-[0.65rem] text-red-500 hover:underline">Delete</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if (empty($comments)): ?>
    <p class="text-sm text-slate-400 text-center py-6">No comments yet. Be the first to reply.</p>
  <?php endif; ?>
</div>
