<?php
/** @var array $post */
/** @var bool $linkToDetail */
$linkToDetail = $linkToDetail ?? true;
$isAdmin = \App\Core\Auth::isAdmin();
$canDelete = $post['viewer_is_author'] || $isAdmin;
?>
<div class="card p-5">
  <div class="flex items-start gap-3 mb-3">
    <a href="<?= e(url('alumni/' . $post['author_id'])) ?>" class="flex-shrink-0">
      <?= avatar_html(['name' => $post['author_name'], 'avatar' => $post['author_avatar']], 'w-11 h-11') ?>
    </a>
    <div class="min-w-0 flex-1">
      <div class="flex items-center gap-2 flex-wrap">
        <a href="<?= e(url('alumni/' . $post['author_id'])) ?>" class="text-sm font-bold text-primary-navy hover:text-gold"><?= e($post['author_name']) ?></a>
        <span class="badge-blue"><?= e(post_type_label($post['post_type'])) ?></span>
      </div>
      <?php if ($post['author_headline']): ?><p class="text-xs text-slate-500 truncate"><?= e($post['author_headline']) ?></p><?php endif; ?>
      <p class="text-[0.68rem] text-slate-400"><?= e(time_ago($post['created_at'])) ?></p>
    </div>
    <div class="flex items-center gap-3 flex-shrink-0">
      <?php if ($canDelete): ?>
        <div class="relative">
          <button type="button" class="kebab-btn text-slate-300 hover:text-primary-navy px-1" title="More options"><i class="bi bi-three-dots-vertical"></i></button>
          <div class="kebab-menu hidden absolute right-0 top-full mt-1 z-20 bg-white border border-slate-200 rounded-lg shadow-lg w-40 py-1">
            <a href="<?= e(url('feed/' . $post['id'])) ?>" class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 hover:bg-slate-50"><i class="bi bi-eye w-4"></i> View Post</a>
            <form method="POST" action="<?= e(url('feed/' . $post['id'] . '/delete')) ?>" data-confirm="Delete this post? This cannot be undone.">
              <?= csrf_field() ?>
              <button type="submit" class="w-full flex items-center gap-2 text-left px-3 py-2 text-xs text-red-600 hover:bg-red-50"><i class="bi bi-trash w-4"></i> Delete</button>
            </form>
          </div>
        </div>
      <?php endif; ?>
      <?php if (!$post['viewer_is_author']): ?>
        <form method="POST" action="<?= e(url(($post['viewer_is_following_author'] ? 'unfollow/' : 'follow/') . $post['author_id'])) ?>" class="follow-form" data-follow-url="<?= e(url('follow/' . $post['author_id'])) ?>" data-unfollow-url="<?= e(url('unfollow/' . $post['author_id'])) ?>" data-following="<?= $post['viewer_is_following_author'] ? '1' : '0' ?>">
          <?= csrf_field() ?>
          <button type="submit" class="follow-btn btn <?= $post['viewer_is_following_author'] ? 'bg-white border border-slate-200 !text-slate-500' : 'bg-white border border-gold !text-gold hover:bg-gold hover:!text-white' ?> !px-3 !py-1 text-[0.68rem] whitespace-nowrap"><?= $post['viewer_is_following_author'] ? 'Following' : '+ Follow' ?></button>
        </form>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($linkToDetail): ?>
    <a href="<?= e(url('feed/' . $post['id'])) ?>" class="block">
  <?php endif; ?>
    <p class="text-sm text-slate-700 whitespace-pre-line mb-3"><?= e($post['content']) ?></p>
    <?php if (!empty($post['image'])): ?>
      <img src="<?= e($post['image']) ?>" alt="" class="w-full max-h-96 object-cover rounded-lg mb-3">
    <?php endif; ?>
  <?php if ($linkToDetail): ?>
    </a>
  <?php endif; ?>

  <?php if (!empty($post['shared_post'])): $sp = $post['shared_post']; ?>
    <div class="border border-slate-200 rounded-lg p-4 mb-3 bg-slate-50">
      <div class="flex items-center gap-2 mb-2">
        <?= avatar_html(['name' => $sp['author_name'], 'avatar' => $sp['author_avatar']], 'w-7 h-7') ?>
        <a href="<?= e(url('alumni/' . $sp['author_id'])) ?>" class="text-xs font-bold text-primary-navy hover:text-gold"><?= e($sp['author_name']) ?></a>
        <span class="text-[0.65rem] text-slate-400"><?= e(time_ago($sp['created_at'])) ?></span>
      </div>
      <p class="text-xs text-slate-600 whitespace-pre-line"><?= e($sp['content']) ?></p>
      <?php if (!empty($sp['image'])): ?>
        <img src="<?= e($sp['image']) ?>" alt="" class="w-full max-h-64 object-cover rounded mt-2">
      <?php endif; ?>
    </div>
  <?php elseif (!empty($post['shared_post_id'])): ?>
    <div class="border border-slate-200 rounded-lg p-4 mb-3 bg-slate-50 text-xs text-slate-400">Original post is no longer available.</div>
  <?php endif; ?>

  <div class="flex items-center gap-5 pt-3 border-t border-slate-100 text-xs text-slate-500">
    <form method="POST" action="<?= e(url('feed/' . $post['id'] . '/like')) ?>" class="like-form" data-post-id="<?= (int) $post['id'] ?>">
      <?= csrf_field() ?>
      <button type="submit" class="like-btn flex items-center gap-1.5 <?= $post['viewer_has_liked'] ? 'text-gold font-semibold is-liked' : 'hover:text-gold' ?>">
        <i class="fa-solid fa-heart"></i> <span class="like-count"><?= (int) $post['like_count'] ?></span> <span class="like-label">Like<?= $post['like_count'] === 1 ? '' : 's' ?></span>
      </button>
    </form>
    <button type="button" class="comment-trigger flex items-center gap-1.5 hover:text-gold" data-post-id="<?= (int) $post['id'] ?>" data-author="<?= e($post['author_name']) ?>">
      <i class="fa-solid fa-comment"></i> <span class="comment-count" data-comment-count="<?= (int) $post['id'] ?>"><?= (int) $post['comment_count'] ?></span> <span class="comment-label">Comment<?= $post['comment_count'] === 1 ? '' : 's' ?></span>
    </button>
    <?php if (empty($post['shared_post_id'])): ?>
      <details class="relative share-toggle">
        <summary class="flex items-center gap-1.5 hover:text-gold cursor-pointer list-none"><i class="fa-solid fa-share"></i> Share</summary>
        <form method="POST" action="<?= e(url('feed/' . $post['id'] . '/share')) ?>" class="absolute left-0 top-full mt-1 z-20 bg-white border border-slate-200 rounded-lg shadow-lg p-3 w-64">
          <?= csrf_field() ?>
          <textarea name="caption" rows="2" class="form-textarea text-xs mb-2" placeholder="Add a caption (optional)"></textarea>
          <button type="submit" class="btn-gold !px-3 !py-1.5 text-xs w-full">Share to Feed</button>
        </form>
      </details>
    <?php endif; ?>
  </div>
</div>
