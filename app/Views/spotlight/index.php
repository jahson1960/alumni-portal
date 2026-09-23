<div class="max-w-[1280px] mx-auto px-4 md:px-8 py-8">

  <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-6 items-start">

    <!-- Sidebar -->
    <aside class="space-y-6 lg:sticky lg:top-[calc(var(--header-height)+1rem)] lg:max-h-[calc(100vh-var(--header-height)-2rem)] lg:overflow-y-auto">
      <div>
        <h1 class="text-2xl font-extrabold text-primary-navy">Alumni Spotlight</h1>
        <div class="w-10 h-1 bg-gold rounded-full my-2"></div>
        <p class="text-sm text-slate-500">Celebrating the achievements, leadership and impact of our outstanding alumni.</p>
      </div>

      <a href="mailto:<?= e($nominateEmail) ?>?subject=<?= rawurlencode('Alumni Spotlight Nomination') ?>" class="btn bg-white border border-gold !text-gold hover:bg-gold hover:!text-white !px-4 !py-2.5 text-xs w-full flex items-center justify-center gap-2"><i class="bi bi-star"></i> Nominate an Alumni</a>

      <div class="card p-4 !bg-sky-50 !border-sky-100">
        <p class="text-2xl text-sky-300 leading-none mb-1">&ldquo;</p>
        <p class="text-sm text-slate-600 italic">Our alumni inspire, lead and create lasting change across industries.</p>
      </div>
    </aside>

    <!-- Main -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
      <?php foreach ($alumni as $person): ?>
        <div class="card p-6 flex gap-4">
          <a href="<?= e(url('alumni/' . $person['id'])) ?>" class="flex-shrink-0"><?= avatar_html($person, 'w-16 h-16') ?></a>
          <div class="min-w-0">
            <div class="flex items-center gap-1.5">
              <a href="<?= e(url('alumni/' . $person['id'])) ?>" class="text-base font-bold text-primary-navy hover:text-gold"><?= e($person['name']) ?></a>
              <i class="bi bi-patch-check-fill text-gold text-sm" title="Spotlighted Alumni"></i>
            </div>
            <p class="text-xs text-slate-500 mt-0.5"><?= e($person['headline'] ?: '') ?><?= $person['company'] ? ' &middot; ' . e($person['company']) : '' ?></p>

            <?php
            $tags = array_slice(array_filter(array_map('trim', explode(',', (string) ($person['expertise_areas'] ?? '')))), 0, 2);
            ?>
            <?php if ($tags): ?>
              <div class="flex flex-wrap gap-1.5 mt-2">
                <?php foreach ($tags as $ti => $tag): ?>
                  <span class="<?= $ti % 2 === 0 ? 'badge-blue' : 'badge-green' ?> uppercase text-[0.65rem] tracking-wide"><?= e($tag) ?></span>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>

            <?php if ($person['spotlight_note']): ?>
              <p class="text-sm text-slate-600 mt-3 whitespace-pre-line"><?= e($person['spotlight_note']) ?></p>
            <?php endif; ?>

            <a href="<?= e(url('alumni/' . $person['id'])) ?>" class="text-xs font-semibold text-gold hover:underline inline-flex items-center gap-1 mt-3">View Profile <i class="fa-solid fa-arrow-right"></i></a>
          </div>
        </div>
      <?php endforeach; ?>
      <?php if (empty($alumni)): ?>
        <p class="text-sm text-slate-400 col-span-full">No alumni are currently spotlighted.</p>
      <?php endif; ?>
    </div>
  </div>
</div>
