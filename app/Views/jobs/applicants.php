<?php
$statusMeta = [
    'new' => ['label' => 'Applied', 'class' => 'bg-sky-100 text-sky-700'],
    'reviewed' => ['label' => 'Reviewed', 'class' => 'bg-amber-100 text-amber-700'],
    'shortlisted' => ['label' => 'Shortlisted', 'class' => 'bg-emerald-100 text-emerald-700'],
    'interview_scheduled' => ['label' => 'Interview Scheduled', 'class' => 'bg-violet-100 text-violet-700'],
    'rejected' => ['label' => 'Rejected', 'class' => 'bg-red-100 text-red-700'],
];
$experienceLabels = \App\Models\Job::EXPERIENCE_LEVELS;
$educationLabels = ['bachelors' => "Bachelor's", 'masters' => "Master's", 'phd' => 'PhD', 'other' => 'Other', 'none' => 'Not listed'];
$matchCategoryLabels = [
    'education' => 'Education',
    'experience' => 'Experience',
    'skills' => 'Skills & Expertise',
    'interests' => 'Interests',
    'contact_location' => 'Contact & Location',
];
$isClosed = \App\Models\Job::isClosed($job);
$statusCounts = array_fill_keys(array_keys($statusMeta), 0);
foreach ($applicants as $a) {
    $statusCounts[$a['status']] = ($statusCounts[$a['status']] ?? 0) + 1;
}
?>
<div class="max-w-[1280px] mx-auto px-4 md:px-8 py-8">
  <a href="<?= e(url('jobs/mine')) ?>" class="text-sm text-slate-500 hover:text-gold mb-4 inline-flex items-center gap-2"><i class="fa-solid fa-arrow-left"></i> Back to Jobs</a>

  <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
      <h1 class="text-xl font-extrabold text-primary-navy flex items-center gap-2.5"><i class="fa-solid fa-users text-primary-navy"></i> Applicants</h1>
      <p class="text-sm text-slate-500 mt-1"><?= e($job['title']) ?> &bull; <?= count($applicants) ?> Applicant<?= count($applicants) === 1 ? '' : 's' ?></p>
    </div>
    <?php if ($applicants): ?>
      <button type="button" id="broadcast-open-btn" class="btn-gold !px-4 !py-2.5 text-sm flex items-center gap-2"><i class="fa-solid fa-bullhorn"></i> Broadcast Message</button>
    <?php endif; ?>
  </div>

  <!-- Job summary -->
  <div class="card p-5 mb-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
      <div class="flex gap-3 min-w-0">
        <?= job_logo_html($job, 'w-12 h-12 flex-shrink-0 !rounded-xl') ?>
        <div class="min-w-0">
          <h2 class="text-base font-bold text-primary-navy truncate"><?= e($job['title']) ?><?= $isClosed ? ' <span class="badge-blue !bg-slate-200 !text-slate-600 ml-1">Closed</span>' : '' ?></h2>
          <p class="text-xs text-slate-500 mt-0.5 flex flex-wrap items-center gap-x-3 gap-y-1">
            <span><i class="fa-solid fa-building text-slate-400"></i> <?= e($job['company']) ?></span>
            <?php if ($job['location']): ?><span><i class="fa-solid fa-location-dot text-slate-400"></i> <?= e($job['location']) ?></span><?php endif; ?>
            <?php if ($job['job_type']): ?><span><i class="fa-solid fa-briefcase text-slate-400"></i> <?= e($job['job_type']) ?></span><?php endif; ?>
          </p>
          <?php if ($job['description']): ?>
            <p class="text-xs text-slate-500 mt-2 line-clamp-2 max-w-xl"><?= e(mb_strimwidth(strip_tags($job['description']), 0, 220, '...')) ?></p>
          <?php endif; ?>
        </div>
      </div>
      <div class="flex items-center gap-2 flex-shrink-0">
        <a href="<?= e(url('jobs/' . $job['id'])) ?>" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-3.5 !py-2 text-xs"><i class="fa-regular fa-comment-dots"></i> View Job Details</a>
        <a href="<?= e(url('jobs/mine/' . $job['id'] . '/edit')) ?>" class="btn-gold !bg-primary-navy hover:!bg-primary-navy/90 !px-3.5 !py-2 text-xs"><i class="fa-solid fa-gear"></i> Manage Job</a>
      </div>
    </div>
  </div>

  <!-- Search + Filters -->
  <div class="flex flex-wrap items-center gap-3 mb-5">
    <div class="relative flex-1 min-w-[200px]">
      <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
      <input type="text" id="app-search" placeholder="Search by name, email or skills..." class="form-input !pl-10">
    </div>
    <select id="app-status" class="form-input !py-2.5 text-sm max-w-[170px]">
      <option value="all">All Statuses</option>
      <?php foreach ($statusMeta as $key => $m): ?>
        <option value="<?= e($key) ?>"><?= e($m['label']) ?></option>
      <?php endforeach; ?>
    </select>
    <select id="app-experience" class="form-input !py-2.5 text-sm max-w-[190px]">
      <option value="all">All Experience Levels</option>
      <?php foreach ($experienceLabels as $key => $label): ?>
        <option value="<?= e($key) ?>"><?= e($label) ?></option>
      <?php endforeach; ?>
    </select>
    <select id="app-education" class="form-input !py-2.5 text-sm max-w-[180px]">
      <option value="all">All Education Levels</option>
      <?php foreach ($educationLabels as $key => $label): ?>
        <option value="<?= e($key) ?>"><?= e($label) ?></option>
      <?php endforeach; ?>
    </select>
    <select id="app-sort" class="form-input !py-2.5 text-sm max-w-[190px]">
      <option value="recent">Sort by: Most Recent</option>
      <option value="oldest">Sort by: Oldest</option>
      <option value="match">Sort by: Best Match</option>
      <option value="name">Sort by: Name (A&ndash;Z)</option>
    </select>
  </div>

  <div class="space-y-4" id="applicant-list">
    <?php foreach ($applicants as $applicant):
        $meta = $statusMeta[$applicant['status']] ?? $statusMeta['new'];
        $searchBlob = mb_strtolower($applicant['applicant_name'] . ' ' . $applicant['applicant_email'] . ' ' . implode(' ', $applicant['skills_list']));
        $isNew = (time() - strtotime($applicant['created_at'])) < 86400;
        $ed = $applicant['education_summary'];
        $exp = $applicant['experience_summary'];
        if ($exp) {
            $expStart = $exp['start_date'] ? date('Y', strtotime($exp['start_date'])) : '';
            $expEnd = $exp['is_current'] ? 'Present' : ($exp['end_date'] ? date('Y', strtotime($exp['end_date'])) : '');
        }
    ?>
      <div class="applicant-card card p-5" data-status="<?= e($applicant['status']) ?>" data-experience="<?= e($applicant['experience_level']) ?>" data-education="<?= e($applicant['education_level']) ?>" data-search="<?= e($searchBlob) ?>" data-created="<?= e($applicant['created_at']) ?>" data-match="<?= (int) $applicant['match_percent'] ?>" data-name="<?= e(mb_strtolower($applicant['applicant_name'])) ?>">
        <div class="grid grid-cols-1 lg:grid-cols-[1.3fr_1.3fr_200px_180px] gap-5 items-start">

          <!-- Identity + contact -->
          <div class="flex gap-3 min-w-0">
            <a href="<?= e(url('alumni/' . $applicant['user_id'])) ?>" class="flex-shrink-0">
              <?= avatar_html(['name' => $applicant['applicant_name'], 'avatar' => $applicant['applicant_avatar']], 'w-14 h-14') ?>
            </a>
            <div class="min-w-0">
              <div class="flex items-center gap-2 flex-wrap">
                <a href="<?= e(url('alumni/' . $applicant['user_id'])) ?>" class="text-sm font-bold text-primary-navy hover:text-gold truncate"><?= e($applicant['applicant_name']) ?></a>
                <?php if ($isNew): ?><span class="badge !bg-sky-100 !text-sky-600 uppercase text-[0.6rem]">New</span><?php endif; ?>
              </div>
              <p class="text-[0.68rem] text-slate-400 mb-2"><?= e(time_ago($applicant['created_at'])) ?></p>

              <div class="flex flex-col gap-1.5 text-xs text-slate-500">
                <?php if ($applicant['applicant_city'] || $applicant['applicant_country']): ?>
                  <span><i class="fa-solid fa-location-dot w-3.5 text-slate-400"></i> <?= e(trim(($applicant['applicant_city'] ?: '') . (($applicant['applicant_city'] && $applicant['applicant_country']) ? ', ' : '') . ($applicant['applicant_country'] ?: ''))) ?></span>
                <?php endif; ?>
                <?php if ($applicant['applicant_show_email']): ?>
                  <span class="truncate"><i class="fa-regular fa-envelope w-3.5 text-slate-400"></i> <?= e($applicant['applicant_email']) ?></span>
                <?php endif; ?>
                <?php if ($applicant['applicant_show_phone'] && $applicant['applicant_phone']): ?>
                  <span><i class="fa-solid fa-phone w-3.5 text-slate-400"></i> <?= e($applicant['applicant_phone']) ?></span>
                <?php endif; ?>
                <?php if ($applicant['applicant_linkedin']): ?>
                  <a href="<?= e($applicant['applicant_linkedin']) ?>" target="_blank" rel="noopener noreferrer" class="text-sky-600 hover:underline"><i class="fa-brands fa-linkedin w-3.5"></i> LinkedIn Profile</a>
                <?php endif; ?>
              </div>
            </div>
          </div>

          <!-- Education / Experience / Skills -->
          <div class="min-w-0 space-y-3.5">
            <?php if ($ed): ?>
              <div>
                <p class="text-xs font-bold text-slate-700 flex items-center gap-1.5 mb-1"><i class="fa-solid fa-graduation-cap text-slate-400"></i> Education</p>
                <p class="text-sm font-semibold text-primary-navy"><?= e($ed['degree'] ?: 'Studied') ?><?= $ed['field_of_study'] ? ', ' . e($ed['field_of_study']) : '' ?></p>
                <p class="text-xs text-slate-500"><?= e($ed['school']) ?><?= ($ed['start_year'] || $ed['end_year']) ? ', ' . e((string) ($ed['start_year'] ?: '')) . ' &ndash; ' . e((string) ($ed['end_year'] ?: '')) : '' ?></p>
              </div>
            <?php endif; ?>
            <?php if ($exp): ?>
              <div>
                <p class="text-xs font-bold text-slate-700 flex items-center gap-1.5 mb-1"><i class="fa-solid fa-briefcase text-slate-400"></i> Experience</p>
                <p class="text-sm font-semibold text-primary-navy"><?= e($applicant['total_years']) ?> year<?= $applicant['total_years'] == 1 ? '' : 's' ?></p>
                <p class="text-xs text-slate-500"><?= e($exp['title']) ?><?= $exp['company'] ? ' &bull; ' . e($exp['company']) : '' ?><?= ($expStart || $expEnd) ? ' (' . e($expStart) . ' &ndash; ' . e($expEnd) . ')' : '' ?></p>
              </div>
            <?php endif; ?>
            <?php if ($applicant['skills_list']): ?>
              <div>
                <p class="text-xs font-bold text-slate-700 flex items-center gap-1.5 mb-1.5"><i class="fa-solid fa-tags text-slate-400"></i> Skills</p>
                <div class="flex flex-wrap gap-1.5">
                  <?php foreach ($applicant['skills_list'] as $skill): ?>
                    <span class="bg-sky-50 text-sky-700 rounded-md px-2 py-1 text-[0.68rem] font-medium"><?= e($skill) ?></span>
                  <?php endforeach; ?>
                </div>
              </div>
            <?php endif; ?>
            <?php if ($applicant['responses'] || $applicant['applicant_resume_file'] || $applicant['applicant_resume_url']): ?>
              <div class="flex flex-wrap gap-x-4 gap-y-1 pt-1">
                <?php if ($applicant['responses']): ?>
                  <button type="button" class="view-responses-btn text-xs text-gold hover:underline font-semibold">View Submitted Answers</button>
                <?php endif; ?>
                <?php if ($applicant['applicant_resume_file'] || $applicant['applicant_resume_url']): ?>
                  <a href="<?= e($applicant['applicant_resume_file'] ? upload_url($applicant['applicant_resume_file']) : $applicant['applicant_resume_url']) ?>" target="_blank" rel="noopener noreferrer" class="text-xs text-gold hover:underline font-semibold">View Resume</a>
                <?php endif; ?>
              </div>
            <?php endif; ?>
          </div>

          <!-- Match score -->
          <div class="min-w-0">
            <div class="flex items-center gap-2.5">
              <div class="relative w-14 h-14 flex-shrink-0">
                <svg viewBox="0 0 36 36" class="w-14 h-14 -rotate-90">
                  <circle cx="18" cy="18" r="15.5" fill="none" stroke="#e2e8f0" stroke-width="3"></circle>
                  <circle cx="18" cy="18" r="15.5" fill="none" stroke="<?= $applicant['match_percent'] >= 80 ? '#10b981' : ($applicant['match_percent'] >= 50 ? '#f59e0b' : '#ef4444') ?>" stroke-width="3" stroke-dasharray="97.4" stroke-dashoffset="<?= round(97.4 * (1 - $applicant['match_percent'] / 100), 2) ?>" stroke-linecap="round"></circle>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                  <span class="text-xs font-extrabold text-primary-navy"><?= (int) $applicant['match_percent'] ?>%</span>
                </div>
              </div>
              <p class="text-xs font-bold text-sky-700">Profile Match</p>
            </div>
            <div class="space-y-1.5 mt-3">
              <?php foreach ($matchCategoryLabels as $catKey => $catLabel): $met = $applicant['match_categories'][$catKey] ?? false; ?>
                <div class="flex items-center gap-1.5 text-[0.7rem] <?= $met ? 'text-slate-600' : 'text-amber-600' ?>">
                  <i class="<?= $met ? 'fa-solid fa-circle-check text-emerald-500' : 'fa-solid fa-triangle-exclamation text-amber-500' ?> flex-shrink-0"></i>
                  <span class="truncate"><?= e($catLabel) ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Status + actions -->
          <div class="min-w-0 space-y-2">
            <div>
              <form method="POST" action="<?= e(url('jobs/' . $job['id'] . '/applicants/' . $applicant['id'] . '/status')) ?>" class="status-form">
                <?= csrf_field() ?>
                <select name="status" class="status-select w-full !rounded-full !border-0 !py-1.5 !pl-3 !pr-7 text-xs font-bold <?= e($meta['class']) ?>">
                  <?php foreach ($statusMeta as $key => $m): ?>
                    <option value="<?= e($key) ?>" <?= $applicant['status'] === $key ? 'selected' : '' ?>><?= e($m['label']) ?></option>
                  <?php endforeach; ?>
                </select>
              </form>
              <p class="status-indicator text-[0.65rem] mt-1 h-3.5 flex items-center gap-1 opacity-0 transition-opacity duration-300"></p>
            </div>
            <a href="<?= e(url('alumni/' . $applicant['user_id'])) ?>" class="btn !bg-primary-navy !text-white hover:!bg-primary-navy/90 !py-2 text-xs w-full flex items-center justify-center gap-1.5"><i class="fa-regular fa-eye"></i> View Profile</a>
            <a href="<?= e(url('messages/' . $applicant['user_id'])) ?>" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !py-2 text-xs w-full flex items-center justify-center gap-1.5"><i class="fa-regular fa-envelope"></i> Send Message</a>
          </div>
        </div>

        <?php if ($applicant['responses']): ?>
          <div class="applicant-responses hidden mt-4 pt-4 border-t border-slate-100 space-y-5">
            <?php
              $responsesBySection = array_fill_keys(array_keys(\App\Models\ApplicationTemplate::SECTIONS), []);
              foreach ($applicant['responses'] as $response) {
                  $responsesBySection[$response['section'] ?? 'profile_info'][] = $response;
              }
            ?>
            <?php foreach (\App\Models\ApplicationTemplate::SECTIONS as $sectionKey => $sectionLabel): ?>
              <?php if (!empty($responsesBySection[$sectionKey])): ?>
                <div class="space-y-3">
                  <p class="text-[0.65rem] font-extrabold text-slate-400 uppercase tracking-wide"><?= e($sectionLabel) ?></p>
                  <?php foreach ($responsesBySection[$sectionKey] as $response): ?>
                    <div>
                      <p class="text-xs font-semibold text-slate-500"><?= e($response['label']) ?></p>
                      <?php if ($response['type'] === 'file'): ?>
                        <a href="<?= e($response['value']) ?>" target="_blank" rel="noopener noreferrer" class="text-sm text-gold hover:underline inline-flex items-center gap-1.5"><i class="fa-regular fa-file-lines"></i> View file</a>
                      <?php elseif ($response['type'] === 'url'): ?>
                        <a href="<?= e($response['value']) ?>" target="_blank" rel="noopener noreferrer" class="text-sm text-gold hover:underline break-all"><?= e($response['value']) ?></a>
                      <?php else: ?>
                        <p class="text-sm text-slate-700 whitespace-pre-line"><?= e($response['value']) ?></p>
                      <?php endif; ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>
    <?php if (empty($applicants)): ?>
      <p class="text-sm text-slate-400">No one has applied yet.</p>
    <?php endif; ?>
  </div>

  <p id="app-empty" class="hidden text-sm text-slate-400 py-6 text-center">No applicants match those filters.</p>
  <p id="app-count" class="text-xs text-slate-400 text-center mt-5 flex items-center justify-center gap-1.5"></p>
