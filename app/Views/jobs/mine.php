<?php
$statusMeta = [
    'draft' => ['label' => 'Draft', 'class' => 'bg-slate-100 text-slate-500'],
    'pending' => ['label' => 'Under Review', 'class' => 'bg-amber-100 text-amber-700'],
    'active' => ['label' => 'Active', 'class' => 'bg-emerald-100 text-emerald-700'],
    'closed' => ['label' => 'Closed', 'class' => 'bg-slate-200 text-slate-600'],
];
$experienceLabels = [
    'entry' => '0-2 years',
    'mid' => '3-5 years',
    'senior' => '6-10 years',
    'executive' => '10+ years',
];
$statusTabs = [
    'all' => 'All',
    'pending' => 'Under Review',
    'active' => 'Active',
    'closed' => 'Closed',
    'draft' => 'Drafts',
];
?>
<div class="max-w-[1280px] mx-auto px-4 md:px-8 py-8">
  <a href="<?= e(url('jobs')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to Careers</a>

  <div class="flex flex-wrap items-start justify-between gap-4">
    <div>
      <h1 class="text-2xl font-extrabold text-primary-navy">My Job Posts</h1>
      <p class="text-sm text-slate-500 mt-1">Manage and track your job postings.</p>
    </div>
    <a href="<?= e(url('jobs/post')) ?>" class="btn-gold !px-4 !py-2.5 text-sm">+ Post a New Job</a>
  </div>

  <div class="flex items-center gap-6 mt-4 mb-6 border-b border-slate-200">
    <a href="<?= e(url('jobs/post')) ?>" class="text-sm font-semibold pb-2.5 border-b-2 -mb-px whitespace-nowrap text-slate-500 border-transparent hover:text-primary-navy">Post a Job</a>
    <a href="<?= e(url('jobs/mine')) ?>" class="text-sm font-semibold pb-2.5 border-b-2 -mb-px whitespace-nowrap text-gold border-gold">My Job Posts</a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6 items-start">
    <div>
      <!-- Stats -->
      <div class="grid grid-cols-2 md:grid-cols-5 gap-3 mb-6">
        <div class="card p-4">
          <div class="w-9 h-9 rounded-lg bg-sky-100 text-sky-600 flex items-center justify-center mb-2"><i class="bi bi-file-earmark-text"></i></div>
          <p class="text-xl font-extrabold text-primary-navy leading-none"><?= count($jobs) ?></p>
          <p class="text-xs font-semibold text-slate-600 mt-1.5">Total Posts</p>
          <p class="text-[0.65rem] text-slate-400">All time</p>
        </div>
        <div class="card p-4">
          <div class="w-9 h-9 rounded-lg bg-amber-100 text-amber-600 flex items-center justify-center mb-2"><i class="bi bi-clock"></i></div>
          <p class="text-xl font-extrabold text-primary-navy leading-none"><?= $stats['pending'] ?></p>
          <p class="text-xs font-semibold text-slate-600 mt-1.5">Under Review</p>
          <p class="text-[0.65rem] text-slate-400">Awaiting approval</p>
        </div>
        <div class="card p-4">
          <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center mb-2"><i class="bi bi-check-circle"></i></div>
          <p class="text-xl font-extrabold text-primary-navy leading-none"><?= $stats['active'] ?></p>
          <p class="text-xs font-semibold text-slate-600 mt-1.5">Active</p>
          <p class="text-[0.65rem] text-slate-400">Live on job board</p>
        </div>
        <div class="card p-4">
          <div class="w-9 h-9 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center mb-2"><i class="bi bi-archive"></i></div>
          <p class="text-xl font-extrabold text-primary-navy leading-none"><?= $stats['closed'] ?></p>
          <p class="text-xs font-semibold text-slate-600 mt-1.5">Closed</p>
          <p class="text-[0.65rem] text-slate-400">Not accepting applications</p>
        </div>
        <div class="card p-4">
          <div class="w-9 h-9 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center mb-2"><i class="bi bi-file-earmark"></i></div>
          <p class="text-xl font-extrabold text-primary-navy leading-none"><?= $stats['draft'] ?></p>
          <p class="text-xs font-semibold text-slate-600 mt-1.5">Drafts</p>
          <p class="text-[0.65rem] text-slate-400">Unfinished posts</p>
        </div>
      </div>

      <!-- Search + Filters -->
      <div class="flex items-center gap-3 mb-4">
        <div class="relative flex-1">
          <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
          <input type="text" id="jobs-search" placeholder="Search by job title or company..." class="form-input !pl-10">
        </div>
        <div class="relative flex-shrink-0">
          <button type="button" id="jobs-filter-btn" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-4 !py-2.5 text-sm flex items-center gap-2"><i class="fa-solid fa-filter"></i> Filters</button>
          <div id="jobs-filter-panel" class="hidden absolute right-0 top-full mt-1 z-20 bg-white border border-slate-200 rounded-lg shadow-lg w-44 py-1">
            <button type="button" class="jobs-sort-option w-full text-left px-3 py-2 text-xs text-slate-700 hover:bg-slate-50" data-sort="recent">Newest first</button>
            <button type="button" class="jobs-sort-option w-full text-left px-3 py-2 text-xs text-slate-700 hover:bg-slate-50" data-sort="oldest">Oldest first</button>
            <button type="button" class="jobs-sort-option w-full text-left px-3 py-2 text-xs text-slate-700 hover:bg-slate-50" data-sort="title">Title (A&ndash;Z)</button>
          </div>
        </div>
      </div>

      <!-- Status tabs -->
      <div class="flex flex-wrap gap-2 mb-5">
        <?php foreach ($statusTabs as $key => $label): ?>
          <button type="button" class="jobs-tab-link badge <?= $key === 'all' ? 'bg-primary-navy text-white' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>" data-status="<?= e($key) ?>"><?= e($label) ?> (<?= $key === 'all' ? count($jobs) : $stats[$key] ?>)</button>
        <?php endforeach; ?>
      </div>

      <!-- Job rows -->
      <div id="job-rows" class="space-y-3">
        <?php foreach ($jobs as $job):
            $group = $job['status_group'];
            $meta = $statusMeta[$group];
            $label = $group === 'closed' && $job['approval_status'] === 'rejected' ? 'Not Approved' : $meta['label'];
            $cats = \App\Models\Category::forJob((int) $job['id']);
            $catName = $cats[0]['name'] ?? null;
            $searchBlob = mb_strtolower($job['title'] . ' ' . $job['company']);

            if ($group === 'draft') {
                $footerText = 'Saved ' . time_ago($job['updated_at'] ?? $job['created_at']);
            } elseif ($group === 'pending') {
                $footerText = 'Submitted ' . time_ago($job['created_at']);
            } elseif ($group === 'closed') {
                $footerText = ($label === 'Not Approved' ? 'Not approved ' : 'Closed ') . time_ago($job['updated_at'] ?? $job['created_at']);
            } else {
                $footerText = 'Posted ' . time_ago($job['posted_at'] ?? $job['created_at']);
                if (!empty($job['closing_date'])) {
                    $daysLeft = (int) ceil((strtotime($job['closing_date']) - strtotime('today')) / 86400);
                    if ($daysLeft >= 0) {
                        $footerText .= ' &bull; Expires in ' . $daysLeft . ' day' . ($daysLeft === 1 ? '' : 's');
                    }
                }
            }
        ?>
          <div class="job-row card p-4" data-status="<?= e($group) ?>" data-search="<?= e($searchBlob) ?>" data-created="<?= e($job['created_at']) ?>" data-title="<?= e(mb_strtolower($job['title'])) ?>">
            <div class="flex items-start gap-3">
              <?= job_logo_html($job, 'w-11 h-11') ?>
              <div class="min-w-0 flex-1">
                <div class="flex items-start justify-between gap-3">
                  <div class="min-w-0">
                    <h4 class="text-sm font-bold text-primary-navy truncate"><?= e($job['title'] ?: 'Untitled draft') ?></h4>
                    <p class="text-xs text-slate-500 truncate"><?= e($job['company']) ?><?= $catName ? ' &bull; ' . e($catName) : '' ?></p>
                  </div>
                  <span class="badge <?= $label === 'Not Approved' ? 'bg-red-100 text-red-700' : $meta['class'] ?> flex-shrink-0 uppercase text-[0.65rem]"><?= e($label) ?></span>
                </div>
                <div class="flex flex-wrap gap-3 text-[0.7rem] text-slate-500 mt-2">
                  <?php if ($job['location']): ?><span><i class="fa-solid fa-location-dot"></i> <?= e($job['location']) ?></span><?php endif; ?>
                  <span><i class="fa-regular fa-clock"></i> <?= e($job['job_type']) ?></span>
                  <?php if (!empty($job['experience_level'])): ?><span><i class="fa-solid fa-user"></i> <?= e($experienceLabels[$job['experience_level']] ?? '') ?></span><?php endif; ?>
                </div>
              </div>
            </div>
            <div class="flex items-center justify-between mt-3 pt-3 border-t border-slate-100">
              <p class="text-[0.68rem] text-slate-400"><?= $footerText ?></p>
              <div class="flex items-center gap-2 flex-shrink-0">
                <a href="<?= e(url('jobs/' . $job['id'])) ?>" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-3 !py-1.5 text-xs">View</a>
                <div class="relative">
                  <button type="button" class="kebab-btn text-slate-400 hover:text-primary-navy px-1"><i class="bi bi-three-dots-vertical"></i></button>
                  <div class="kebab-menu hidden absolute right-0 top-full mt-1 z-20 bg-white border border-slate-200 rounded-lg shadow-lg w-32 py-1">
                    <a href="<?= e(url('jobs/mine/' . $job['id'] . '/edit')) ?>" class="block px-3 py-2 text-xs text-slate-700 hover:bg-slate-50">Edit</a>
                    <form method="POST" action="<?= e(url('jobs/mine/' . $job['id'] . '/delete')) ?>" data-confirm="Delete this job post? This cannot be undone.">
                      <?= csrf_field() ?>
                      <button type="submit" class="w-full text-left px-3 py-2 text-xs text-red-600 hover:bg-slate-50">Delete</button>
                    </form>
                  </div>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
      <p id="jobs-empty" class="hidden text-sm text-slate-400 py-6 text-center">No job posts match those filters.</p>
      <?php if (empty($jobs)): ?>
        <p class="text-sm text-slate-400">You haven't posted any jobs yet. <a href="<?= e(url('jobs/post')) ?>" class="text-gold hover:underline">Post one now</a>.</p>
      <?php endif; ?>

      <?php if (!empty($jobs)): ?>
        <div class="flex flex-wrap items-center justify-between gap-3 mt-6">
          <p id="jobs-showing-text" class="text-xs text-slate-400"></p>
          <div class="flex items-center gap-1.5" id="jobs-pagination"></div>
        </div>

        <div class="card p-3 mt-6 flex items-start gap-2.5 !bg-sky-50 !border-sky-100">
          <i class="fa-solid fa-circle-info text-sky-500 mt-0.5"></i>
          <p class="text-xs text-sky-800">Job posts are reviewed by our admin team before they appear on the public job board. This usually takes 1&ndash;2 business days.</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Sidebar -->
    <aside class="space-y-6 lg:sticky lg:top-[calc(var(--header-height)+1rem)]">
      <div class="card p-4">
        <h3 class="text-sm font-bold text-primary-navy mb-3 flex items-center gap-2"><i class="fa-solid fa-lightbulb text-amber-400"></i> Tips for Better Results</h3>
        <div class="space-y-3.5">
          <div class="flex items-start gap-2.5">
            <i class="fa-solid fa-file-lines text-gold mt-0.5"></i>
            <div>
              <p class="text-xs font-bold text-primary-navy">Write clear job titles</p>
              <p class="text-xs text-slate-500 mt-0.5">Use specific titles to attract the right candidates.</p>
            </div>
          </div>
          <div class="flex items-start gap-2.5">
            <i class="fa-solid fa-list-check text-gold mt-0.5"></i>
            <div>
              <p class="text-xs font-bold text-primary-navy">Add skills &amp; requirements</p>
              <p class="text-xs text-slate-500 mt-0.5">Include must-have skills to get better matches.</p>
            </div>
          </div>
          <div class="flex items-start gap-2.5">
            <i class="fa-solid fa-shield-check text-gold mt-0.5"></i>
            <div>
              <p class="text-xs font-bold text-primary-navy">Review before submitting</p>
              <p class="text-xs text-slate-500 mt-0.5">Ensure all details are accurate before sending for review.</p>
            </div>
          </div>
          <div class="flex items-start gap-2.5">
            <i class="fa-solid fa-pen-to-square text-gold mt-0.5"></i>
            <div>
              <p class="text-xs font-bold text-primary-navy">Keep it updated</p>
              <p class="text-xs text-slate-500 mt-0.5">Update your post to keep it relevant and visible.</p>
            </div>
          </div>
        </div>
      </div>

      <div class="card p-4 !bg-indigo-50 !border-indigo-100">
        <p class="text-xs font-bold text-indigo-600 mb-1">Need to hire faster?</p>
        <h3 class="text-sm font-bold text-primary-navy mb-2">Upgrade to Premium Hiring</h3>
        <ul class="space-y-1.5 mb-3">
          <li class="flex items-center gap-2 text-xs text-slate-600"><i class="fa-solid fa-circle-check text-emerald-500"></i> Featured job placement</li>
          <li class="flex items-center gap-2 text-xs text-slate-600"><i class="fa-solid fa-circle-check text-emerald-500"></i> Highlighted to top alumni</li>
          <li class="flex items-center gap-2 text-xs text-slate-600"><i class="fa-solid fa-circle-check text-emerald-500"></i> Priority review</li>
          <li class="flex items-center gap-2 text-xs text-slate-600"><i class="fa-solid fa-circle-check text-emerald-500"></i> Candidate recommendations</li>
        </ul>
        <a href="mailto:<?= e($premiumHiringEmail) ?>?subject=<?= rawurlencode('Premium Hiring Inquiry') ?>" class="btn !bg-indigo-600 !text-white hover:!bg-indigo-700 !py-2 text-xs w-full text-center block">Learn More</a>
      </div>

      <div class="card p-4 !bg-amber-50 !border-amber-100">
        <div class="flex items-center gap-2 mb-1.5">
          <i class="fa-solid fa-headset text-amber-500"></i>
          <h3 class="text-sm font-bold text-primary-navy">Need Help?</h3>
        </div>
        <p class="text-xs text-slate-600 mb-3">Our support team is here to help you with your job posting.</p>
        <a href="mailto:<?= e($supportEmail) ?>?subject=<?= rawurlencode('Job Posting Support') ?>" class="btn bg-white border border-gold !text-gold hover:bg-gold hover:!text-white !py-2 text-xs w-full text-center block">Contact Support</a>
      </div>
    </aside>
  </div>
