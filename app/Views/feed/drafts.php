<div class="max-w-[820px] mx-auto px-4 md:px-8 py-8">

  <div class="flex items-center justify-between gap-4 mb-6">
    <div>
      <a href="<?= e(url('feed')) ?>" class="text-xs font-semibold text-slate-500 hover:text-gold flex items-center gap-1 mb-2"><i class="bi bi-arrow-left"></i> Back to Feed</a>
      <h1 class="text-2xl font-extrabold text-primary-navy">My Drafts</h1>
      <p class="text-sm text-slate-500 mt-1">Posts you've started but haven't shared yet.</p>
    </div>
  </div>

  <div class="space-y-4">
    <?php foreach ($drafts as $draft): ?>
      <div class="card p-5">
        <form method="POST" action="<?= e(url('feed/drafts/' . $draft['id'])) ?>">
          <?= csrf_field() ?>
          <div class="flex flex-wrap items-center justify-between gap-2 mb-3">
            <span class="text-[0.68rem] text-slate-400">Last updated <?= e(time_ago($draft['updated_at'])) ?></span>
            <div class="relative">
              <i class="bi bi-globe absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs pointer-events-none"></i>
              <select name="visibility" class="form-input !pl-8 !py-1.5 text-xs">
                <option value="public" <?= $draft['visibility'] === 'public' ? 'selected' : '' ?>>Public</option>
                <option value="connections" <?= $draft['visibility'] === 'connections' ? 'selected' : '' ?>>Connections Only</option>
              </select>
            </div>
          </div>

          <textarea name="content" rows="3" maxlength="2000" class="form-textarea text-sm"><?= e($draft['content']) ?></textarea>

          <?php if ($draft['image']): ?>
            <img src="<?= e($draft['image']) ?>" alt="" class="mt-3 rounded-lg max-h-40 object-cover">
          <?php endif; ?>

          <input type="hidden" name="post_type" class="draft-type-input" value="<?= e($draft['post_type']) ?>">
          <div class="flex flex-wrap items-center gap-1.5 mt-3">
            <?php foreach ($composerTypes as $key => $ct): ?>
              <button type="button" class="draft-type-btn flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold <?= $draft['post_type'] === $key ? 'bg-gold/10 text-gold' : 'text-slate-500 hover:bg-slate-50' ?>" data-type="<?= e($key) ?>">
                <i class="bi <?= e($ct['icon']) ?>"></i> <?= e($ct['label']) ?>
              </button>
            <?php endforeach; ?>
          </div>

          <div class="flex items-center justify-end gap-3 mt-4 pt-3 border-t border-slate-100">
            <button type="submit" name="action" value="save" class="btn bg-white border border-slate-200 !text-slate-600 hover:bg-slate-50 !px-4 !py-2 text-xs">Update Draft</button>
            <button type="submit" name="action" value="publish" class="btn-gold !px-4 !py-2 text-xs flex items-center gap-2"><i class="bi bi-send"></i> Publish Now</button>
          </div>
        </form>
        <form method="POST" action="<?= e(url('feed/drafts/' . $draft['id'] . '/delete')) ?>" data-confirm="Discard this draft? This can't be undone." class="mt-2 text-right">
          <?= csrf_field() ?>
          <button type="submit" class="text-xs text-slate-400 hover:text-red-500">Discard draft</button>
        </form>
      </div>
    <?php endforeach; ?>
    <?php if (empty($drafts)): ?>
      <p class="text-sm text-slate-400">You don't have any drafts. Anything you save from the composer without posting will show up here.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  (function () {
    document.querySelectorAll('.card').forEach(function (card) {
      var buttons = card.querySelectorAll('.draft-type-btn');
      var input = card.querySelector('.draft-type-input');
      if (!buttons.length || !input) {
        return;
      }
      buttons.forEach(function (btn) {
        btn.addEventListener('click', function () {
          input.value = btn.dataset.type;
          buttons.forEach(function (b) {
            var active = b === btn;
            b.classList.toggle('bg-gold/10', active);
            b.classList.toggle('text-gold', active);
            b.classList.toggle('text-slate-500', !active);
          });
        });
      });
    });
  })();
</script>