</div>

<!-- Broadcast modal -->
<div id="broadcast-modal" class="hidden fixed inset-0 z-50 items-center justify-center bg-black/50 p-4">
  <div class="bg-white rounded-xl shadow-2xl w-full max-w-lg max-h-[85vh] flex flex-col overflow-hidden">
    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100 flex-shrink-0">
      <h3 class="text-sm font-bold text-primary-navy flex items-center gap-2"><i class="fa-solid fa-bullhorn text-gold"></i> Broadcast Message</h3>
      <button type="button" id="broadcast-close-btn" class="text-slate-400 hover:text-slate-600"><i class="fa-solid fa-xmark text-lg"></i></button>
    </div>

    <div class="flex items-center gap-5 px-5 pt-3 border-b border-slate-100 flex-shrink-0">
      <button type="button" class="broadcast-tab-link text-xs font-semibold pb-2.5 border-b-2 -mb-px text-gold border-gold" data-tab="compose">Compose</button>
      <button type="button" class="broadcast-tab-link text-xs font-semibold pb-2.5 border-b-2 -mb-px text-slate-500 border-transparent hover:text-primary-navy" data-tab="history">History<?= $broadcasts ? ' (' . count($broadcasts) . ')' : '' ?></button>
    </div>

    <div id="broadcast-tab-compose" class="broadcast-tab-panel overflow-y-auto">
      <form method="POST" action="<?= e(url('jobs/' . $job['id'] . '/applicants/broadcast')) ?>" class="p-5 space-y-4" id="broadcast-form">
        <?= csrf_field() ?>
        <div>
          <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Send to</label>
          <select name="status" class="form-input text-sm">
            <option value="all">All Applicants (<?= count($applicants) ?>)</option>
            <?php foreach ($statusMeta as $key => $m): ?>
              <option value="<?= e($key) ?>"><?= e($m['label']) ?> (<?= $statusCounts[$key] ?? 0 ?>)</option>
            <?php endforeach; ?>
          </select>
        </div>
        <div>
          <label class="text-xs font-semibold text-slate-600 mb-1.5 block">Message</label>
          <textarea name="body" rows="5" required maxlength="2000" class="form-input text-sm resize-none" placeholder="Write a message to send to the selected applicants..."></textarea>
          <p class="text-[0.65rem] text-slate-400 mt-1.5"><i class="fa-regular fa-circle-info"></i> Every recipient gets this as a direct message and a notification.</p>
        </div>
        <div class="flex items-center justify-end gap-2 pt-1">
          <button type="button" id="broadcast-cancel-btn" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-4 !py-2 text-xs">Cancel</button>
          <button type="submit" id="broadcast-submit-btn" class="btn-gold !px-4 !py-2 text-xs flex items-center gap-1.5"><i class="fa-solid fa-paper-plane"></i> Send Broadcast</button>
        </div>
      </form>
    </div>

    <div id="broadcast-tab-history" class="broadcast-tab-panel hidden overflow-y-auto p-5 space-y-3">
      <?php if (!$broadcasts): ?>
        <p class="text-sm text-slate-400 text-center py-10">No broadcasts sent yet for this job.</p>
      <?php else: ?>
        <?php foreach ($broadcasts as $b): $recalled = (bool) $b['recalled_at']; ?>
          <div class="border border-slate-100 rounded-lg p-3.5 <?= $recalled ? 'bg-slate-50' : '' ?>">
            <div class="flex items-start justify-between gap-2 mb-1.5">
              <div class="flex items-center gap-1.5 flex-wrap">
                <span class="badge bg-slate-100 text-slate-600 !text-[0.65rem]"><?= e(\App\Models\JobBroadcast::STATUS_LABELS[$b['status_filter']] ?? 'All Applicants') ?></span>
                <span class="text-[0.65rem] text-slate-400"><?= e(time_ago($b['created_at'])) ?></span>
                <?php if ($recalled): ?><span class="badge bg-red-100 text-red-600 !text-[0.6rem] uppercase">Recalled</span><?php endif; ?>
              </div>
              <?php if (!$recalled): ?>
                <form method="POST" action="<?= e(url('jobs/' . $job['id'] . '/applicants/broadcasts/' . $b['id'] . '/recall')) ?>" class="flex-shrink-0" data-confirm="Recall this broadcast? It will be removed from every recipient's messages." data-confirm-icon="fa-solid fa-rotate-left" data-confirm-ok-label="Recall">
                  <?= csrf_field() ?>
                  <button type="submit" class="text-[0.65rem] text-red-500 hover:text-red-700 font-semibold flex items-center gap-1"><i class="fa-solid fa-rotate-left"></i> Recall</button>
                </form>
              <?php endif; ?>
            </div>
            <p class="text-xs <?= $recalled ? 'text-slate-400' : 'text-slate-700' ?> whitespace-pre-line"><?= e($b['body']) ?></p>
            <p class="text-[0.65rem] text-slate-400 mt-1.5"><i class="fa-solid fa-paper-plane"></i> Sent to <?= (int) $b['recipient_count'] ?> applicant<?= (int) $b['recipient_count'] === 1 ? '' : 's' ?></p>
          </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
