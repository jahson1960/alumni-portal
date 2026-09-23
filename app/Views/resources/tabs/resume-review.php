<?php
$statusMeta = [
    'pending' => ['label' => 'Under Review', 'class' => 'bg-amber-100 text-amber-700'],
    'claimed' => ['label' => 'In Progress', 'class' => 'bg-sky-100 text-sky-700'],
    'completed' => ['label' => 'Completed', 'class' => 'bg-emerald-100 text-emerald-700'],
];
$isMentor = isset($pendingQueue, $myClaims);
?>
<div class="flex flex-wrap items-start justify-between gap-4 mb-6">
  <div>
    <h1 class="text-2xl font-extrabold text-primary-navy">Resume Review</h1>
    <p class="text-sm text-slate-500 mt-1">Get your resume reviewed by industry experts and receive actionable feedback.</p>
  </div>
</div>

<?php require __DIR__ . '/../partials/tab_switcher.php'; ?>

<div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6 items-start">
  <div>
    <?php if ($isMentor): ?>
      <div class="card p-5 mb-6">
        <h3 class="text-sm font-bold text-primary-navy mb-3">Resume Reviews Awaiting a Reviewer</h3>
        <div class="space-y-3">
          <?php foreach ($pendingQueue as $req): ?>
            <div class="border border-slate-200 rounded-lg p-3.5 flex flex-wrap items-center gap-3">
              <?= avatar_html(['name' => $req['requester_name'], 'avatar' => $req['requester_avatar']], 'w-9 h-9') ?>
              <div class="min-w-0 flex-1">
                <p class="text-sm font-bold text-primary-navy"><?= e($req['requester_name']) ?></p>
                <p class="text-xs text-slate-500 truncate"><?= e($req['requester_headline'] ?: '') ?></p>
                <?php if (!empty($req['message'])): ?><p class="text-xs text-slate-500 mt-1 italic">&ldquo;<?= e($req['message']) ?>&rdquo;</p><?php endif; ?>
                <p class="text-[0.65rem] text-slate-400 mt-1">Submitted <?= e(time_ago($req['created_at'])) ?></p>
              </div>
              <div class="flex items-center gap-3 flex-shrink-0">
                <?php if (!empty($req['resume_url'])): ?><a href="<?= e($req['resume_url']) ?>" target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-sky-600 hover:underline">View CV</a><?php endif; ?>
                <?php if (!empty($req['resume_link'])): ?><a href="<?= e($req['resume_link']) ?>" target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-sky-600 hover:underline">View Link</a><?php endif; ?>
              </div>
              <form method="POST" action="<?= e(url('resources/resume-review/' . $req['id'] . '/claim')) ?>" class="flex-shrink-0">
                <?= csrf_field() ?>
                <button type="submit" class="btn-gold !px-3 !py-1.5 text-xs">Claim This Review</button>
              </form>
            </div>
          <?php endforeach; ?>
          <?php if (empty($pendingQueue)): ?>
            <p class="text-sm text-slate-400">No pending requests right now.</p>
          <?php endif; ?>
        </div>
      </div>

      <div class="card p-5 mb-6">
        <h3 class="text-sm font-bold text-primary-navy mb-3">Reviews You've Claimed</h3>
        <div class="space-y-3">
          <?php foreach ($myClaims as $req): ?>
            <div class="border border-slate-200 rounded-lg p-3.5">
              <div class="flex flex-wrap items-center gap-3">
                <?= avatar_html(['name' => $req['requester_name'], 'avatar' => $req['requester_avatar']], 'w-9 h-9') ?>
                <div class="min-w-0 flex-1">
                  <p class="text-sm font-bold text-primary-navy"><?= e($req['requester_name']) ?></p>
                  <p class="text-[0.65rem] text-slate-400">Claimed <?= e(time_ago($req['claimed_at'])) ?></p>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0">
                  <?php if (!empty($req['resume_url'])): ?><a href="<?= e($req['resume_url']) ?>" target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-sky-600 hover:underline">View CV</a><?php endif; ?>
                  <?php if (!empty($req['resume_link'])): ?><a href="<?= e($req['resume_link']) ?>" target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-sky-600 hover:underline">View Link</a><?php endif; ?>
                </div>
              </div>
              <form method="POST" action="<?= e(url('resources/resume-review/' . $req['id'] . '/feedback')) ?>" class="mt-3 flex gap-2">
                <?= csrf_field() ?>
                <textarea name="feedback" rows="2" class="form-textarea text-xs flex-1" placeholder="Write your feedback..." required></textarea>
                <button type="submit" class="btn-gold !px-3 !py-1.5 text-xs flex-shrink-0 self-end">Submit Feedback</button>
              </form>
            </div>
          <?php endforeach; ?>
          <?php if (empty($myClaims)): ?>
            <p class="text-sm text-slate-400">You haven't claimed any reviews yet.</p>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>

    <div class="card p-5 mb-6">
      <h3 class="text-sm font-bold text-primary-navy mb-4">How it works</h3>
      <div class="space-y-4">
        <div class="flex items-start gap-3">
          <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-cloud-arrow-up"></i></div>
          <div>
            <p class="text-sm font-bold text-primary-navy">1. Upload Resume</p>
            <p class="text-xs text-slate-500 mt-0.5">Add a CV file or resume link to your profile.</p>
          </div>
        </div>
        <div class="flex items-start gap-3">
          <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-user-check"></i></div>
          <div>
            <p class="text-sm font-bold text-primary-navy">2. Expert Review</p>
            <p class="text-xs text-slate-500 mt-0.5">Our alumni mentors will review and provide feedback.</p>
          </div>
        </div>
        <div class="flex items-start gap-3">
          <div class="w-9 h-9 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-arrow-trend-up"></i></div>
          <div>
            <p class="text-sm font-bold text-primary-navy">3. Get Feedback</p>
            <p class="text-xs text-slate-500 mt-0.5">Receive actionable insights to improve your resume.</p>
          </div>
        </div>
      </div>

      <?php if (empty($resumeFileUrl) && empty($resumeLinkUrl)): ?>
        <a href="<?= e(url('profile/edit')) ?>#resume" class="btn !bg-indigo-600 !text-white hover:!bg-indigo-700 w-full mt-5 flex items-center justify-center gap-2"><i class="fa-solid fa-cloud-arrow-up"></i> Add Your Resume / CV</a>
        <p class="text-xs text-slate-400 mt-2 text-center">You don't have a resume or CV on file yet. Upload a file or add a link to your profile first, then come back to request a review.</p>
      <?php else: ?>
        <form method="POST" action="<?= e(url('resources/resume-review')) ?>" class="mt-5" id="resume-review-form" enctype="multipart/form-data">
          <?= csrf_field() ?>
          <textarea name="message" rows="2" class="form-textarea text-sm mb-3" placeholder="Anything specific you'd like feedback on? (optional)"></textarea>
          <button type="button" id="open-resume-review-modal" class="btn !bg-indigo-600 !text-white hover:!bg-indigo-700 w-full flex items-center justify-center gap-2"><i class="fa-solid fa-cloud-arrow-up"></i> Upload Your Resume</button>

          <div id="resume-review-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/50 p-4">
            <div class="bg-white rounded-xl shadow-2xl w-full max-w-md max-h-[90vh] flex flex-col overflow-hidden">
              <div class="flex items-center justify-between px-5 py-3.5 border-b border-slate-100 flex-shrink-0">
                <h3 class="text-sm font-bold text-primary-navy">Submit for Review</h3>
                <button type="button" id="resume-review-modal-close" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
              </div>
              <div class="p-5 overflow-y-auto">
                <p class="text-sm text-slate-600 mb-4">Select what you'd like our alumni mentors to review. It'll be shared with whoever claims your request.</p>

                <?php $bothPresent = $resumeFileUrl && $resumeLinkUrl; ?>

                <?php if ($resumeFileUrl): ?>
                  <div class="border border-slate-200 rounded-lg p-3.5 mb-3">
                    <div class="flex items-start gap-3">
                      <?php if ($bothPresent): ?>
                        <input type="checkbox" id="include-file-checkbox" name="include_file" value="1" checked class="mt-1 rounded border-slate-300 text-gold focus:ring-gold flex-shrink-0">
                      <?php else: ?>
                        <input type="checkbox" id="include-file-checkbox" checked disabled class="mt-1 rounded border-slate-300 text-gold flex-shrink-0 opacity-60">
                        <input type="hidden" name="include_file" value="1">
                      <?php endif; ?>
                      <label for="include-file-checkbox" class="flex items-center gap-2.5 flex-1 min-w-0 <?= $bothPresent ? 'cursor-pointer' : '' ?>">
                        <span class="w-8 h-8 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0"><i class="fa-regular fa-file-lines"></i></span>
                        <span class="min-w-0">
                          <span class="block text-xs font-bold text-primary-navy">Your <?= e($resumeFileExt) ?> CV</span>
                          <span class="block text-[0.68rem] text-slate-400">This is the CV currently on your profile<?= $bothPresent ? '' : ' — always included' ?>.</span>
                        </span>
                      </label>
                      <a href="<?= e($resumeFileUrl) ?>" target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-sky-600 hover:underline flex-shrink-0">View</a>
                    </div>
                    <div class="mt-3 pl-8">
                      <label for="resume_review_file" class="text-xs font-semibold text-slate-500 hover:text-gold cursor-pointer"><i class="fa-solid fa-rotate"></i> Choose a different file from your computer</label>
                      <input type="file" id="resume_review_file" name="resume_review_file" accept=".pdf,.doc,.docx" class="form-input mt-1.5 !py-1.5 text-xs">
                      <p class="text-[0.68rem] text-slate-400 mt-1">This only affects this submission. To change your default CV, <a href="<?= e(url('profile/edit')) ?>#resume" class="text-gold hover:underline">update your profile</a>.</p>
                    </div>
                  </div>
                <?php endif; ?>

                <?php if ($resumeLinkUrl): ?>
                  <div class="border border-slate-200 rounded-lg p-3.5 mb-1">
                    <div class="flex items-start gap-3">
                      <?php if ($bothPresent): ?>
                        <input type="checkbox" id="include-link-checkbox" name="include_link" value="1" checked class="mt-1 rounded border-slate-300 text-gold focus:ring-gold flex-shrink-0">
                      <?php else: ?>
                        <input type="checkbox" id="include-link-checkbox" checked disabled class="mt-1 rounded border-slate-300 text-gold flex-shrink-0 opacity-60">
                        <input type="hidden" name="include_link" value="1">
                      <?php endif; ?>
                      <label for="include-link-checkbox" class="flex items-center gap-2.5 flex-1 min-w-0 <?= $bothPresent ? 'cursor-pointer' : '' ?>">
                        <span class="w-8 h-8 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-link"></i></span>
                        <span class="min-w-0">
                          <span class="block text-xs font-bold text-primary-navy">Your resume link</span>
                          <span class="block text-[0.68rem] text-slate-400 truncate">This link will<?= $bothPresent ? ' also' : '' ?> be submitted&colon; <?= e($resumeLinkUrl) ?></span>
                        </span>
                      </label>
                      <a href="<?= e($resumeLinkUrl) ?>" target="_blank" rel="noopener noreferrer" class="text-xs font-semibold text-sky-600 hover:underline flex-shrink-0">Open</a>
                    </div>
                  </div>
                <?php endif; ?>

                <p id="resume-review-modal-error" class="hidden text-xs text-red-600 mt-2">Select at least one item to submit.</p>
              </div>
              <div class="flex items-center gap-3 px-5 py-4 border-t border-slate-100 flex-shrink-0">
                <button type="button" id="resume-review-modal-cancel" class="btn !bg-slate-100 !text-slate-600 hover:!bg-slate-200 flex-1">Cancel</button>
                <button type="button" id="resume-review-modal-confirm" class="btn-gold flex-1">Confirm &amp; Submit</button>
              </div>
            </div>
          </div>
        </form>

        <script>
          (function () {
            var trigger = document.getElementById('open-resume-review-modal');
            var modal = document.getElementById('resume-review-modal');
            var closeBtn = document.getElementById('resume-review-modal-close');
            var cancelBtn = document.getElementById('resume-review-modal-cancel');
            var confirmBtn = document.getElementById('resume-review-modal-confirm');
            var errorMsg = document.getElementById('resume-review-modal-error');
            var form = document.getElementById('resume-review-form');
            if (!trigger || !modal || !form) return;

            function openModal() {
              if (errorMsg) errorMsg.classList.add('hidden');
              modal.classList.remove('hidden');
              modal.classList.add('flex');
              document.body.style.overflow = 'hidden';
            }
            function closeModal() {
              modal.classList.add('hidden');
              modal.classList.remove('flex');
              document.body.style.overflow = '';
            }

            trigger.addEventListener('click', openModal);
            closeBtn.addEventListener('click', closeModal);
            cancelBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', function (e) { if (e.target === modal) closeModal(); });
            document.addEventListener('keydown', function (e) {
              if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
            });

            confirmBtn.addEventListener('click', function () {
              var anyChecked = Array.prototype.some.call(modal.querySelectorAll('input[type="checkbox"]'), function (cb) { return cb.checked; });
              if (!anyChecked) {
                if (errorMsg) errorMsg.classList.remove('hidden');
                return;
              }
              form.submit();
            });
          })();
        </script>
      <?php endif; ?>
    </div>

    <div class="card p-5">
      <h3 class="text-sm font-bold text-primary-navy mb-3">Your Reviews</h3>
      <div class="space-y-3">
        <?php foreach ($myReviews as $rev): $meta = $statusMeta[$rev['status']]; ?>
          <div class="border border-slate-200 rounded-lg p-3.5">
            <div class="flex items-start justify-between gap-3">
              <div class="min-w-0">
                <p class="text-sm font-bold text-primary-navy">Resume Review Request</p>
                <?php if ($rev['reviewer_name']): ?>
                  <p class="text-xs text-slate-500">Reviewed by <?= e($rev['reviewer_name']) ?><?= $rev['reviewer_headline'] ? ' &middot; ' . e($rev['reviewer_headline']) : '' ?></p>
                <?php endif; ?>
              </div>
              <span class="badge <?= e($meta['class']) ?> flex-shrink-0"><?= e($meta['label']) ?></span>
            </div>
            <p class="text-[0.65rem] text-slate-400 mt-2">Submitted <?= e(time_ago($rev['created_at'])) ?></p>
            <?php if ($rev['status'] === 'completed' && $rev['feedback']): ?>
              <div class="bg-slate-50 rounded-lg p-3 mt-3">
                <p class="text-xs font-bold text-primary-navy mb-1">Feedback</p>
                <p class="text-xs text-slate-600 whitespace-pre-line"><?= e($rev['feedback']) ?></p>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
        <?php if (empty($myReviews)): ?>
          <p class="text-sm text-slate-400">You haven't requested a resume review yet.</p>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <aside class="card p-4 !bg-indigo-50 !border-indigo-100">
    <div class="flex items-center gap-2 mb-1.5">
      <i class="fa-solid fa-lightbulb text-indigo-500"></i>
      <h3 class="text-sm font-bold text-primary-navy">Resume Tips</h3>
    </div>
    <p class="text-xs font-bold text-primary-navy mt-2">Tailor your resume</p>
    <p class="text-xs text-slate-600 mt-1">Customize your resume for each job application by highlighting relevant skills and experiences.</p>
  </aside>
</div>
