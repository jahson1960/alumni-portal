<?php $showActions = $viewerId && !$isOwnerOrAdmin; ?>
<div class="max-w-[1400px] mx-auto px-4 md:px-8 py-8">
  <a href="<?= e($backUrl) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> <?= e($backLabel) ?></a>

  <!-- Hero -->
  <div class="card p-6 md:p-8 mb-6 relative overflow-hidden">
    <div class="hidden md:block absolute top-0 right-0 bottom-0 w-80 bg-gradient-to-br from-primary-navy to-slate-800 overflow-hidden">
      <i class="fa-solid fa-building-columns absolute -right-6 -top-6 text-white/5 text-[9rem]"></i>
      <div class="absolute bottom-6 right-6 left-6 text-right">
        <p class="font-serif italic text-white text-lg leading-snug">RBS Alumni</p>
        <p class="font-serif italic text-white/60 text-xs leading-snug">A global network<br>of changemakers</p>
      </div>
    </div>

    <div class="relative flex items-start gap-5 md:max-w-[calc(100%-20rem)]">
      <?= avatar_html($profile, 'w-24 h-24 text-2xl') ?>
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

        <?php if (!$isOwnerOrAdmin): ?>
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

  <!-- Section nav -->
  <div class="flex flex-nowrap items-center gap-6 mb-6 border-b border-slate-200 overflow-x-auto overflow-y-hidden">
    <a href="#about" class="profile-jump flex items-center gap-1.5 text-sm font-semibold pb-2.5 border-b-2 -mb-px whitespace-nowrap text-gold border-gold" data-tab="about"><i class="fa-regular fa-clock"></i> About</a>
    <a href="#skills" class="profile-jump flex items-center gap-1.5 text-sm font-semibold pb-2.5 border-b-2 -mb-px whitespace-nowrap text-slate-500 border-transparent hover:text-primary-navy" data-tab="skills"><i class="fa-solid fa-wand-magic-sparkles"></i> Skills</a>
    <a href="#experience" class="profile-jump flex items-center gap-1.5 text-sm font-semibold pb-2.5 border-b-2 -mb-px whitespace-nowrap text-slate-500 border-transparent hover:text-primary-navy" data-tab="experience"><i class="fa-solid fa-briefcase"></i> Experience</a>
    <a href="#interests" class="profile-jump flex items-center gap-1.5 text-sm font-semibold pb-2.5 border-b-2 -mb-px whitespace-nowrap text-slate-500 border-transparent hover:text-primary-navy" data-tab="interests"><i class="fa-regular fa-heart"></i> Interests</a>
    <a href="#contact-info" class="profile-jump flex items-center gap-1.5 text-sm font-semibold pb-2.5 border-b-2 -mb-px whitespace-nowrap text-slate-500 border-transparent hover:text-primary-navy" data-tab="contact"><i class="fa-regular fa-id-badge"></i> Contact</a>
    <div class="relative group pb-2.5 -mb-px">
      <button type="button" class="flex items-center gap-1.5 text-sm font-semibold whitespace-nowrap text-slate-500 hover:text-primary-navy">More <i class="fa-solid fa-chevron-down text-[0.6rem]"></i></button>
      <div class="absolute left-0 top-full hidden group-hover:block z-20 pt-1">
        <div class="bg-white min-w-[160px] shadow-lg rounded-md border border-slate-200 py-2">
          <a href="#education" class="profile-jump block px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 hover:text-gold" data-tab="education">Education</a>
        </div>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6 items-start">
    <!-- Main content -->
    <div class="card p-6 md:p-8 space-y-6">
      <div id="about">
        <h3 class="text-sm font-bold text-primary-navy flex items-center gap-2 mb-2"><span class="w-7 h-7 rounded-full bg-[var(--menu-accent-100)] text-[var(--menu-accent-600)] flex items-center justify-center flex-shrink-0 text-xs"><i class="fa-regular fa-user"></i></span> About</h3>
        <?php if ($profile['bio']): ?>
          <div class="prose prose-sm max-w-none prose-headings:text-primary-navy prose-a:text-gold text-slate-600"><?= $profile['bio'] ?></div>
        <?php else: ?>
          <p class="text-xs text-slate-400">No bio added yet.</p>
        <?php endif; ?>
      </div>

      <div id="skills" class="border-t border-slate-100 pt-6 space-y-4">
        <div>
          <h3 class="text-sm font-bold text-primary-navy flex items-center gap-2 mb-2"><span class="w-7 h-7 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0 text-xs"><i class="fa-solid fa-tag"></i></span> Skills</h3>
          <?php if ($profile['skills']): ?>
            <div><?= tag_list_html($profile['skills']) ?></div>
          <?php else: ?>
            <p class="text-xs text-slate-400">No skills listed yet.</p>
          <?php endif; ?>
        </div>
        <div>
          <h3 class="text-sm font-bold text-primary-navy flex items-center gap-2 mb-2"><span class="w-7 h-7 rounded-full bg-green-100 text-green-600 flex items-center justify-center flex-shrink-0 text-xs"><i class="fa-solid fa-bullseye"></i></span> Areas of Expertise</h3>
          <?php if ($profile['expertise_areas']): ?>
            <div><?= tag_list_html($profile['expertise_areas'], 'badge-gold') ?></div>
          <?php else: ?>
            <p class="text-xs text-slate-400">No areas of expertise listed yet.</p>
          <?php endif; ?>
        </div>
      </div>

      <div id="experience" class="border-t border-slate-100 pt-6">
        <h3 class="text-sm font-bold text-primary-navy flex items-center gap-2 mb-4"><span class="w-7 h-7 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0 text-xs"><i class="fa-solid fa-briefcase"></i></span> Experience &amp; Background</h3>
        <?php if ($experience): ?>
          <div class="space-y-5">
            <?php foreach ($experience as $exp): ?>
              <div class="flex gap-4">
                <div class="w-12 h-12 rounded-lg bg-slate-100 text-slate-400 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-building"></i></div>
                <div class="min-w-0">
                  <p class="text-sm font-bold text-primary-navy"><?= e($exp['company']) ?></p>
                  <p class="text-xs text-slate-600 font-medium"><?= e($exp['title']) ?></p>
                  <p class="text-[0.68rem] text-slate-400 mt-0.5">
                    <?= $exp['start_date'] ? e(format_date($exp['start_date'], 'M Y')) : '' ?> &ndash; <?= $exp['is_current'] ? 'Present' : ($exp['end_date'] ? e(format_date($exp['end_date'], 'M Y')) : '') ?>
                    <?= $exp['location'] ? ' &middot; ' . e($exp['location']) : '' ?>
                  </p>
                  <?php if ($exp['description']): ?><p class="text-xs text-slate-500 mt-1.5 leading-relaxed"><?= nl2br(e($exp['description'])) ?></p><?php endif; ?>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="text-xs text-slate-400">No experience added yet.</p>
        <?php endif; ?>
      </div>

      <div id="interests" class="border-t border-slate-100 pt-6 space-y-4">
        <div>
          <h3 class="text-sm font-bold text-primary-navy flex items-center gap-2 mb-2"><span class="w-7 h-7 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center flex-shrink-0 text-xs"><i class="fa-solid fa-people-group"></i></span> Business Interests</h3>
          <?php if ($profile['business_interests']): ?>
            <div><?= tag_list_html($profile['business_interests']) ?></div>
          <?php else: ?>
            <p class="text-xs text-slate-400">No business interests listed yet.</p>
          <?php endif; ?>
        </div>
        <div>
          <h3 class="text-sm font-bold text-primary-navy flex items-center gap-2 mb-2"><span class="w-7 h-7 rounded-full bg-rose-100 text-rose-600 flex items-center justify-center flex-shrink-0 text-xs"><i class="fa-regular fa-heart"></i></span> Personal Interests</h3>
          <?php if ($profile['personal_interests']): ?>
            <div><?= tag_list_html($profile['personal_interests'], 'badge bg-rose-50 text-rose-600') ?></div>
          <?php else: ?>
            <p class="text-xs text-slate-400">No personal interests listed yet.</p>
          <?php endif; ?>
        </div>
      </div>

      <div id="education" class="border-t border-slate-100 pt-6">
        <h3 class="text-sm font-bold text-primary-navy flex items-center gap-2 mb-4"><span class="w-7 h-7 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0 text-xs"><i class="fa-solid fa-graduation-cap"></i></span> Education</h3>
        <?php if ($education): ?>
          <div class="space-y-4">
            <?php foreach ($education as $edu): ?>
              <div>
                <p class="text-sm font-bold text-primary-navy"><?= e($edu['school']) ?></p>
                <p class="text-xs text-slate-500"><?= e(trim(($edu['degree'] ?? '') . (($edu['degree'] && $edu['field_of_study']) ? ', ' : '') . ($edu['field_of_study'] ?? ''))) ?></p>
                <p class="text-[0.68rem] text-slate-400 mt-0.5"><?= e($edu['start_year'] ?? '') ?><?= $edu['start_year'] || $edu['end_year'] ? ' – ' : '' ?><?= e($edu['end_year'] ?? '') ?></p>
              </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="text-xs text-slate-400">No education added yet.</p>
        <?php endif; ?>
      </div>
    </div>

    <!-- Sidebar -->
    <div class="space-y-4">
      <div id="contact-info" class="card p-5">
        <h3 class="text-sm font-bold text-primary-navy flex items-center gap-2 mb-4"><span class="w-8 h-8 rounded-full bg-[var(--menu-accent-100)] text-[var(--menu-accent-600)] flex items-center justify-center flex-shrink-0"><i class="fa-regular fa-user"></i></span> Contact Information</h3>
        <div class="space-y-3.5">
          <?php if (($isOwnerOrAdmin || $profile['show_phone']) && $profile['phone']): ?>
            <div class="flex items-start gap-2.5">
              <i class="fa-solid fa-phone text-slate-400 mt-0.5 w-4 text-center"></i>
              <div><p class="text-sm text-slate-700"><?= e($profile['phone']) ?></p><p class="text-[0.68rem] text-slate-400">Phone</p></div>
            </div>
          <?php endif; ?>
          <?php if ($isOwnerOrAdmin || $profile['show_email']): ?>
            <div class="flex items-start gap-2.5">
              <i class="fa-solid fa-envelope text-slate-400 mt-0.5 w-4 text-center"></i>
              <div><p class="text-sm text-slate-700 break-all"><?= e($profile['email']) ?></p><p class="text-[0.68rem] text-slate-400">Email</p></div>
            </div>
          <?php endif; ?>
          <?php if (location_display($profile)): ?>
            <div class="flex items-start gap-2.5">
              <i class="fa-solid fa-location-dot text-slate-400 mt-0.5 w-4 text-center"></i>
              <div><p class="text-sm text-slate-700"><?= e(location_display($profile)) ?></p><p class="text-[0.68rem] text-slate-400">Location</p></div>
            </div>
          <?php endif; ?>
          <?php if ($profile['linkedin_url']): ?>
            <div class="flex items-start gap-2.5">
              <i class="fa-brands fa-linkedin text-slate-400 mt-0.5 w-4 text-center"></i>
              <div><a href="<?= e($profile['linkedin_url']) ?>" target="_blank" rel="noopener noreferrer" class="text-sm text-sky-600 hover:underline">LinkedIn Profile <i class="fa-solid fa-arrow-up-right-from-square text-[0.6rem]"></i></a><p class="text-[0.68rem] text-slate-400">View profile</p></div>
            </div>
          <?php endif; ?>
          <?php if (!$isOwnerOrAdmin && !$profile['show_email'] && !(($profile['show_phone']) && $profile['phone']) && !location_display($profile) && !$profile['linkedin_url']): ?>
            <p class="text-xs text-slate-400">This alumnus hasn't shared contact details.</p>
          <?php endif; ?>
        </div>
      </div>

      <?php if ($showActions): ?>
        <div class="card p-5 bg-[var(--menu-accent-50)] border-[var(--menu-accent-100)]">
          <h3 class="text-sm font-bold text-primary-navy flex items-center gap-2 mb-2"><span class="w-8 h-8 rounded-full bg-white text-[var(--menu-accent-600)] flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-people-group"></i></span> In your network</h3>
          <p class="text-xs text-slate-500 mb-3">
            <?= $mutualCount > 0 ? 'You are connected with ' . $mutualCount . ' mutual alumn' . ($mutualCount === 1 ? 'us' : 'i') . '.' : "You don't have any mutual connections yet." ?>
          </p>
          <?php if ($mutualCount > 0): ?>
            <a href="<?= e(url('alumni/' . $profile['id'] . '/mutual-connections')) ?>" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !py-2 text-xs w-full text-center block">View Mutual Connections</a>
          <?php endif; ?>
        </div>

        <div class="card p-5">
          <h3 class="text-sm font-bold text-primary-navy flex items-center gap-2 mb-3"><span class="w-8 h-8 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0"><i class="fa-regular fa-id-badge"></i></span> Quick Actions</h3>
          <div class="divide-y divide-slate-100">
            <?php if ($connectionStatus !== null && $connectionStatus['status'] === 'accepted'): ?>
              <a href="<?= e(url('messages/' . $profile['id'])) ?>" class="flex items-center justify-between py-2.5 text-sm text-slate-700 hover:text-gold">
                <span><i class="fa-regular fa-envelope w-4 text-center text-slate-400"></i> Send a Message</span>
                <i class="fa-solid fa-chevron-right text-[0.65rem] text-slate-300"></i>
              </a>
            <?php else: ?>
              <span class="flex items-center justify-between py-2.5 text-sm text-slate-300 cursor-not-allowed" title="Connect first to send a message">
                <span><i class="fa-regular fa-envelope w-4 text-center"></i> Send a Message</span>
              </span>
            <?php endif; ?>

            <?php if ($upcomingEvents): ?>
              <button type="button" class="quick-action-toggle w-full flex items-center justify-between py-2.5 text-sm text-slate-700 hover:text-gold" data-target="invite-panel">
                <span><i class="fa-regular fa-calendar w-4 text-center text-slate-400"></i> Invite to Event</span>
                <i class="fa-solid fa-chevron-right text-[0.65rem] text-slate-300"></i>
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

            <button type="button" class="quick-action-toggle w-full flex items-center justify-between py-2.5 text-sm text-slate-700 hover:text-red-600" data-target="report-panel">
              <span><i class="fa-regular fa-flag w-4 text-center text-slate-400"></i> Report Profile</span>
              <i class="fa-solid fa-chevron-right text-[0.65rem] text-slate-300"></i>
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
    document.querySelectorAll('.profile-jump').forEach(function (link) {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        var target = document.getElementById(link.getAttribute('href').slice(1));
        if (!target) return;
        document.querySelectorAll('.profile-jump').forEach(function (l) {
          l.classList.toggle('text-gold', l === link);
          l.classList.toggle('border-gold', l === link);
          l.classList.toggle('text-slate-500', l !== link);
          l.classList.toggle('border-transparent', l !== link);
        });
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
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
