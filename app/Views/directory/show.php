<div class="max-w-3xl mx-auto px-4 md:px-8 py-8">
  <a href="<?= e($backUrl) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> <?= e($backLabel) ?></a>

  <div class="card p-6 md:p-8">
    <div class="flex items-start gap-5 mb-6">
      <?= avatar_html($profile, 'w-20 h-20 text-xl') ?>
      <div>
        <div class="flex items-center gap-2 flex-wrap">
          <h1 class="text-xl font-extrabold text-primary-navy"><?= e($profile['name']) ?></h1>
          <span class="text-[0.68rem] font-semibold"><?= online_status_html($profile['last_active_at'] ?? null, (int) $profile['id']) ?></span>
        </div>
        <?php if ($profile['headline']): ?><p class="text-sm text-slate-600"><?= e($profile['headline']) ?></p><?php endif; ?>
        <?php if ($profile['company']): ?><p class="text-sm text-slate-500"><?= e($profile['company']) ?><?= $profile['industry'] ? ' &middot; ' . e($profile['industry']) : '' ?></p><?php endif; ?>
        <div class="flex flex-wrap gap-3 mt-2 text-xs text-slate-500">
          <?php if (location_display($profile)): ?><span><i class="fa-solid fa-location-dot"></i> <?= e(location_display($profile)) ?></span><?php endif; ?>
          <?php if ($profile['program']): ?><span><i class="fa-solid fa-graduation-cap"></i> <?= e($profile['program']) ?></span><?php endif; ?>
          <?php if ($profile['graduation_year']): ?><span><i class="fa-solid fa-calendar"></i> Class of <?= e($profile['graduation_year']) ?></span><?php endif; ?>
        </div>
        <div class="flex flex-wrap gap-4 mt-2">
          <?php if ($profile['linkedin_url']): ?>
            <a href="<?= e($profile['linkedin_url']) ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-xs text-sky-600 font-semibold hover:underline"><i class="fa-brands fa-linkedin"></i> LinkedIn</a>
          <?php endif; ?>
          <?php if ($profile['personal_website']): ?>
            <a href="<?= e($profile['personal_website']) ?>" target="_blank" rel="noopener" class="inline-flex items-center gap-1 text-xs text-sky-600 font-semibold hover:underline"><i class="fa-solid fa-globe"></i> Website</a>
          <?php endif; ?>
          <?php if ($isOwnerOrAdmin || $profile['show_email']): ?>
            <span class="inline-flex items-center gap-1 text-xs text-slate-500"><i class="fa-solid fa-envelope"></i> <?= e($profile['email']) ?></span>
          <?php endif; ?>
          <?php if (($isOwnerOrAdmin || $profile['show_phone']) && $profile['phone']): ?>
            <span class="inline-flex items-center gap-1 text-xs text-slate-500"><i class="fa-solid fa-phone"></i> <?= e($profile['phone']) ?></span>
          <?php endif; ?>
        </div>

        <?php if (!$isOwnerOrAdmin): ?>
          <div class="flex flex-wrap items-center gap-2 mt-4">
            <?php if (!$viewerId): ?>
              <a href="<?= e(url('login')) ?>" class="btn-gold !px-4 !py-2 text-xs">Log in to Connect</a>
            <?php elseif ($connectionStatus === null): ?>
              <form method="POST" action="<?= e(url('connections/' . $profile['id'] . '/request')) ?>">
                <?= csrf_field() ?>
                <button type="submit" class="btn-gold !px-4 !py-2 text-xs">Connect</button>
              </form>
            <?php elseif ($connectionStatus['status'] === 'pending' && (int) $connectionStatus['requester_id'] === (int) $viewerId): ?>
              <span class="badge bg-slate-100 text-slate-600 !text-xs !px-3 !py-2">Request Sent</span>
              <form method="POST" action="<?= e(url('connections/' . $profile['id'] . '/cancel')) ?>">
                <?= csrf_field() ?>
                <button type="submit" class="text-xs text-slate-500 hover:underline">Cancel</button>
              </form>
            <?php elseif ($connectionStatus['status'] === 'pending'): ?>
              <form method="POST" action="<?= e(url('connections/' . $connectionStatus['id'] . '/accept')) ?>">
                <?= csrf_field() ?>
                <button type="submit" class="btn-gold !px-4 !py-2 text-xs">Accept Request</button>
              </form>
              <form method="POST" action="<?= e(url('connections/' . $connectionStatus['id'] . '/decline')) ?>">
                <?= csrf_field() ?>
                <button type="submit" class="text-xs text-slate-500 hover:underline">Decline</button>
              </form>
            <?php else: ?>
              <span class="badge bg-green-100 text-green-700 !text-xs !px-3 !py-2"><i class="fa-solid fa-check"></i> Connected</span>
              <a href="<?= e(url('messages/' . $profile['id'])) ?>" class="btn-gold !px-4 !py-2 text-xs">Message</a>
              <form method="POST" action="<?= e(url('connections/' . $profile['id'] . '/remove')) ?>" onsubmit="return confirm('Remove this connection?');">
                <?= csrf_field() ?>
                <button type="submit" class="text-xs text-slate-500 hover:underline">Remove</button>
              </form>
            <?php endif; ?>

            <?php if ($viewerId): ?>
              <?php if ($isFollowing): ?>
                <form method="POST" action="<?= e(url('unfollow/' . $profile['id'])) ?>">
                  <?= csrf_field() ?>
                  <button type="submit" class="btn bg-slate-100 text-slate-600 hover:bg-slate-200 !px-4 !py-2 text-xs">Following</button>
                </form>
              <?php else: ?>
                <form method="POST" action="<?= e(url('follow/' . $profile['id'])) ?>">
                  <?= csrf_field() ?>
                  <button type="submit" class="btn bg-slate-100 text-slate-600 hover:bg-slate-200 !px-4 !py-2 text-xs">+ Follow</button>
                </form>
              <?php endif; ?>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <?php if ($profile['bio']): ?>
      <div class="border-t border-slate-100 pt-5 mb-5">
        <h3 class="section-title text-xs mb-2">About</h3>
        <div class="prose prose-sm max-w-none prose-headings:text-primary-navy prose-a:text-gold text-slate-600"><?= $profile['bio'] ?></div>
      </div>
    <?php endif; ?>

    <?php if ($profile['skills']): ?>
      <div class="border-t border-slate-100 pt-5 mb-5">
        <h3 class="section-title text-xs mb-2">Skills</h3>
        <div><?= tag_list_html($profile['skills']) ?></div>
      </div>
    <?php endif; ?>

    <?php if ($profile['expertise_areas']): ?>
      <div class="border-t border-slate-100 pt-5 mb-5">
        <h3 class="section-title text-xs mb-2">Areas of Expertise</h3>
        <div><?= tag_list_html($profile['expertise_areas'], 'badge-gold') ?></div>
      </div>
    <?php endif; ?>

    <?php if ($profile['business_interests']): ?>
      <div class="border-t border-slate-100 pt-5">
        <h3 class="section-title text-xs mb-2">Business Interests</h3>
        <div><?= tag_list_html($profile['business_interests']) ?></div>
      </div>
    <?php endif; ?>
  </div>
</div>
