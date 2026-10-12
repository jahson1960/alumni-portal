<?php
$showActions = $viewerId && !$isSelf;
$heroBackground = \App\Models\Setting::get('profile_hero_background', '');
$heroGradientColor = \App\Models\Setting::get('profile_hero_gradient_color', '#ffffff');
$heroGradientIntensity = (int) \App\Models\Setting::get('profile_hero_gradient_intensity', '70');
$heroGradientMovement = (int) \App\Models\Setting::get('profile_hero_gradient_transparent_zone', '0');
$heroTaglineTitle = \App\Models\Setting::get('profile_hero_tagline_title', 'RBS Alumni');
$heroTaglineSubtitle = \App\Models\Setting::get('profile_hero_tagline_subtitle', "A global network\nof changemakers");
$heroTaglineTitleColor = \App\Models\Setting::get('profile_hero_tagline_title_color', '#000000');
$heroTaglineSubtitleColor = \App\Models\Setting::get('profile_hero_tagline_subtitle_color', '#000000');
?>
<div class="max-w-[1400px] mx-auto px-4 md:px-8 py-8">
  <a href="<?= e($backUrl) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> <?= e($backLabel) ?></a>

  <!-- Hero -->
  <div class="card p-6 md:p-8 mb-6 relative overflow-hidden bg-cover bg-right" <?php if ($heroBackground): ?>style="background-image: <?= hero_gradient_css($heroGradientColor, $heroGradientIntensity, $heroGradientMovement) ?>, url('<?= e($heroBackground) ?>');"<?php endif; ?>>
    <?php if (!$heroBackground): ?>
      <div class="hidden md:flex absolute top-0 right-0 bottom-0 w-80 bg-gradient-to-br from-primary-navy to-slate-800 overflow-hidden flex-col justify-center">
        <i class="fa-solid fa-building-columns absolute -right-6 -top-6 text-white/5 text-[9rem]"></i>
        <div class="relative px-6 text-right">
          <p class="font-serif italic text-white text-lg leading-snug"><?= e($heroTaglineTitle) ?></p>
          <p class="font-serif italic text-white/60 text-xs leading-snug"><?= nl2br(e($heroTaglineSubtitle)) ?></p>
        </div>
      </div>
    <?php else: ?>
      <div class="hidden md:flex absolute inset-y-0 right-6 flex-col justify-center text-right max-w-[14rem]">
        <p class="font-serif italic text-lg leading-snug" style="color: <?= e($heroTaglineTitleColor) ?>"><?= e($heroTaglineTitle) ?></p>
        <p class="font-serif italic text-xs leading-snug" style="color: <?= e($heroTaglineSubtitleColor) ?>"><?= nl2br(e($heroTaglineSubtitle)) ?></p>
      </div>
    <?php endif; ?>

    <div class="relative flex items-start gap-3 sm:gap-5 md:max-w-[calc(100%-20rem)]">
      <?= avatar_html($profile, 'w-16 h-16 sm:w-24 sm:h-24 text-lg sm:text-2xl') ?>
      <div class="min-w-0">
        <div class="flex items-center gap-2 flex-wrap">
          <h1 class="text-xl font-extrabold text-primary-navy"><?= e($profile['name']) ?></h1>
          <i class="fa-solid fa-circle-check text-sky-500 text-sm" title="Verified Alumni"></i>
          <span class="text-[0.68rem] font-semibold"><?= online_status_html($profile['last_active_at'] ?? null, (int) $profile['id']) ?></span>
        </div>
        <?php if ($profile['headline']): ?><p class="text-sm text-slate-600"><?= e($profile['headline']) ?></p><?php endif; ?>
        <?php if ($profile['company']): ?><p class="text-sm text-slate-500"><?= e($profile['company']) ?><?= $profile['industry'] ? ' &middot; ' . e($profile['industry']) : '' ?></p><?php endif; ?>
        <div class="flex flex-wrap gap-3 mt-2 text-xs text-slate-500">
          <?php if (location_display($profile)): ?><span><i class="fa-solid fa-location-dot"></i> <?= e(location_display($profile)) ?></span><?php endif; ?>
          <?php if ($profile['program']): ?><span><i class="fa-solid fa-briefcase"></i> <?= e($profile['program']) ?></span><?php endif; ?>
          <?php if ($profile['graduation_year']): ?><span><i class="fa-solid fa-graduation-cap"></i> Class of <?= e($profile['graduation_year']) ?></span><?php endif; ?>
        </div>

        <?php if (!$isSelf): ?>
          <div class="flex flex-wrap items-center gap-2 mt-4">
            <?php if (!$viewerId): ?>
              <a href="<?= e(url('login')) ?>" class="btn-gold !px-4 !py-2 text-xs"><i class="fa-solid fa-user-plus"></i> Log in to Connect</a>
            <?php elseif ($connectionStatus === null): ?>
              <form method="POST" action="<?= e(url('connections/' . $profile['id'] . '/request')) ?>">
                <?= csrf_field() ?>
                <button type="submit" class="btn-gold !px-4 !py-2 text-xs"><i class="fa-solid fa-user-plus"></i> Connect</button>
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
              <a href="<?= e(url('messages/' . $profile['id'])) ?>" class="btn-gold !px-4 !py-2 text-xs"><i class="fa-regular fa-comment-dots"></i> Message</a>
              <form method="POST" action="<?= e(url('connections/' . $profile['id'] . '/remove')) ?>" onsubmit="return confirm('Remove this connection?');">
                <?= csrf_field() ?>
                <button type="submit" class="text-xs text-slate-500 hover:underline">Remove</button>
              </form>
            <?php endif; ?>

            <?php if ($viewerId): ?>
              <?php if ($isFollowing): ?>
                <form method="POST" action="<?= e(url('unfollow/' . $profile['id'])) ?>">
                  <?= csrf_field() ?>
                  <button type="submit" class="btn bg-slate-100 text-slate-600 hover:bg-slate-200 !px-4 !py-2 text-xs"><i class="fa-solid fa-check"></i> Following</button>
                </form>
              <?php else: ?>
                <form method="POST" action="<?= e(url('follow/' . $profile['id'])) ?>">
                  <?= csrf_field() ?>
                  <button type="submit" class="btn bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 !px-4 !py-2 text-xs"><i class="fa-solid fa-plus"></i> Follow</button>
                </form>
              <?php endif; ?>
            <?php endif; ?>

            <?php if ($viewerId): ?>
              <div class="relative">
                <button type="button" class="profile-kebab-btn btn bg-white border border-slate-200 text-slate-500 hover:bg-slate-50 !px-3 !py-2 text-xs" title="More options"><i class="fa-solid fa-ellipsis-vertical"></i></button>
                <div class="profile-kebab-menu hidden absolute left-0 top-full mt-1 z-20 bg-white border border-slate-200 rounded-lg shadow-lg w-48 py-1">
                  <button type="button" id="copy-profile-link" class="w-full flex items-center gap-2 text-left px-3 py-2 text-xs text-slate-700 hover:bg-slate-50"><i class="fa-solid fa-link w-4"></i> Copy Profile Link</button>
                  <form method="POST" action="<?= e(url('connections/' . $profile['id'] . '/block')) ?>" onsubmit="return confirm('Block this alumnus? They won\'t be able to contact you or send a connection request again.');">
                    <?= csrf_field() ?>
                    <button type="submit" class="w-full flex items-center gap-2 text-left px-3 py-2 text-xs text-red-600 hover:bg-red-50"><i class="fa-solid fa-ban w-4"></i> Block</button>
                  </form>
                </div>
              </div>
            <?php endif; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Tabs -->
  <div class="card overflow-x-auto mb-6">
    <div class="flex items-center px-6 gap-8 min-w-max text-sm font-medium">
      <button type="button" class="profile-tab-link py-4 text-gold border-b-2 border-gold font-semibold flex items-center gap-2" data-tab="about"><i class="fa-regular fa-clock text-xs"></i> About</button>
      <button type="button" class="profile-tab-link py-4 text-slate-500 hover:text-slate-900 border-b-2 border-transparent transition flex items-center gap-2" data-tab="education"><i class="fa-solid fa-graduation-cap text-xs"></i> Education</button>
      <button type="button" class="profile-tab-link py-4 text-slate-500 hover:text-slate-900 border-b-2 border-transparent transition flex items-center gap-2" data-tab="skills"><i class="fa-solid fa-wand-magic-sparkles text-xs"></i> Skills</button>
      <button type="button" class="profile-tab-link py-4 text-slate-500 hover:text-slate-900 border-b-2 border-transparent transition flex items-center gap-2" data-tab="experience"><i class="fa-solid fa-briefcase text-xs"></i> Experience</button>
      <button type="button" class="profile-tab-link py-4 text-slate-500 hover:text-slate-900 border-b-2 border-transparent transition flex items-center gap-2" data-tab="interests"><i class="fa-regular fa-heart text-xs"></i> Interests</button>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
    <!-- Main content -->
    <div class="lg:col-span-2 space-y-6">
      <div class="profile-tab-panel space-y-6" data-tab="about">
        <div class="card p-6">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-8 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center flex-shrink-0"><i class="fa-regular fa-user text-sm"></i></div>
            <h3 class="text-base font-bold text-slate-900">About</h3>
          </div>
          <?php if ($profile['bio']): ?>
            <div class="prose prose-sm max-w-none prose-headings:text-slate-900 prose-a:text-gold text-slate-600 pl-11"><?= $profile['bio'] ?></div>
          <?php else: ?>
            <p class="text-sm text-slate-400 pl-11">No bio added yet.</p>
          <?php endif; ?>
        </div>

        <div class="card p-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-tag text-sm"></i></div>
            <h3 class="text-base font-bold text-slate-900">Skills</h3>
          </div>
          <?php if ($profile['skills']): ?>
            <div class="flex flex-wrap gap-2 pl-11">
              <?php foreach (array_filter(array_map('trim', explode(',', $profile['skills']))) as $skill): ?>
                <span class="bg-sky-50 text-blue-600 text-xs font-medium px-3.5 py-1.5 rounded-full border border-sky-100"><?= e($skill) ?></span>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-sm text-slate-400 pl-11">No skills listed yet.</p>
          <?php endif; ?>
        </div>

        <div class="card p-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-bullseye text-sm"></i></div>
            <h3 class="text-base font-bold text-slate-900">Areas of Expertise</h3>
          </div>
          <?php if ($profile['expertise_areas']): ?>
            <div class="flex flex-wrap gap-2 pl-11">
              <?php foreach (array_filter(array_map('trim', explode(',', $profile['expertise_areas']))) as $area): ?>
                <span class="bg-amber-50 text-amber-800 text-xs font-medium px-3.5 py-1.5 rounded-full border border-amber-200/50"><?= e($area) ?></span>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-sm text-slate-400 pl-11">No areas of expertise listed yet.</p>
          <?php endif; ?>
        </div>

        <div class="card p-6">
          <div class="flex items-center gap-3 mb-6">
            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-timeline text-sm"></i></div>
            <h3 class="text-base font-bold text-slate-900">Experience &amp; Background</h3>
          </div>
          <?php if ($experience): ?>
            <?php $lastExpKeyAbout = array_key_last($experience); ?>
            <div class="space-y-6 pl-11">
              <?php foreach ($experience as $expKey => $exp): ?>
                <div class="grid grid-cols-1 md:grid-cols-12 gap-4 md:gap-6<?= $expKey !== $lastExpKeyAbout ? ' pb-6 border-b border-slate-100' : '' ?>">
                  <div class="md:col-span-5 flex items-start gap-3">
                    <div class="w-12 h-12 rounded-xl bg-slate-900 flex items-center justify-center text-amber-400 flex-shrink-0 shadow-sm"><i class="fa-solid fa-cube text-lg"></i></div>
                    <div>
                      <h4 class="text-sm font-bold text-slate-900"><?= e($exp['company']) ?></h4>
                      <p class="text-xs font-medium text-slate-700"><?= e($exp['title']) ?></p>
                      <p class="text-[11px] text-slate-400 mt-0.5"><?= $exp['start_date'] ? e(format_date($exp['start_date'], 'M Y')) : '' ?> &ndash; <?= $exp['is_current'] ? 'Present' : ($exp['end_date'] ? e(format_date($exp['end_date'], 'M Y')) : '') ?></p>
                      <?php if ($exp['location']): ?><div class="flex items-center gap-1 text-[11px] text-slate-500 mt-1"><i class="fa-solid fa-location-dot"></i> <?= e($exp['location']) ?></div><?php endif; ?>
                    </div>
                  </div>
                  <?php if ($exp['description']): ?>
                    <div class="md:col-span-7">
                      <p class="text-slate-600 text-xs sm:text-sm leading-relaxed"><?= nl2br(e($exp['description'])) ?></p>
                    </div>
                  <?php endif; ?>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-sm text-slate-400 pl-11">No experience added yet.</p>
          <?php endif; ?>
        </div>

        <div class="card p-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-briefcase text-sm"></i></div>
            <h3 class="text-base font-bold text-slate-900">Business Interests</h3>
          </div>
          <?php if ($profile['business_interests']): ?>
            <div class="flex flex-wrap gap-2 pl-11">
              <?php foreach (array_filter(array_map('trim', explode(',', $profile['business_interests']))) as $interest): ?>
                <span class="bg-purple-50 text-purple-700 text-xs font-medium px-3.5 py-1.5 rounded-full border border-purple-100"><?= e($interest) ?></span>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-sm text-slate-400 pl-11">No business interests listed yet.</p>
          <?php endif; ?>
        </div>

        <div class="card p-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center flex-shrink-0"><i class="fa-regular fa-heart text-sm"></i></div>
            <h3 class="text-base font-bold text-slate-900">Personal Interests</h3>
          </div>
          <?php if ($profile['personal_interests']): ?>
            <div class="flex flex-wrap gap-2 pl-11">
              <?php foreach (array_filter(array_map('trim', explode(',', $profile['personal_interests']))) as $interest): ?>
                <span class="bg-rose-50 text-rose-700 text-xs font-medium px-3.5 py-1.5 rounded-full border border-rose-100"><?= e($interest) ?></span>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-sm text-slate-400 pl-11">No personal interests listed yet.</p>
          <?php endif; ?>
        </div>

        <div class="card p-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-graduation-cap text-sm"></i></div>
            <h3 class="text-base font-bold text-slate-900">Education</h3>
          </div>
          <?php if ($education): ?>
            <div class="space-y-4 pl-11">
              <?php foreach ($education as $edu): ?>
                <div>
                  <p class="text-sm font-bold text-slate-900"><?= e($edu['school']) ?></p>
                  <p class="text-xs text-slate-500"><?= e(trim(($edu['degree'] ?? '') . (($edu['degree'] && $edu['field_of_study']) ? ', ' : '') . ($edu['field_of_study'] ?? ''))) ?></p>
                  <p class="text-[0.68rem] text-slate-400 mt-0.5"><?= e($edu['start_year'] ?? '') ?><?= $edu['start_year'] || $edu['end_year'] ? ' – ' : '' ?><?= e($edu['end_year'] ?? '') ?></p>
                </div>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-sm text-slate-400 pl-11">No education added yet.</p>
          <?php endif; ?>
        </div>
      </div>

      <div class="profile-tab-panel hidden card p-6" data-tab="education">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-8 h-8 rounded-full bg-indigo-50 text-indigo-500 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-graduation-cap text-sm"></i></div>
          <h3 class="text-base font-bold text-slate-900">Education</h3>
        </div>
        <?php if ($education): ?>
          <div class="space-y-4 pl-11">
            <?php foreach ($education as $edu): ?>
              <div>
                <p class="text-sm font-bold text-slate-900"><?= e($edu['school']) ?></p>
                <p class="text-xs text-slate-500"><?= e(trim(($edu['degree'] ?? '') . (($edu['degree'] && $edu['field_of_study']) ? ', ' : '') . ($edu['field_of_study'] ?? ''))) ?></p>
                <p class="text-[0.68rem] text-slate-400 mt-0.5"><?= e($edu['start_year'] ?? '') ?><?= $edu['start_year'] || $edu['end_year'] ? ' – ' : '' ?><?= e($edu['end_year'] ?? '') ?></p>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="text-sm text-slate-400 pl-11">No education added yet.</p>
        <?php endif; ?>
      </div>

      <div class="profile-tab-panel hidden space-y-6" data-tab="skills">
        <div class="card p-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-tag text-sm"></i></div>
            <h3 class="text-base font-bold text-slate-900">Skills</h3>
          </div>
          <?php if ($profile['skills']): ?>
            <div class="flex flex-wrap gap-2 pl-11">
              <?php foreach (array_filter(array_map('trim', explode(',', $profile['skills']))) as $skill): ?>
                <span class="bg-sky-50 text-blue-600 text-xs font-medium px-3.5 py-1.5 rounded-full border border-sky-100"><?= e($skill) ?></span>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-sm text-slate-400 pl-11">No skills listed yet.</p>
          <?php endif; ?>
        </div>

        <div class="card p-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-bullseye text-sm"></i></div>
            <h3 class="text-base font-bold text-slate-900">Areas of Expertise</h3>
          </div>
          <?php if ($profile['expertise_areas']): ?>
            <div class="flex flex-wrap gap-2 pl-11">
              <?php foreach (array_filter(array_map('trim', explode(',', $profile['expertise_areas']))) as $area): ?>
                <span class="bg-amber-50 text-amber-800 text-xs font-medium px-3.5 py-1.5 rounded-full border border-amber-200/50"><?= e($area) ?></span>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-sm text-slate-400 pl-11">No areas of expertise listed yet.</p>
          <?php endif; ?>
        </div>
      </div>

      <div class="profile-tab-panel hidden space-y-6" data-tab="interests">
        <div class="card p-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-briefcase text-sm"></i></div>
            <h3 class="text-base font-bold text-slate-900">Business Interests</h3>
          </div>
          <?php if ($profile['business_interests']): ?>
            <div class="flex flex-wrap gap-2 pl-11">
              <?php foreach (array_filter(array_map('trim', explode(',', $profile['business_interests']))) as $interest): ?>
                <span class="bg-purple-50 text-purple-700 text-xs font-medium px-3.5 py-1.5 rounded-full border border-purple-100"><?= e($interest) ?></span>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-sm text-slate-400 pl-11">No business interests listed yet.</p>
          <?php endif; ?>
        </div>

        <div class="card p-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-full bg-rose-50 text-rose-500 flex items-center justify-center flex-shrink-0"><i class="fa-regular fa-heart text-sm"></i></div>
            <h3 class="text-base font-bold text-slate-900">Personal Interests</h3>
          </div>
          <?php if ($profile['personal_interests']): ?>
            <div class="flex flex-wrap gap-2 pl-11">
              <?php foreach (array_filter(array_map('trim', explode(',', $profile['personal_interests']))) as $interest): ?>
                <span class="bg-rose-50 text-rose-700 text-xs font-medium px-3.5 py-1.5 rounded-full border border-rose-100"><?= e($interest) ?></span>
              <?php endforeach; ?>
            </div>
          <?php else: ?>
            <p class="text-sm text-slate-400 pl-11">No personal interests listed yet.</p>
          <?php endif; ?>
        </div>
      </div>

      <div class="profile-tab-panel hidden card p-6" data-tab="experience">
        <div class="flex items-center gap-3 mb-6">
          <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-timeline text-sm"></i></div>
          <h3 class="text-base font-bold text-slate-900">Experience &amp; Background</h3>
        </div>
        <?php if ($experience): ?>
          <?php $lastExpKey = array_key_last($experience); ?>
          <div class="space-y-6 pl-11">
            <?php foreach ($experience as $expKey => $exp): ?>
              <div class="grid grid-cols-1 md:grid-cols-12 gap-4 md:gap-6<?= $expKey !== $lastExpKey ? ' pb-6 border-b border-slate-100' : '' ?>">
                <div class="md:col-span-5 flex items-start gap-3">
                  <div class="w-12 h-12 rounded-xl bg-slate-900 flex items-center justify-center text-amber-400 flex-shrink-0 shadow-sm"><i class="fa-solid fa-cube text-lg"></i></div>
                  <div>
                    <h4 class="text-sm font-bold text-slate-900"><?= e($exp['company']) ?></h4>
                    <p class="text-xs font-medium text-slate-700"><?= e($exp['title']) ?></p>
                    <p class="text-[11px] text-slate-400 mt-0.5"><?= $exp['start_date'] ? e(format_date($exp['start_date'], 'M Y')) : '' ?> &ndash; <?= $exp['is_current'] ? 'Present' : ($exp['end_date'] ? e(format_date($exp['end_date'], 'M Y')) : '') ?></p>
                    <?php if ($exp['location']): ?><div class="flex items-center gap-1 text-[11px] text-slate-500 mt-1"><i class="fa-solid fa-location-dot"></i> <?= e($exp['location']) ?></div><?php endif; ?>
                  </div>
                </div>
                <?php if ($exp['description']): ?>
                  <div class="md:col-span-7">
                    <p class="text-slate-600 text-xs sm:text-sm leading-relaxed"><?= nl2br(e($exp['description'])) ?></p>
                  </div>
                <?php endif; ?>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="text-sm text-slate-400 pl-11">No experience added yet.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Sidebar -->
    <div class="lg:col-span-1 space-y-6 lg:sticky lg:top-[calc(var(--header-height)+1rem)] lg:self-start">
      <div class="card p-6">
        <div class="flex items-center gap-3 mb-4">
          <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center flex-shrink-0"><i class="fa-regular fa-address-book text-sm"></i></div>
          <h3 class="text-sm font-bold text-slate-900">Contact Information</h3>
        </div>
        <div class="space-y-3.5 pl-11 text-xs">
          <?php if (($isOwnerOrAdmin || $profile['show_phone']) && $profile['phone']): ?>
            <div class="flex items-start gap-3">
              <i class="fa-solid fa-phone text-slate-400 mt-0.5"></i>
              <div><a href="tel:<?= e($profile['phone']) ?>" class="font-medium text-slate-800 hover:text-blue-600"><?= e($profile['phone']) ?></a><p class="text-[11px] text-slate-400">Phone</p></div>
            </div>
          <?php endif; ?>
          <?php if ($isOwnerOrAdmin || $profile['show_email']): ?>
            <div class="flex items-start gap-3">
              <i class="fa-regular fa-envelope text-slate-400 mt-0.5"></i>
              <div><a href="mailto:<?= e($profile['email']) ?>" class="font-medium text-slate-800 hover:text-blue-600 break-all"><?= e($profile['email']) ?></a><p class="text-[11px] text-slate-400">Email</p></div>
            </div>
          <?php endif; ?>
          <?php if (location_display($profile)): ?>
            <div class="flex items-start gap-3">
              <i class="fa-solid fa-location-dot text-slate-400 mt-0.5"></i>
              <div><span class="font-medium text-slate-800"><?= e(location_display($profile)) ?></span><p class="text-[11px] text-slate-400">Location</p></div>
            </div>
          <?php endif; ?>
          <?php if ($profile['linkedin_url']): ?>
            <div class="flex items-start gap-3">
              <i class="fa-brands fa-linkedin text-slate-400 mt-0.5"></i>
              <div><a href="<?= e($profile['linkedin_url']) ?>" target="_blank" rel="noopener noreferrer" class="font-medium text-blue-600 hover:underline flex items-center gap-1">LinkedIn Profile <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i></a><p class="text-[11px] text-slate-400">View profile</p></div>
            </div>
          <?php endif; ?>
          <?php if (!$isOwnerOrAdmin && !$profile['show_email'] && !(($profile['show_phone']) && $profile['phone']) && !location_display($profile) && !$profile['linkedin_url']): ?>
            <p class="text-slate-400">This alumnus hasn't shared contact details.</p>
          <?php endif; ?>
        </div>
      </div>

      <?php if ($showActions): ?>
        <div class="card p-6">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-8 h-8 rounded-full bg-blue-50 text-blue-500 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-users text-sm"></i></div>
            <h3 class="text-sm font-bold text-slate-900">In your network</h3>
          </div>
          <div class="pl-11 space-y-3">
            <p class="text-xs text-slate-600">
              <?= $mutualCount > 0 ? 'You are connected with ' . $mutualCount . ' mutual alumn' . ($mutualCount === 1 ? 'us' : 'i') . '.' : "You don't have any mutual connections yet." ?>
            </p>
            <a href="<?= e(url('alumni/' . $profile['id'] . '/mutual-connections')) ?>" class="block w-full text-center bg-slate-50 hover:bg-slate-100 text-blue-600 border border-blue-200/60 font-semibold text-xs py-2 rounded-lg transition">View Mutual Connections</a>
          </div>
        </div>

        <div class="card p-6">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-8 h-8 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-bolt text-sm"></i></div>
            <h3 class="text-sm font-bold text-slate-900">Quick Actions</h3>
          </div>
          <div class="space-y-1 pl-11">
            <a href="<?= e(url('messages/' . $profile['id'])) ?>" class="flex items-center justify-between py-2 text-slate-700 hover:text-blue-600 text-xs font-medium transition group">
              <span class="flex items-center gap-2.5"><i class="fa-regular fa-paper-plane text-blue-500"></i> Send a Message</span>
              <i class="fa-solid fa-chevron-right text-[10px] text-slate-400 group-hover:translate-x-0.5 transition"></i>
            </a>

            <?php if ($upcomingEvents): ?>
              <button type="button" class="quick-action-toggle w-full flex items-center justify-between py-2 text-slate-700 hover:text-blue-600 text-xs font-medium transition group" data-target="invite-panel">
                <span class="flex items-center gap-2.5"><i class="fa-regular fa-calendar-plus text-blue-500"></i> Invite to Event</span>
                <i class="fa-solid fa-chevron-right text-[10px] text-slate-400 group-hover:translate-x-0.5 transition"></i>
              </button>
              <div id="invite-panel" class="hidden py-3">
                <form method="POST" action="<?= e(url('alumni/' . $profile['id'] . '/invite-to-event')) ?>" class="space-y-2">
                  <?= csrf_field() ?>
                  <select name="event_id" class="form-input text-xs" required>
                    <option value="">Select an event…</option>
                    <?php foreach ($upcomingEvents as $event): ?>
                      <option value="<?= e((string) $event['id']) ?>"><?= e($event['title']) ?> &mdash; <?= e(format_date($event['event_date'], 'M j, Y')) ?></option>
                    <?php endforeach; ?>
                  </select>
                  <button type="submit" class="btn-gold !py-1.5 text-xs w-full">Send Invite</button>
                </form>
              </div>
            <?php endif; ?>

            <button type="button" class="quick-action-toggle w-full flex items-center justify-between py-2 text-rose-600 hover:text-rose-700 text-xs font-medium transition group" data-target="report-panel">
              <span class="flex items-center gap-2.5"><i class="fa-regular fa-flag text-rose-500"></i> Report Profile</span>
              <i class="fa-solid fa-chevron-right text-[10px] text-slate-400 group-hover:translate-x-0.5 transition"></i>
            </button>
            <div id="report-panel" class="hidden py-3">
              <form method="POST" action="<?= e(url('alumni/' . $profile['id'] . '/report')) ?>" class="space-y-2">
                <?= csrf_field() ?>
                <select name="reason" class="form-input text-xs" required>
                  <option value="">Select a reason…</option>
                  <option>Impersonation or fake profile</option>
                  <option>Inappropriate content</option>
                  <option>Spam or scam</option>
                  <option>Harassment</option>
                  <option>Other</option>
                </select>
                <textarea name="notes" rows="2" class="form-textarea text-xs" placeholder="Additional details (optional)"></textarea>
                <button type="submit" class="btn bg-red-50 !text-red-600 hover:bg-red-100 !py-1.5 text-xs w-full">Submit Report</button>
              </form>
            </div>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var tabLinks = document.querySelectorAll('.profile-tab-link');
    var tabPanels = document.querySelectorAll('.profile-tab-panel');

    function activateTab(tab) {
      tabPanels.forEach(function (p) { p.classList.toggle('hidden', p.dataset.tab !== tab); });
      tabLinks.forEach(function (l) {
        var active = l.dataset.tab === tab;
        l.classList.toggle('text-gold', active);
        l.classList.toggle('border-gold', active);
        l.classList.toggle('text-slate-500', !active);
        l.classList.toggle('border-transparent', !active);
      });
    }

    tabLinks.forEach(function (link) {
      link.addEventListener('click', function () {
        activateTab(link.dataset.tab);
      });
    });

    document.querySelectorAll('.profile-kebab-btn').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.stopPropagation();
        var menu = btn.nextElementSibling;
        document.querySelectorAll('.profile-kebab-menu').forEach(function (m) { if (m !== menu) m.classList.add('hidden'); });
        menu.classList.toggle('hidden');
      });
    });

    document.addEventListener('click', function () {
      document.querySelectorAll('.profile-kebab-menu').forEach(function (m) { m.classList.add('hidden'); });
    });

    document.querySelectorAll('.quick-action-toggle').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var panel = document.getElementById(btn.dataset.target);
        if (panel) panel.classList.toggle('hidden');
      });
    });

    var copyBtn = document.getElementById('copy-profile-link');
    if (copyBtn) {
      copyBtn.addEventListener('click', function () {
        var url = window.location.href;
        var done = function () {
          var original = copyBtn.innerHTML;
          copyBtn.innerHTML = '<i class="fa-solid fa-check w-4"></i> Link Copied';
          setTimeout(function () { copyBtn.innerHTML = original; }, 1500);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(url).then(done).catch(function () {});
        }
      });
    }
  });
</script>
