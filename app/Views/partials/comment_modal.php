<div id="comment-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/50 p-4">
  <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[85vh] flex flex-col overflow-hidden" id="comment-modal-panel">
    <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 flex-shrink-0">
      <span class="w-6"></span>
      <h3 id="comment-modal-title" class="text-sm font-bold text-primary-navy text-center flex-1 truncate px-2">Post</h3>
      <button type="button" id="comment-modal-close" class="text-slate-400 hover:text-slate-600 w-6 text-right"><i class="fa-solid fa-xmark text-lg"></i></button>
    </div>
    <div id="comment-modal-body" class="flex-1 overflow-y-auto p-5"></div>
    <form id="comment-modal-form" method="POST" action="" class="flex items-center gap-3 px-5 py-3.5 border-t border-slate-100 flex-shrink-0">
      <?= csrf_field() ?>
      <?php $modalViewer = \App\Core\Auth::user(); ?>
      <?php if ($modalViewer): ?><?= avatar_html($modalViewer, 'w-8 h-8 flex-shrink-0') ?><?php endif; ?>
      <input type="text" name="content" id="comment-modal-input" class="form-input flex-1 !rounded-full" placeholder="Write a comment..." autocomplete="off">
      <button type="submit" class="text-gold hover:text-gold/80 flex-shrink-0" title="Post comment"><i class="fa-solid fa-paper-plane text-lg"></i></button>
    </form>
  </div>
</div>