</div>

<script>
  (function () {
    var searchInput = document.getElementById('jobs-search');
    var tabLinks = document.querySelectorAll('.jobs-tab-link');
    var rows = Array.from(document.querySelectorAll('.job-row'));
    var emptyState = document.getElementById('jobs-empty');
    var showingText = document.getElementById('jobs-showing-text');
    var pagination = document.getElementById('jobs-pagination');
    var filterBtn = document.getElementById('jobs-filter-btn');
    var filterPanel = document.getElementById('jobs-filter-panel');
    var sortOptions = document.querySelectorAll('.jobs-sort-option');

    var PER_PAGE = 10;
    var activeStatus = 'all';
    var activeSort = 'recent';
    var currentPage = 1;

    function sortRows(list) {
      return list.slice().sort(function (a, b) {
        if (activeSort === 'title') return a.dataset.title.localeCompare(b.dataset.title);
        var diff = new Date(a.dataset.created) - new Date(b.dataset.created);
        return activeSort === 'oldest' ? diff : -diff;
      });
    }

    function render() {
      var q = (searchInput.value || '').trim().toLowerCase();
      var filtered = rows.filter(function (row) {
        var matchesStatus = activeStatus === 'all' || row.dataset.status === activeStatus;
        var matchesSearch = !q || row.dataset.search.indexOf(q) !== -1;
        return matchesStatus && matchesSearch;
      });
      filtered = sortRows(filtered);

      var totalPages = Math.max(1, Math.ceil(filtered.length / PER_PAGE));
      currentPage = Math.min(currentPage, totalPages);
      var start = (currentPage - 1) * PER_PAGE;
      var pageRows = filtered.slice(start, start + PER_PAGE);

      rows.forEach(function (row) { row.classList.add('hidden'); });
      pageRows.forEach(function (row) { row.classList.remove('hidden'); });
      pageRows.forEach(function (row, i) { row.style.order = i; });

      emptyState.classList.toggle('hidden', filtered.length > 0);

      if (filtered.length === 0) {
        showingText.textContent = '';
      } else {
        showingText.textContent = 'Showing ' + (start + 1) + ' to ' + Math.min(start + PER_PAGE, filtered.length) + ' of ' + filtered.length + ' job' + (filtered.length === 1 ? '' : 's');
      }

      pagination.innerHTML = '';
      if (totalPages > 1) {
        var prev = document.createElement('button');
        prev.type = 'button';
        prev.className = 'w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:text-primary-navy' + (currentPage <= 1 ? ' pointer-events-none opacity-40' : '');
        prev.innerHTML = '<i class="fa-solid fa-arrow-left text-xs"></i>';
        prev.addEventListener('click', function () { currentPage--; render(); });
        pagination.appendChild(prev);

        for (var p = 1; p <= totalPages; p++) {
          (function (p) {
            var btn = document.createElement('button');
            btn.type = 'button';
            btn.textContent = p;
            btn.className = 'w-8 h-8 rounded-lg flex items-center justify-center text-xs font-semibold ' + (p === currentPage ? 'bg-gold text-white' : 'border border-slate-200 text-slate-600 hover:bg-slate-50');
            btn.addEventListener('click', function () { currentPage = p; render(); });
            pagination.appendChild(btn);
          })(p);
        }

        var next = document.createElement('button');
        next.type = 'button';
        next.className = 'w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:text-primary-navy' + (currentPage >= totalPages ? ' pointer-events-none opacity-40' : '');
        next.innerHTML = '<i class="fa-solid fa-arrow-right text-xs"></i>';
        next.addEventListener('click', function () { currentPage++; render(); });
        pagination.appendChild(next);
      }
    }

    searchInput.addEventListener('input', function () { currentPage = 1; render(); });

    tabLinks.forEach(function (btn) {
      btn.addEventListener('click', function () {
        activeStatus = btn.dataset.status;
        currentPage = 1;
        tabLinks.forEach(function (b) {
          var isActive = b === btn;
          b.classList.toggle('bg-primary-navy', isActive);
          b.classList.toggle('text-white', isActive);
          b.classList.toggle('bg-slate-100', !isActive);
          b.classList.toggle('text-slate-600', !isActive);
        });
        render();
      });
    });

    if (filterBtn) {
      filterBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        filterPanel.classList.toggle('hidden');
      });
    }
    sortOptions.forEach(function (opt) {
      opt.addEventListener('click', function () {
        activeSort = opt.dataset.sort;
        filterPanel.classList.add('hidden');
        render();
      });
    });
    document.addEventListener('click', function (e) {
      if (filterPanel && !filterPanel.classList.contains('hidden') && !filterPanel.contains(e.target) && e.target !== filterBtn) {
        filterPanel.classList.add('hidden');
      }
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

    render();
  })();
</script>