</div>

<script>
  (function () {
    var searchInput = document.getElementById('app-search');
    var statusSelect = document.getElementById('app-status');
    var experienceSelect = document.getElementById('app-experience');
    var educationSelect = document.getElementById('app-education');
    var sortSelect = document.getElementById('app-sort');
    var cards = Array.from(document.querySelectorAll('.applicant-card'));
    var emptyState = document.getElementById('app-empty');
    var countText = document.getElementById('app-count');

    function render() {
      var q = (searchInput.value || '').trim().toLowerCase();
      var visible = cards.filter(function (card) {
        var matchesSearch = !q || card.dataset.search.indexOf(q) !== -1;
        var matchesStatus = statusSelect.value === 'all' || card.dataset.status === statusSelect.value;
        var matchesExperience = experienceSelect.value === 'all' || card.dataset.experience === experienceSelect.value;
        var matchesEducation = educationSelect.value === 'all' || card.dataset.education === educationSelect.value;
        return matchesSearch && matchesStatus && matchesExperience && matchesEducation;
      });

      var sorted = visible.slice().sort(function (a, b) {
        if (sortSelect.value === 'name') return a.dataset.name.localeCompare(b.dataset.name);
        if (sortSelect.value === 'match') return b.dataset.match - a.dataset.match;
        var diff = new Date(a.dataset.created) - new Date(b.dataset.created);
        return sortSelect.value === 'oldest' ? diff : -diff;
      });

      cards.forEach(function (card) { card.classList.add('hidden'); });
      sorted.forEach(function (card, i) {
        card.classList.remove('hidden');
        card.style.order = i;
      });

      emptyState.classList.toggle('hidden', sorted.length > 0);
      countText.innerHTML = '<i class="fa-solid fa-users"></i> ' + sorted.length + ' applicant' + (sorted.length === 1 ? '' : 's') + ' found';
    }

    [searchInput, statusSelect, experienceSelect, educationSelect, sortSelect].forEach(function (el) {
      el.addEventListener('input', render);
      el.addEventListener('change', render);
    });

    document.querySelectorAll('.view-responses-btn').forEach(function (btn) {
      btn.addEventListener('click', function () {
        var panel = btn.closest('.applicant-card').querySelector('.applicant-responses');
        if (panel) panel.classList.toggle('hidden');
      });
    });

    document.getElementById('applicant-list').style.display = 'flex';
    document.getElementById('applicant-list').style.flexDirection = 'column';

    render();

    // Status dropdown: save in the background (no page reload), with a small inline indicator.
    var statusClassMap = {
      new: ['bg-sky-100', 'text-sky-700'],
      reviewed: ['bg-amber-100', 'text-amber-700'],
      shortlisted: ['bg-emerald-100', 'text-emerald-700'],
      interview_scheduled: ['bg-violet-100', 'text-violet-700'],
      rejected: ['bg-red-100', 'text-red-700']
    };
    var allStatusClasses = Object.keys(statusClassMap).reduce(function (acc, k) { return acc.concat(statusClassMap[k]); }, []);

    function setIndicator(el, state, text) {
      var colors = { saving: 'text-slate-400', saved: 'text-emerald-600', error: 'text-red-600' };
      var icon = state === 'saving' ? 'fa-circle-notch fa-spin' : (state === 'saved' ? 'fa-circle-check' : 'fa-triangle-exclamation');
      el.className = 'status-indicator text-[0.65rem] mt-1 h-3.5 flex items-center gap-1 transition-opacity duration-300 opacity-100 ' + colors[state];
      el.innerHTML = '<i class="fa-solid ' + icon + '"></i> ' + text;
    }

    document.querySelectorAll('.status-form').forEach(function (form) {
      var select = form.querySelector('.status-select');
      var indicator = form.parentElement.querySelector('.status-indicator');
      var card = form.closest('.applicant-card');
      var csrfInput = form.querySelector('input[name="_csrf"]');
      var lastSavedValue = select.value;
      var fadeTimer = null;

      select.addEventListener('change', function () {
        var newStatus = select.value;
        select.classList.remove.apply(select.classList, allStatusClasses);
        select.classList.add.apply(select.classList, statusClassMap[newStatus] || []);
        card.dataset.status = newStatus;

        clearTimeout(fadeTimer);
        setIndicator(indicator, 'saving', 'Saving...');

        var body = new URLSearchParams();
        body.set('_csrf', csrfInput.value);
        body.set('status', newStatus);

        fetch(form.action, {
          method: 'POST',
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
          body: body.toString()
        })
          .then(function (res) { return res.ok ? res.json() : Promise.reject(); })
          .then(function (data) {
            if (!data.ok) return Promise.reject();
            lastSavedValue = newStatus;
            setIndicator(indicator, 'saved', 'Saved');
            fadeTimer = setTimeout(function () { indicator.classList.remove('opacity-100'); indicator.classList.add('opacity-0'); }, 1500);
          })
          .catch(function () {
            select.value = lastSavedValue;
            select.classList.remove.apply(select.classList, allStatusClasses);
            select.classList.add.apply(select.classList, statusClassMap[lastSavedValue] || []);
            card.dataset.status = lastSavedValue;
            setIndicator(indicator, 'error', 'Failed to save - try again');
          });
      });
    });

    // Broadcast modal
    var broadcastModal = document.getElementById('broadcast-modal');
    var broadcastOpenBtn = document.getElementById('broadcast-open-btn');
    var broadcastCloseBtn = document.getElementById('broadcast-close-btn');
    var broadcastCancelBtn = document.getElementById('broadcast-cancel-btn');
    var broadcastForm = document.getElementById('broadcast-form');
    var broadcastSubmitBtn = document.getElementById('broadcast-submit-btn');
    var broadcastTabLinks = document.querySelectorAll('.broadcast-tab-link');

    function openBroadcast() {
      broadcastModal.classList.remove('hidden');
      broadcastModal.classList.add('flex');
      setBroadcastTab('compose');
    }
    function closeBroadcast() { broadcastModal.classList.add('hidden'); broadcastModal.classList.remove('flex'); }

    function setBroadcastTab(tab) {
      broadcastTabLinks.forEach(function (btn) {
        var active = btn.dataset.tab === tab;
        btn.classList.toggle('text-gold', active);
        btn.classList.toggle('border-gold', active);
        btn.classList.toggle('text-slate-500', !active);
        btn.classList.toggle('border-transparent', !active);
      });
      document.querySelectorAll('.broadcast-tab-panel').forEach(function (panel) {
        panel.classList.toggle('hidden', panel.id !== 'broadcast-tab-' + tab);
      });
    }

    if (broadcastOpenBtn) broadcastOpenBtn.addEventListener('click', openBroadcast);
    if (broadcastCloseBtn) broadcastCloseBtn.addEventListener('click', closeBroadcast);
    if (broadcastCancelBtn) broadcastCancelBtn.addEventListener('click', closeBroadcast);
    if (broadcastModal) {
      broadcastModal.addEventListener('click', function (e) { if (e.target === broadcastModal) closeBroadcast(); });
    }
    broadcastTabLinks.forEach(function (btn) {
      btn.addEventListener('click', function () { setBroadcastTab(btn.dataset.tab); });
    });
    if (broadcastForm) {
      broadcastForm.addEventListener('submit', function () {
        broadcastSubmitBtn.disabled = true;
        broadcastSubmitBtn.innerHTML = '<i class="fa-solid fa-circle-notch fa-spin"></i> Sending...';
      });
    }
  })();
</script>
