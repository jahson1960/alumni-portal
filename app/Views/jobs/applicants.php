<?php
$statusMeta = [
    'new' => ['label' => 'Applied', 'class' => 'bg-sky-100 text-sky-700'],
    'reviewed' => ['label' => 'Reviewed', 'class' => 'bg-amber-100 text-amber-700'],
    'shortlisted' => ['label' => 'Shortlisted', 'class' => 'bg-emerald-100 text-emerald-700'],
    'interview_scheduled' => ['label' => 'Interview Scheduled', 'class' => 'bg-indigo-100 text-indigo-700'],
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
?>
<div class="max-w-[960px] mx-auto px-4 md:px-8 py-8">
  <a href="<?= e(url('jobs/mine')) ?>" class="text-sm text-slate-500 hover:text-gold mb-4 inline-flex items-center gap-2"><i class="fa-solid fa-arrow-left"></i> Back to My Job Posts</a>

  <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
      <h1 class="text-xl font-extrabold text-primary-navy">Applicants</h1>
      <p class="text-sm text-slate-500 mt-1">Review and manage candidates for this role.</p>
    </div>
  </div>

  <!-- Job summary -->
  <div class="card p-5 mb-6">
    <div class="flex flex-wrap items-start justify-between gap-4">
      <div class="flex gap-3 min-w-0">
        <?= job_logo_html($job, 'w-12 h-12 flex-shrink-0') ?>
        <div class="min-w-0">
          <h2 class="text-base font-bold text-primary-navy truncate"><?= e($job['title']) ?><?= $isClosed ? ' <span class="badge-blue !bg-slate-200 !text-slate-600 ml-1">Closed</span>' : '' ?></h2>
          <p class="text-xs text-slate-500 mt-0.5"><?= e($job['company']) ?><?= $job['location'] ? ' &bull; ' . e($job['location']) : '' ?><?= $job['job_type'] ? ' &bull; ' . e($job['job_type']) : '' ?></p>
          <?php if ($job['description']): ?>
            <p class="text-xs text-slate-500 mt-2 line-clamp-2"><?= e(mb_strimwidth(strip_tags($job['description']), 0, 220, '...')) ?></p>
          <?php endif; ?>
        </div>
      </div>
      <div class="flex items-center gap-2 flex-shrink-0">
        <a href="<?= e(url('jobs/' . $job['id'])) ?>" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-3.5 !py-2 text-xs">View Job Details</a>
        <a href="<?= e(url('jobs/mine/' . $job['id'] . '/edit')) ?>" class="btn-gold !px-3.5 !py-2 text-xs">Manage Job</a>
      </div>
    </div>
  </div>

  <!-- Search + Filters -->
  <div class="flex flex-wrap items-center gap-3 mb-5">
    <div class="relative flex-1 min-w-[200px]">
      <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
      <input type="text" id="app-search" placeholder="Search by name, skill, or school..." class="form-input !pl-10">
    </div>
    <select id="app-status" class="form-input !py-2.5 text-sm max-w-[170px]">
      <option value="all">All Statuses</option>
      <?php foreach ($statusMeta as $key => $m): ?>
        <option value="<?= e($key) ?>"><?= e($m['label']) ?></option>
      <?php endforeach; ?>
    </select>
    <select id="app-experience" class="form-input !py-2.5 text-sm max-w-[180px]">
      <option value="all">All Experience Levels</option>
      <?php foreach ($experienceLabels as $key => $label): ?>
        <option value="<?= e($key) ?>"><?= e($label) ?></option>
      <?php endforeach; ?>
    </select>
    <select id="app-education" class="form-input !py-2.5 text-sm max-w-[170px]">
      <option value="all">All Education Levels</option>
      <?php foreach ($educationLabels as $key => $label): ?>
        <option value="<?= e($key) ?>"><?= e($label) ?></option>
      <?php endforeach; ?>
    </select>
    <select id="app-sort" class="form-input !py-2.5 text-sm max-w-[160px]">
      <option value="recent">Newest first</option>
      <option value="oldest">Oldest first</option>
      <option value="match">Best match</option>
      <option value="name">Name (A&ndash;Z)</option>
    </select>
  </div>

  <div class="space-y-4" id="applicant-list">
    <?php foreach ($applicants as $applicant):
        $meta = $statusMeta[$applicant['status']] ?? $statusMeta['new'];
        $searchBlob = mb_strtolower($applicant['applicant_name'] . ' ' . implode(' ', $applicant['skills_list']) . ' ' . ($applicant['education_summary']['school'] ?? ''));
        $isNew = $applicant['status'] === 'new';
    ?>
      <div class="applicant-card card p-5" data-status="<?= e($applicant['status']) ?>" data-experience="<?= e($applicant['experience_level']) ?>" data-education="<?= e($applicant['education_level']) ?>" data-search="<?= e($searchBlob) ?>" data-created="<?= e($applicant['created_at']) ?>" data-match="<?= (int) $applicant['match_percent'] ?>" data-name="<?= e(mb_strtolower($applicant['applicant_name'])) ?>">
        <div class="flex flex-wrap items-start justify-between gap-4">
          <div class="flex gap-3 min-w-0 flex-1">
            <a href="<?= e(url('alumni/' . $applicant['user_id'])) ?>" class="flex-shrink-0">
              <?= avatar_html(['name' => $applicant['applicant_name'], 'avatar' => $applicant['applicant_avatar']], 'w-12 h-12') ?>
            </a>
            <div class="min-w-0 flex-1">
              <div class="flex items-center gap-2 flex-wrap">
                <a href="<?= e(url('alumni/' . $applicant['user_id'])) ?>" class="text-sm font-bold text-primary-navy hover:text-gold truncate"><?= e($applicant['applicant_name']) ?></a>
                <?php if ($isNew): ?><span class="badge !bg-gold/15 !text-gold uppercase text-[0.6rem]">New</span><?php endif; ?>
                <span class="text-[0.68rem] text-slate-400"><?= e(time_ago($applicant['created_at'])) ?></span>
              </div>
              <p class="text-xs text-slate-500 mt-0.5"><?= e($applicant['applicant_headline'] ?: '') ?></p>

              <div class="flex flex-wrap gap-3 text-[0.72rem] text-slate-500 mt-2.5">
                <?php if ($applicant['applicant_city'] || $applicant['applicant_country']): ?>
                  <span><i class="fa-solid fa-location-dot"></i> <?= e(trim(($applicant['applicant_city'] ?: '') . (($applicant['applicant_city'] && $applicant['applicant_country']) ? ', ' : '') . ($applicant['applicant_country'] ?: ''))) ?></span>
                <?php endif; ?>
                <?php if ($applicant['applicant_show_email']): ?>
                  <span><i class="fa-regular fa-envelope"></i> <?= e($applicant['applicant_email']) ?></span>
                <?php endif; ?>
                <?php if ($applicant['applicant_show_phone'] && $applicant['applicant_phone']): ?>
                  <span><i class="fa-solid fa-phone"></i> <?= e($applicant['applicant_phone']) ?></span>
                <?php endif; ?>
                <?php if ($applicant['applicant_linkedin']): ?>
                  <a href="<?= e($applicant['applicant_linkedin']) ?>" target="_blank" rel="noopener noreferrer" class="hover:text-gold"><i class="fa-brands fa-linkedin"></i> LinkedIn</a>
                <?php endif; ?>
              </div>

              <div class="grid sm:grid-cols-2 gap-3 mt-3">
                <?php if ($applicant['education_summary']): $ed = $applicant['education_summary']; ?>
                  <div>
                    <p class="text-[0.65rem] font-extrabold text-slate-400 uppercase tracking-wide mb-0.5">Education</p>
                    <p class="text-xs text-slate-700"><?= e($ed['degree'] ?: 'Studied') ?><?= $ed['school'] ? ' &bull; ' . e($ed['school']) : '' ?></p>
                    <?php if ($ed['start_year'] || $ed['end_year']): ?>
                      <p class="text-[0.68rem] text-slate-400"><?= e((string) ($ed['start_year'] ?: '')) ?><?= $ed['start_year'] && $ed['end_year'] ? '&ndash;' : '' ?><?= e((string) ($ed['end_year'] ?: '')) ?></p>
                    <?php endif; ?>
                  </div>
                <?php endif; ?>
                <?php if ($applicant['experience_summary']): $exp = $applicant['experience_summary']; ?>
                  <div>
                    <p class="text-[0.65rem] font-extrabold text-slate-400 uppercase tracking-wide mb-0.5">Experience</p>
                    <p class="text-xs text-slate-700"><?= e($applicant['total_years']) ?> year<?= $applicant['total_years'] == 1 ? '' : 's' ?> total</p>
                    <p class="text-[0.68rem] text-slate-400"><?= e($exp['title']) ?><?= $exp['company'] ? ' &bull; ' . e($exp['company']) : '' ?></p>
                  </div>
                <?php endif; ?>
              </div>

              <?php if ($applicant['skills_list']): ?>
                <div class="flex flex-wrap gap-1.5 mt-3">
                  <?php foreach ($applicant['skills_list'] as $skill): ?>
                    <span class="badge bg-slate-100 text-slate-600 !text-[0.65rem]"><?= e($skill) ?></span>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            </div>
          </div>

          <!-- Match score -->
          <div class="flex flex-col items-center flex-shrink-0 w-[140px]">
            <div class="relative w-16 h-16">
              <svg viewBox="0 0 36 36" class="w-16 h-16 -rotate-90">
                <circle cx="18" cy="18" r="15.5" fill="none" stroke="#e2e8f0" stroke-width="3"></circle>
                <circle cx="18" cy="18" r="15.5" fill="none" stroke="<?= $applicant['match_percent'] >= 80 ? '#10b981' : ($applicant['match_percent'] >= 50 ? '#f59e0b' : '#ef4444') ?>" stroke-width="3" stroke-dasharray="97.4" stroke-dashoffset="<?= round(97.4 * (1 - $applicant['match_percent'] / 100), 2) ?>" stroke-linecap="round"></circle>
              </svg>
              <div class="absolute inset-0 flex items-center justify-center">
                <span class="text-sm font-extrabold text-primary-navy"><?= (int) $applicant['match_percent'] ?>%</span>
              </div>
            </div>
            <p class="text-[0.6rem] font-semibold text-slate-400 uppercase tracking-wide mt-1 mb-2">Profile Match</p>
            <div class="w-full space-y-1">
              <?php foreach ($matchCategoryLabels as $catKey => $catLabel): $met = $applicant['match_categories'][$catKey] ?? false; ?>
                <div class="flex items-center gap-1.5 text-[0.65rem] <?= $met ? 'text-slate-500' : 'text-amber-600' ?>">
                  <i class="<?= $met ? 'fa-solid fa-circle-check text-emerald-500' : 'fa-solid fa-triangle-exclamation text-amber-500' ?> flex-shrink-0"></i>
                  <span class="truncate"><?= e($catLabel) ?></span>
                </div>
              <?php endforeach; ?>
            </div>
          </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-3 mt-4 pt-3.5 border-t border-slate-100">
          <form method="POST" action="<?= e(url('jobs/' . $job['id'] . '/applicants/' . $applicant['id'] . '/status')) ?>" class="flex items-center gap-2">
            <?= csrf_field() ?>
            <select name="status" onchange="this.form.submit()" class="form-input !py-1.5 text-xs max-w-[170px] font-semibold <?= e($meta['class']) ?>">
              <?php foreach ($statusMeta as $key => $m): ?>
                <option value="<?= e($key) ?>" <?= $applicant['status'] === $key ? 'selected' : '' ?>><?= e($m['label']) ?></option>
              <?php endforeach; ?>
            </select>
          </form>

          <div class="flex items-center gap-2 flex-shrink-0">
            <a href="<?= e(url('alumni/' . $applicant['user_id'])) ?>" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-3 !py-1.5 text-xs">View Profile</a>
            <a href="<?= e(url('messages/' . $applicant['user_id'])) ?>" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-3 !py-1.5 text-xs">Send Message</a>
            <div class="relative">
              <button type="button" class="kebab-btn text-slate-400 hover:text-primary-navy px-1"><i class="bi bi-three-dots-vertical"></i></button>
              <div class="kebab-menu hidden absolute right-0 bottom-full mb-1 z-20 bg-white border border-slate-200 rounded-lg shadow-lg w-44 py-1">
                <?php if ($applicant['responses']): ?>
                  <button type="button" class="view-responses-btn block w-full text-left px-3 py-2 text-xs text-slate-700 hover:bg-slate-50" data-applicant="<?= e((string) $applicant['id']) ?>">View Submitted Answers</button>
                <?php endif; ?>
                <?php if ($applicant['applicant_resume_file'] || $applicant['applicant_resume_url']): ?>
                  <a href="<?= e($applicant['applicant_resume_file'] ? upload_url($applicant['applicant_resume_file']) : $applicant['applicant_resume_url']) ?>" target="_blank" rel="noopener noreferrer" class="block px-3 py-2 text-xs text-slate-700 hover:bg-slate-50">View Resume</a>
                <?php endif; ?>
              </div>
            </div>
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
  <p id="app-count" class="text-xs text-slate-400 text-center mt-5"></p>
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
      countText.textContent = sorted.length + ' applicant' + (sorted.length === 1 ? '' : 's') + ' found';
    }

    [searchInput, statusSelect, experienceSelect, educationSelect, sortSelect].forEach(function (el) {
      el.addEventListener('input', render);
      el.addEventListener('change', render);
    });

    document.querySelectorAll('.kebab-btn').forEach(function (btn) {
      btn.addEventListener('click', function (e) {
        e.stopPropagation();
        var menu = btn.nextElementSibling;
        document.querySelectorAll('.kebab-menu').forEach(function (m) { if (m !== menu) m.classList.add('hidden'); });
        menu.classList.toggle('hidden');
      });
    });
    document.addEventListener('click', function () {
      document.querySelectorAll('.kebab-menu').forEach(function (m) { m.classList.add('hidden'); });
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
  })();
</script>
