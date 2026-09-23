<?php
$experienceLabel = !empty($job['experience_level']) ? (\App\Models\Job::EXPERIENCE_LEVELS[$job['experience_level']] ?? null) : null;
?>
<div class="max-w-[1280px] mx-auto px-4 md:px-8 py-8">

  <div class="flex items-center gap-2 text-xs text-slate-400 mb-5 flex-wrap">
    <a href="<?= e(url('/')) ?>" class="hover:text-gold flex items-center gap-1.5"><i class="fa-solid fa-house"></i></a>
    <i class="fa-solid fa-chevron-right text-[0.6rem]"></i>
    <a href="<?= e(url('jobs')) ?>" class="hover:text-gold">Careers</a>
    <i class="fa-solid fa-chevron-right text-[0.6rem]"></i>
    <a href="<?= e(url('jobs')) ?>" class="hover:text-gold">Job Board</a>
    <i class="fa-solid fa-chevron-right text-[0.6rem]"></i>
    <span class="text-primary-navy font-semibold truncate max-w-[220px] sm:max-w-none"><?= e($job['title']) ?></span>
  </div>

  <?php if ($job['approval_status'] === 'pending'): ?>
    <div class="card p-4 mb-4 bg-amber-50 border-amber-100 flex items-start gap-3">
      <i class="fa-solid fa-hourglass-half text-amber-500 mt-0.5"></i>
      <p class="text-xs text-amber-800">This job post is awaiting admin approval and is only visible to you.</p>
    </div>
  <?php elseif ($job['approval_status'] === 'rejected'): ?>
    <div class="card p-4 mb-4 bg-red-50 border-red-100 flex items-start gap-3">
      <i class="fa-solid fa-circle-xmark text-red-500 mt-0.5"></i>
      <p class="text-xs text-red-800">This job post was not approved. Edit it from <a href="<?= e(url('jobs/mine')) ?>" class="underline font-semibold">My Job Posts</a> to resubmit.</p>
    </div>
  <?php endif; ?>

  <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6 items-start">

    <!-- Main -->
    <div class="card !p-0 overflow-hidden">
      <div class="p-6 md:p-8">
        <div class="flex flex-wrap items-start justify-between gap-5 mb-4">
          <div class="flex gap-4 min-w-0">
            <?= job_logo_html($job, 'w-16 h-16') ?>
            <div class="min-w-0">
              <?php if ($job['is_featured']): ?><span class="badge-blue mb-2 inline-block">FEATURED</span><?php endif; ?>
              <div class="flex items-center gap-2 flex-wrap">
                <h1 class="text-xl md:text-2xl font-extrabold text-primary-navy"><?= e($job['title']) ?></h1>
                <?php if ($isClosed): ?><span class="badge-blue !bg-slate-200 !text-slate-600">Closed</span><?php endif; ?>
              </div>
              <?php if (!empty($job['company_id'])): ?>
                <a href="<?= e(url('companies/' . $job['company_id'])) ?>" class="text-sm text-sky-600 hover:underline"><?= e($job['company']) ?></a>
              <?php else: ?>
                <p class="text-sm text-slate-500"><?= e($job['company']) ?></p>
              <?php endif; ?>
            </div>
          </div>
          <div class="flex flex-col gap-2 flex-shrink-0 w-full sm:w-auto">
            <?php if ($isClosed): ?>
              <button type="button" disabled class="btn-gold !px-5 !py-2.5 text-sm opacity-50 cursor-not-allowed flex items-center justify-center gap-2"><i class="fa-solid fa-paper-plane"></i> Applications Closed</button>
            <?php else: ?>
              <form method="POST" action="<?= e(url('jobs/' . $job['id'] . '/apply')) ?>">
                <?= csrf_field() ?>
                <button type="submit" class="btn-gold !px-5 !py-2.5 text-sm w-full flex items-center justify-center gap-2"><i class="fa-solid fa-paper-plane"></i> <?= $hasApplied ? 'Apply Again' : 'Apply Now' ?></button>
              </form>
            <?php endif; ?>
            <?php if (\App\Core\Auth::check()): ?>
              <form method="POST" action="<?= e(url('jobs/' . $job['id'] . '/save')) ?>">
                <?= csrf_field() ?>
                <button type="submit" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-5 !py-2.5 text-sm w-full flex items-center justify-center gap-2"><i class="<?= $isSaved ? 'fa-solid' : 'fa-regular' ?> fa-bookmark"></i> <?= $isSaved ? 'Saved' : 'Save Job' ?></button>
              </form>
            <?php endif; ?>
          </div>
        </div>

        <div class="flex flex-wrap gap-4 text-xs text-slate-500 mb-3">
          <span><i class="fa-solid fa-location-dot"></i> <?= e($job['location']) ?></span>
          <span><i class="fa-solid fa-briefcase"></i> <?= e($job['job_type']) ?></span>
          <span><i class="fa-regular fa-calendar"></i> Posted <?= e(time_ago($job['posted_at'])) ?></span>
          <?php if (!empty($job['closing_date'])): ?>
            <span><i class="fa-regular fa-calendar-xmark"></i> <?= $isClosed ? 'Closed' : 'Closes' ?> <?= e(format_date($job['closing_date'])) ?></span>
          <?php endif; ?>
          <?php if ($poster): ?>
            <span><i class="fa-solid fa-user"></i> Posted by <a href="<?= e(url('alumni/' . $poster['id'])) ?>" class="text-sky-600 hover:underline"><?= e($poster['name']) ?></a></span>
          <?php endif; ?>
        </div>

        <?php if ($categories): ?>
          <div class="flex flex-wrap gap-2">
            <?php foreach ($categories as $cat): $pc = palette_classes($cat['slug']); ?>
              <span class="rounded-full px-3 py-1 text-xs font-semibold <?= e($pc['bg']) ?> <?= e($pc['text']) ?>"><?= e($cat['name']) ?></span>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>

      <div class="flex items-center gap-6 px-6 md:px-8 border-t border-slate-100 overflow-x-auto overflow-y-hidden">
        <button type="button" class="job-tab-link flex items-center gap-2 text-sm font-semibold py-3.5 border-b-2 -mb-px whitespace-nowrap text-gold border-gold" data-tab="overview">Overview</button>
        <?php if (!empty($job['responsibilities'])): ?>
          <button type="button" class="job-tab-link flex items-center gap-2 text-sm font-semibold py-3.5 border-b-2 -mb-px whitespace-nowrap text-slate-500 border-transparent hover:text-primary-navy" data-tab="responsibilities">Responsibilities</button>
        <?php endif; ?>
        <?php if (!empty($job['requirements'])): ?>
          <button type="button" class="job-tab-link flex items-center gap-2 text-sm font-semibold py-3.5 border-b-2 -mb-px whitespace-nowrap text-slate-500 border-transparent hover:text-primary-navy" data-tab="requirements">Requirements</button>
        <?php endif; ?>
        <?php if (!empty($job['company_about'])): ?>
          <button type="button" class="job-tab-link flex items-center gap-2 text-sm font-semibold py-3.5 border-b-2 -mb-px whitespace-nowrap text-slate-500 border-transparent hover:text-primary-navy" data-tab="about">About <?= e($job['company']) ?></button>
        <?php endif; ?>
      </div>

      <div class="p-6 md:p-8 space-y-6">
        <div class="job-tab-panel" data-panel="role">
          <div class="flex items-start gap-3 mb-3">
            <div class="w-9 h-9 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center flex-shrink-0"><i class="fa-regular fa-file-lines"></i></div>
            <h3 class="text-base font-extrabold text-primary-navy pt-1.5">About the Role</h3>
          </div>
          <div class="prose prose-sm md:prose-base max-w-none prose-headings:text-primary-navy prose-a:text-gold"><?= $job['description'] ?></div>
        </div>

        <?php if (!empty($job['responsibilities'])): ?>
          <div class="job-tab-panel pt-6 border-t border-slate-100" data-panel="responsibilities">
            <div class="flex items-start gap-3 mb-3">
              <div class="w-9 h-9 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-bullseye"></i></div>
              <h3 class="text-base font-extrabold text-primary-navy pt-1.5">Key Responsibilities</h3>
            </div>
            <div class="prose prose-sm md:prose-base max-w-none prose-headings:text-primary-navy prose-a:text-gold prose-ul:list-disc marker:text-gold"><?= $job['responsibilities'] ?></div>
          </div>
        <?php endif; ?>

        <?php if (!empty($job['requirements'])): ?>
          <div class="job-tab-panel pt-6 border-t border-slate-100" data-panel="requirements">
            <div class="flex items-start gap-3 mb-3">
              <div class="w-9 h-9 rounded-lg bg-sky-50 text-sky-600 flex items-center justify-center flex-shrink-0"><i class="fa-regular fa-user"></i></div>
              <h3 class="text-base font-extrabold text-primary-navy pt-1.5">Requirements</h3>
            </div>
            <div class="prose prose-sm md:prose-base max-w-none prose-headings:text-primary-navy prose-a:text-gold prose-ul:list-disc marker:text-gold"><?= $job['requirements'] ?></div>
          </div>
        <?php endif; ?>

        <?php if (!empty($job['company_about'])): ?>
          <div class="job-tab-panel hidden pt-6 border-t border-slate-100" data-panel="about">
            <div class="flex items-start gap-4">
              <?= job_logo_html($job, 'w-12 h-12') ?>
              <div class="flex-1 min-w-0">
                <h3 class="text-base font-extrabold text-primary-navy mb-2">About <?= e($job['company']) ?></h3>
                <div class="prose prose-sm max-w-none prose-headings:text-primary-navy prose-a:text-gold"><?= $job['company_about'] ?></div>
                <?php if (!empty($companyWebsite)): ?>
                  <a href="<?= e($companyWebsite) ?>" target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-gold hover:underline mt-3 inline-flex items-center gap-1.5">Visit Company Website <i class="fa-solid fa-arrow-right"></i></a>
                <?php endif; ?>
              </div>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- Sidebar -->
    <aside class="space-y-6">
      <div class="card p-5 !bg-sky-50 !border-sky-100">
        <div class="flex items-start gap-3">
          <div class="w-11 h-11 rounded-full bg-white text-sky-600 flex items-center justify-center flex-shrink-0 text-lg"><i class="fa-solid fa-building"></i></div>
          <div>
            <h3 class="text-sm font-extrabold text-primary-navy mb-1">Build Your Future</h3>
            <p class="text-xs text-slate-600 leading-relaxed">Take the next step in your career with opportunities from leading companies.</p>
          </div>
        </div>
      </div>

      <div class="card p-5">
        <h3 class="text-sm font-extrabold text-primary-navy mb-4 flex items-center gap-2"><i class="fa-regular fa-clipboard text-gold"></i> Job Summary</h3>
        <div class="space-y-4">
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center flex-shrink-0"><i class="fa-regular fa-building"></i></div>
            <div>
              <p class="text-xs text-slate-400">Company</p>
              <p class="text-sm font-semibold text-primary-navy"><?= e($job['company']) ?></p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-briefcase"></i></div>
            <div>
              <p class="text-xs text-slate-400">Job Title</p>
              <p class="text-sm font-semibold text-primary-navy"><?= e($job['title']) ?></p>
            </div>
          </div>
          <?php if (!empty($job['location'])): ?>
            <div class="flex items-start gap-3">
              <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-location-dot"></i></div>
              <div>
                <p class="text-xs text-slate-400">Location</p>
                <p class="text-sm font-semibold text-primary-navy"><?= e($job['location']) ?></p>
              </div>
            </div>
          <?php endif; ?>
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center flex-shrink-0"><i class="fa-regular fa-clock"></i></div>
            <div>
              <p class="text-xs text-slate-400">Employment Type</p>
              <p class="text-sm font-semibold text-primary-navy"><?= e($job['job_type']) ?></p>
            </div>
          </div>
          <?php if ($experienceLabel): ?>
            <div class="flex items-start gap-3">
              <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-user-group"></i></div>
              <div>
                <p class="text-xs text-slate-400">Experience Level</p>
                <p class="text-sm font-semibold text-primary-navy"><?= e($experienceLabel) ?></p>
              </div>
            </div>
          <?php endif; ?>
          <div class="flex items-start gap-3">
            <div class="w-8 h-8 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center flex-shrink-0"><i class="fa-regular fa-calendar"></i></div>
            <div>
              <p class="text-xs text-slate-400">Posted</p>
              <p class="text-sm font-semibold text-primary-navy"><?= e(time_ago($job['posted_at'])) ?></p>
            </div>
          </div>
        </div>
      </div>

      <div class="card p-5">
        <h3 class="text-sm font-extrabold text-primary-navy mb-4 flex items-center gap-2"><i class="fa-solid fa-share-nodes text-gold"></i> Share This Job</h3>
        <div class="flex items-center gap-2.5">
          <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($jobUrl) ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-[#0a66c2] text-white flex items-center justify-center hover:opacity-85" title="Share on LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
          <a href="https://twitter.com/intent/tweet?url=<?= urlencode($jobUrl) ?>&text=<?= urlencode($job['title'] . ' at ' . $job['company']) ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-black text-white flex items-center justify-center hover:opacity-85" title="Share on X"><i class="fa-brands fa-x-twitter"></i></a>
          <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode($jobUrl) ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-[#1877f2] text-white flex items-center justify-center hover:opacity-85" title="Share on Facebook"><i class="fa-brands fa-facebook-f"></i></a>
          <a href="https://wa.me/?text=<?= urlencode($job['title'] . ' at ' . $job['company'] . ' ' . $jobUrl) ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full bg-[#25d366] text-white flex items-center justify-center hover:opacity-85" title="Share on WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
          <button type="button" id="copy-job-link" data-url="<?= e($jobUrl) ?>" class="w-9 h-9 rounded-full bg-slate-100 text-slate-500 flex items-center justify-center hover:bg-slate-200" title="Copy link"><i class="fa-solid fa-link"></i></button>
        </div>
        <p id="copy-job-link-msg" class="hidden text-xs text-emerald-600 mt-2">Link copied!</p>
      </div>

      <?php if (!empty($job['company_about'])): ?>
        <div class="card p-5">
          <div class="flex items-start gap-3 mb-2">
            <?= job_logo_html($job, 'w-11 h-11') ?>
            <h3 class="text-sm font-extrabold text-primary-navy pt-1.5">About <?= e($job['company']) ?></h3>
          </div>
          <div class="prose prose-xs max-w-none text-slate-500 line-clamp-4 mb-3"><?= $job['company_about'] ?></div>
          <?php if (!empty($companyWebsite)): ?>
            <a href="<?= e($companyWebsite) ?>" target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-gold hover:underline inline-flex items-center gap-1.5">Visit Company Website <i class="fa-solid fa-arrow-right"></i></a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </aside>
  </div>
</div>

<script>
  (function () {
    var tabLinks = document.querySelectorAll('.job-tab-link');
    var panels = document.querySelectorAll('.job-tab-panel');

    function activate(tab) {
      tabLinks.forEach(function (b) {
        var isActive = b.dataset.tab === tab;
        b.classList.toggle('text-gold', isActive);
        b.classList.toggle('border-gold', isActive);
        b.classList.toggle('text-slate-500', !isActive);
        b.classList.toggle('border-transparent', !isActive);
      });
      panels.forEach(function (panel) {
        var show = tab === 'overview' ? panel.dataset.panel !== 'about' : panel.dataset.panel === tab;
        panel.classList.toggle('hidden', !show);
      });
    }

    tabLinks.forEach(function (btn) {
      btn.addEventListener('click', function () { activate(btn.dataset.tab); });
    });

    var copyBtn = document.getElementById('copy-job-link');
    var copyMsg = document.getElementById('copy-job-link-msg');
    if (copyBtn) {
      copyBtn.addEventListener('click', function () {
        var url = copyBtn.dataset.url;
        var done = function () {
          if (!copyMsg) return;
          copyMsg.classList.remove('hidden');
          setTimeout(function () { copyMsg.classList.add('hidden'); }, 2000);
        };
        if (navigator.clipboard && navigator.clipboard.writeText) {
          navigator.clipboard.writeText(url).then(done).catch(function () {});
        } else {
          var input = document.createElement('input');
          input.value = url;
          document.body.appendChild(input);
          input.select();
          document.execCommand('copy');
          document.body.removeChild(input);
          done();
        }
      });
    }
  })();
</script>
