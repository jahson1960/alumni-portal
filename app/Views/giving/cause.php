<div class="max-w-3xl mx-auto px-4 md:px-8 py-8">
  <a href="<?= e(url('give')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to Give Back</a>

  <div class="card p-6 md:p-8">
    <div class="flex items-center gap-4 mb-4">
      <div class="w-14 h-14 rounded-lg bg-[var(--menu-accent-50)] text-[var(--menu-accent-600)] flex items-center justify-center text-2xl flex-shrink-0"><i class="<?= e($cause['icon']) ?>"></i></div>
      <div>
        <h1 class="text-xl font-extrabold text-primary-navy"><?= e($cause['title']) ?></h1>
        <?php if ($cause['description']): ?><p class="text-sm text-slate-500"><?= e($cause['description']) ?></p><?php endif; ?>
      </div>
    </div>

    <?php if ($cause['body']): ?>
      <div class="prose prose-sm max-w-none prose-headings:text-primary-navy prose-a:text-gold mb-6 border-t border-slate-100 pt-6"><?= $cause['body'] ?></div>
    <?php endif; ?>

    <h3 class="section-title text-xs mb-2">How to Get Involved</h3>
    <button type="button" id="reveal-cause-btn" class="btn !bg-[var(--menu-accent-600)] !text-white hover:!bg-[var(--menu-accent-700)]">Get in Touch</button>
    <p id="reveal-cause-email" class="hidden text-sm text-slate-600 mt-3">
      Reach out to <strong class="text-primary-navy"><?= e($contactEmail) ?></strong> referencing "<?= e($cause['title']) ?>" and our Giving team will follow up with next steps.
    </p>
    <script>
      document.getElementById('reveal-cause-btn').addEventListener('click', function () {
        this.classList.add('hidden');
        document.getElementById('reveal-cause-email').classList.remove('hidden');
      });
    </script>
  </div>
</div>
