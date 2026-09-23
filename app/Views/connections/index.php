<?php
$authUser = \App\Core\Auth::user();
$cohortText = function (array $person): string {
    return trim(($person['program'] ?? '') . ($person['graduation_year'] ? ' ' . $person['graduation_year'] : ''));
};
?>
<div class="max-w-[1400px] mx-auto px-4 md:px-8 py-8">

  <div class="flex flex-col sm:flex-row sm:items-start sm:justify-between gap-4 mb-4">
    <div>
      <h1 id="connections-title" class="text-2xl font-extrabold text-primary-navy uppercase">My Connections</h1>
      <p id="connections-subtitle" class="text-sm text-slate-500">Stay connected. Grow together.</p>
    </div>
    <a href="<?= e(url('directory')) ?>" class="btn bg-white border border-gold !text-gold hover:bg-gold hover:!text-white !px-4 !py-2.5 text-xs whitespace-nowrap"><i class="bi bi-people"></i> Find Alumni</a>
  </div>

  <div class="sticky top-[var(--header-height)] z-30 bg-slate-50 py-3 mb-3">
    <div class="relative max-w-sm ml-auto">
      <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
      <input type="text" id="connections-search" placeholder="Search by name, company, or skill..." class="form-input !pl-9 text-sm w-full">
    </div>
  </div>

  <div class="flex flex-nowrap items-center gap-6 mb-6 border-b border-slate-200 overflow-x-auto overflow-y-hidden">
    <button type="button" class="conn-tab-link flex items-center gap-2 text-sm font-semibold pb-2.5 border-b-2 -mb-px whitespace-nowrap text-gold border-gold" data-tab="connections"><i class="bi bi-people-fill"></i> My Connections</button>
    <button type="button" class="conn-tab-link flex items-center gap-2 text-sm font-semibold pb-2.5 border-b-2 -mb-px whitespace-nowrap text-slate-500 border-transparent hover:text-primary-navy" data-tab="requests"><i class="bi bi-person-plus"></i> Requests <span id="requests-badge" class="bg-gold text-white text-[10px] font-bold rounded-full w-5 h-5 flex items-center justify-center <?= count($incoming) > 0 ? '' : 'hidden' ?>"><?= count($incoming) ?></span></button>
    <button type="button" class="conn-tab-link flex items-center gap-2 text-sm font-semibold pb-2.5 border-b-2 -mb-px whitespace-nowrap text-slate-500 border-transparent hover:text-primary-navy" data-tab="suggested"><i class="bi bi-star"></i> Suggested</button>
    <button type="button" class="conn-tab-link flex items-center gap-2 text-sm font-semibold pb-2.5 border-b-2 -mb-px whitespace-nowrap text-slate-500 border-transparent hover:text-primary-navy" data-tab="following"><i class="bi bi-eye"></i> Following</button>
  </div>

  <!-- My Connections -->
  <div class="conn-tab-panel" data-tab="connections">
    <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
      <p id="connections-count" class="text-xs text-slate-400"><?= count($connections) ?> Connection<?= count($connections) === 1 ? '' : 's' ?></p>
      <div class="flex items-center gap-3">
        <form method="GET" action="<?= e(url('connections')) ?>" id="sort-form" class="flex items-center gap-1.5">
          <select name="sort" onchange="document.getElementById('sort-form').submit()" class="form-input !py-1.5 text-xs">
            <option value="recent" <?= $sort === 'recent' ? 'selected' : '' ?>>Recently Connected</option>
            <option value="name_asc" <?= $sort === 'name_asc' ? 'selected' : '' ?>>Name (A&ndash;Z)</option>
            <option value="name_desc" <?= $sort === 'name_desc' ? 'selected' : '' ?>>Name (Z&ndash;A)</option>
          </select>
        </form>
        <div class="flex items-center border border-slate-200 rounded-lg overflow-hidden">
          <button type="button" id="grid-view-btn" class="px-2.5 py-1.5 bg-gold text-white"><i class="bi bi-grid-3x3-gap"></i></button>
          <button type="button" id="list-view-btn" class="px-2.5 py-1.5 bg-white text-slate-400 hover:text-primary-navy"><i class="bi bi-list-ul"></i></button>
        </div>
      </div>
    </div>

    <div id="connections-results" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <?php foreach ($connections as $i => $person):
        $search = mb_strtolower($person['name'] . ' ' . $person['company'] . ' ' . $person['headline']);
      ?>
        <div class="conn-card card p-5 <?= $i >= 8 ? 'hidden conn-extra' : '' ?>" data-search="<?= e($search) ?>">
          <div class="flex items-start justify-between mb-3">
            <a href="<?= e(url('alumni/' . $person['id'])) ?>" data-role="avatar"><?= avatar_html($person, 'w-11 h-11') ?></a>
            <div class="relative">
              <button type="button" class="kebab-btn text-slate-300 hover:text-primary-navy px-1" title="More options"><i class="bi bi-three-dots-vertical"></i></button>
              <div class="kebab-menu hidden absolute right-0 top-full mt-1 z-20 bg-white border border-slate-200 rounded-lg shadow-lg w-44 py-1">
                <a href="<?= e(url('alumni/' . $person['id'])) ?>" class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 hover:bg-slate-50"><i class="bi bi-person w-4"></i> View Profile</a>
                <form method="POST" action="<?= e(url('connections/' . $person['id'] . '/block')) ?>" class="ajax-action-form" data-confirm="Block this connection? They won't be able to send you a connection request again." data-on-success="remove-card" data-decrement="#connections-count">
                  <?= csrf_field() ?>
                  <button type="submit" class="w-full flex items-center gap-2 text-left px-3 py-2 text-xs text-slate-700 hover:bg-slate-50"><i class="bi bi-slash-circle w-4"></i> Block Connection</button>
                </form>
                <form method="POST" action="<?= e(url('connections/' . $person['id'] . '/remove')) ?>" class="ajax-action-form" data-confirm="Remove this connection?" data-on-success="remove-card" data-decrement="#connections-count">
                  <?= csrf_field() ?>
                  <button type="submit" class="w-full flex items-center gap-2 text-left px-3 py-2 text-xs text-red-600 hover:bg-red-50"><i class="bi bi-trash w-4"></i> Remove Connection</button>
                </form>
              </div>
            </div>
          </div>
          <a href="<?= e(url('alumni/' . $person['id'])) ?>" data-role="name" class="text-sm font-bold text-primary-navy hover:text-gold truncate block"><?= e($person['name']) ?></a>
          <p data-role="headline" class="text-xs text-slate-500 truncate"><?= e($person['headline'] ?: '') ?></p>
          <?php if ($person['company']): ?><p data-role="company" class="text-xs text-slate-600 font-medium truncate"><?= e($person['company']) ?></p><?php endif; ?>
          <?php if (location_display($person)): ?><p class="text-[0.68rem] text-slate-400 mt-1"><i class="bi bi-geo-alt"></i> <span data-role="location"><?= e(location_display($person)) ?></span></p><?php endif; ?>

          <?php if ($cohortText($person)): ?>
            <span data-role="cohort" class="inline-block mt-2 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-gold/10 text-gold"><?= e($cohortText($person)) ?></span>
          <?php endif; ?>

          <p class="text-[0.65rem] text-slate-400 mt-3 pt-3 border-t border-slate-100">Connected <?= e(time_ago($person['connected_at'] ?? null)) ?></p>
          <a href="<?= e(url('messages/' . $person['id'])) ?>" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !py-1.5 text-xs w-full mt-2"><i class="bi bi-chat-dots"></i> Message</a>
        </div>
      <?php endforeach; ?>
      <?php if (empty($connections)): ?>
        <p id="connections-empty" class="text-sm text-slate-400 col-span-full">You haven't connected with anyone yet. Check the <button type="button" class="conn-tab-link text-gold hover:underline" data-tab="suggested">Suggested</button> tab to get started.</p>
      <?php endif; ?>
    </div>
    <?php if (count($connections) > 8): ?>
      <div class="text-center mt-6">
        <button type="button" id="load-more-connections" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-5 !py-2 text-xs">Load More <i class="bi bi-chevron-down"></i></button>
      </div>
    <?php endif; ?>
  </div>

  <!-- Requests -->
  <div class="conn-tab-panel hidden" data-tab="requests">
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 items-start">
      <div class="card p-5">
        <h3 id="incoming-heading" class="text-xs font-bold text-primary-navy uppercase tracking-wide mb-1">Incoming Requests (<?= count($incoming) ?>)</h3>
        <p class="text-xs text-slate-400 mb-4">Alumni who want to connect with you</p>
        <div id="incoming-list" class="space-y-4">
          <?php foreach ($incoming as $person):
            $search = mb_strtolower($person['name'] . ' ' . $person['company'] . ' ' . $person['headline']);
          ?>
            <div class="flex flex-wrap items-center gap-3 pb-4 border-b border-slate-100 last:border-0 last:pb-0" data-search="<?= e($search) ?>">
              <a href="<?= e(url('alumni/' . $person['id'])) ?>" data-role="avatar" class="flex-shrink-0"><?= avatar_html($person, 'w-11 h-11') ?></a>
              <div class="min-w-0 flex-1">
                <a href="<?= e(url('alumni/' . $person['id'])) ?>" data-role="name" class="text-sm font-bold text-primary-navy hover:text-gold truncate block"><?= e($person['name']) ?></a>
                <p data-role="headline" class="text-xs text-slate-500 truncate"><?= e($person['headline'] ?: '') ?></p>
                <?php if ($person['company']): ?><p data-role="company" class="text-xs text-slate-500 truncate"><?= e($person['company']) ?></p><?php endif; ?>
                <?php if ($cohortText($person)): ?><p class="text-[0.68rem] text-slate-400 mt-0.5">Rome Business School &bull; <span data-role="cohort"><?= e($cohortText($person)) ?></span></p><?php endif; ?>
              </div>
              <?php if (location_display($person)): ?><p class="text-[0.68rem] text-slate-400 whitespace-nowrap"><i class="bi bi-geo-alt"></i> <span data-role="location"><?= e(location_display($person)) ?></span></p><?php endif; ?>
              <div class="flex gap-2 flex-shrink-0 w-full sm:w-auto">
                <form method="POST" action="<?= e(url('connections/' . $person['connection_id'] . '/decline')) ?>" class="ajax-action-form flex-1 sm:flex-none" data-on-success="remove-row" data-decrement="#incoming-heading,#requests-badge">
                  <?= csrf_field() ?>
                  <button type="submit" class="btn bg-white border border-slate-200 !text-slate-600 hover:bg-slate-50 !px-4 !py-1.5 text-xs w-full">Ignore</button>
                </form>
                <form method="POST" action="<?= e(url('connections/' . $person['connection_id'] . '/accept')) ?>" class="ajax-action-form flex-1 sm:flex-none" data-on-success="add-connection-card" data-decrement="#incoming-heading,#requests-badge">
                  <?= csrf_field() ?>
                  <button type="submit" class="btn-gold !px-4 !py-1.5 text-xs w-full">Accept</button>
                </form>
              </div>
            </div>
          <?php endforeach; ?>
          <p id="incoming-empty" class="text-sm text-slate-400 <?= empty($incoming) ? '' : 'hidden' ?>">No incoming requests right now.</p>
        </div>
      </div>

      <div class="card p-5">
        <h3 id="outgoing-heading" class="text-xs font-bold text-primary-navy uppercase tracking-wide mb-1">Sent Requests (<?= count($outgoing) ?>)</h3>
        <p class="text-xs text-slate-400 mb-4">Requests you've sent to connect</p>
        <div id="outgoing-list" class="space-y-4">
          <?php foreach ($outgoing as $person):
            $search = mb_strtolower($person['name'] . ' ' . $person['company'] . ' ' . $person['headline']);
          ?>
            <div class="flex flex-wrap items-center gap-3 pb-4 border-b border-slate-100 last:border-0 last:pb-0" data-search="<?= e($search) ?>">
              <a href="<?= e(url('alumni/' . $person['id'])) ?>" class="flex-shrink-0"><?= avatar_html($person, 'w-11 h-11') ?></a>
              <div class="min-w-0 flex-1">
                <a href="<?= e(url('alumni/' . $person['id'])) ?>" class="text-sm font-bold text-primary-navy hover:text-gold truncate block"><?= e($person['name']) ?></a>
                <p class="text-xs text-slate-500 truncate"><?= e($person['headline'] ?: '') ?></p>
                <?php if ($person['company']): ?><p class="text-xs text-slate-500 truncate"><?= e($person['company']) ?></p><?php endif; ?>
                <?php if ($cohortText($person)): ?><p class="text-[0.68rem] text-slate-400 mt-0.5">Rome Business School &bull; <?= e($cohortText($person)) ?></p><?php endif; ?>
              </div>
              <?php if (location_display($person)): ?><p class="text-[0.68rem] text-slate-400 whitespace-nowrap"><i class="bi bi-geo-alt"></i> <?= e(location_display($person)) ?></p><?php endif; ?>
              <span class="text-[0.65rem] font-semibold text-amber-600 flex items-center gap-1 whitespace-nowrap"><i class="bi bi-clock"></i> Pending</span>
              <form method="POST" action="<?= e(url('connections/' . $person['id'] . '/cancel')) ?>" class="ajax-action-form" data-on-success="remove-row" data-decrement="#outgoing-heading">
                <?= csrf_field() ?>
                <button type="submit" class="btn bg-white border border-red-200 !text-red-600 hover:bg-red-50 !px-4 !py-1.5 text-xs whitespace-nowrap">Cancel Request</button>
              </form>
            </div>
          <?php endforeach; ?>
          <p id="outgoing-empty" class="text-sm text-slate-400 <?= empty($outgoing) ? '' : 'hidden' ?>">No pending sent requests.</p>
        </div>
      </div>
    </div>
  </div>

  <!-- Suggested -->
  <div class="conn-tab-panel hidden" data-tab="suggested">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <?php foreach ($suggestions as $person):
        $search = mb_strtolower($person['name'] . ' ' . $person['company'] . ' ' . $person['headline']);
      ?>
        <div class="card p-5" data-search="<?= e($search) ?>">
          <a href="<?= e(url('alumni/' . $person['id'])) ?>" data-role="avatar" class="block mb-3"><?= avatar_html($person, 'w-11 h-11') ?></a>
          <a href="<?= e(url('alumni/' . $person['id'])) ?>" data-role="name" class="text-sm font-bold text-primary-navy hover:text-gold truncate block"><?= e($person['name']) ?></a>
          <p data-role="headline" class="text-xs text-slate-500 truncate"><?= e($person['headline'] ?: '') ?></p>
          <?php if ($person['company']): ?><p data-role="company" class="text-xs text-slate-600 font-medium truncate"><?= e($person['company']) ?></p><?php endif; ?>
          <?php if ($cohortText($person)): ?>
            <span data-role="cohort" class="inline-block mt-2 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-gold/10 text-gold"><?= e($cohortText($person)) ?></span>
          <?php endif; ?>
          <form method="POST" action="<?= e(url('connections/' . $person['id'] . '/request')) ?>" class="ajax-action-form mt-3" data-on-success="mark-pending">
            <?= csrf_field() ?>
            <button type="submit" class="btn-gold !py-1.5 text-xs w-full"><i class="bi bi-person-plus"></i> Connect</button>
          </form>
        </div>
      <?php endforeach; ?>
      <?php if (empty($suggestions)): ?>
        <p class="text-sm text-slate-400 col-span-full">No suggestions right now &mdash; check back later.</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Following -->
  <div class="conn-tab-panel hidden" data-tab="following">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
      <?php foreach ($following as $person):
        $search = mb_strtolower($person['name'] . ' ' . $person['company'] . ' ' . $person['headline']);
      ?>
        <div class="card p-5" data-search="<?= e($search) ?>">
          <a href="<?= e(url('alumni/' . $person['id'])) ?>" class="block mb-3"><?= avatar_html($person, 'w-11 h-11') ?></a>
          <a href="<?= e(url('alumni/' . $person['id'])) ?>" class="text-sm font-bold text-primary-navy hover:text-gold truncate block"><?= e($person['name']) ?></a>
          <p class="text-xs text-slate-500 truncate"><?= e($person['headline'] ?: '') ?></p>
          <?php if ($person['company']): ?><p class="text-xs text-slate-600 font-medium truncate"><?= e($person['company']) ?></p><?php endif; ?>
          <?php if ($cohortText($person)): ?>
            <span class="inline-block mt-2 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-gold/10 text-gold"><?= e($cohortText($person)) ?></span>
          <?php endif; ?>
          <form method="POST" action="<?= e(url('unfollow/' . $person['id'])) ?>" class="ajax-action-form mt-3" data-on-success="remove-card">
            <?= csrf_field() ?>
            <button type="submit" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !py-1.5 text-xs w-full"><i class="bi bi-eye-slash"></i> Unfollow</button>
          </form>
        </div>
      <?php endforeach; ?>
      <?php if (empty($following)): ?>
        <p class="text-sm text-slate-400 col-span-full">You're not following anyone yet.</p>
      <?php endif; ?>
    </div>
  </div>

  <div class="mt-8 card !bg-sky-50 !border-sky-100 p-4 flex flex-wrap items-center justify-between gap-3">
    <p class="text-xs text-sky-800 flex items-center gap-2"><i class="bi bi-shield-check text-sky-500"></i> Your privacy matters. Connections are only visible to you and the people you connect with.</p>
    <a href="<?= e(url('profile/edit') . '#privacy') ?>" class="text-xs font-semibold text-sky-700 hover:underline flex items-center gap-1">Manage Privacy Settings <i class="bi bi-chevron-right"></i></a>
  </div>
</div>

<script>
  (function () {
    function decrementCounter(el) {
      if (!el) return;
      var match = el.textContent.match(/\d+/);
      if (!match) return;
      var n = Math.max(0, parseInt(match[0], 10) - 1);
      el.textContent = el.textContent.replace(/\d+/, n);
      if (el.id === 'requests-badge') {
        el.classList.toggle('hidden', n === 0);
      }
    }

    function incrementCounter(el) {
      if (!el) return;
      var match = el.textContent.match(/\d+/);
      var n = match ? parseInt(match[0], 10) + 1 : 1;
      el.textContent = match ? el.textContent.replace(/\d+/, n) : el.textContent + ' (' + n + ')';
    }

    function revealEmptyStateIfNowEmpty(container) {
      if (!container || container.querySelectorAll('[data-search]').length > 0) return;
      var empty = container.querySelector('[id$="-empty"]');
      if (empty) empty.classList.remove('hidden');
    }

    function roleText(scope, role) {
      var el = scope.querySelector('[data-role="' + role + '"]');
      return el ? el.textContent.trim() : '';
    }

    function roleHtml(scope, role) {
      var el = scope.querySelector('[data-role="' + role + '"]');
      return el ? el.innerHTML : '';
    }

    function roleHref(scope, role) {
      var el = scope.querySelector('[data-role="' + role + '"]');
      return el ? el.getAttribute('href') : '#';
    }

    function csrfValue() {
      var input = document.querySelector('input[name="_csrf"]');
      return input ? input.value : '';
    }

    // Delegated so it also covers kebab menus on cards added dynamically (accepted requests).
    document.addEventListener('click', function (e) {
      var btn = e.target.closest('.kebab-btn');
      if (btn) {
        e.stopPropagation();
        var menu = btn.nextElementSibling;
        document.querySelectorAll('.kebab-menu').forEach(function (m) { if (m !== menu) m.classList.add('hidden'); });
        menu.classList.toggle('hidden');
        return;
      }
      document.querySelectorAll('.kebab-menu').forEach(function (m) { m.classList.add('hidden'); });
    });

    function addSentRequestRow(card, personId) {
      var list = document.getElementById('outgoing-list');
      if (!list) return;

      var name = roleText(card, 'name');
      var profileHref = roleHref(card, 'name');
      var avatarHtml = roleHtml(card, 'avatar');
      var headline = roleText(card, 'headline');
      var company = roleText(card, 'company');
      var cohort = roleText(card, 'cohort');
      var cancelAction = <?= json_encode(url('connections')) ?> + '/' + personId + '/cancel';

      var row = document.createElement('div');
      row.className = 'flex flex-wrap items-center gap-3 pb-4 border-b border-slate-100 last:border-0 last:pb-0';
      row.setAttribute('data-search', (name + ' ' + company + ' ' + headline).toLowerCase());
      row.innerHTML =
        '<a href="' + profileHref + '" class="flex-shrink-0">' + avatarHtml + '</a>' +
        '<div class="min-w-0 flex-1">' +
          '<a href="' + profileHref + '" class="text-sm font-bold text-primary-navy hover:text-gold truncate block">' + name + '</a>' +
          '<p class="text-xs text-slate-500 truncate">' + headline + '</p>' +
          (company ? '<p class="text-xs text-slate-500 truncate">' + company + '</p>' : '') +
          (cohort ? '<p class="text-[0.68rem] text-slate-400 mt-0.5">Rome Business School &bull; ' + cohort + '</p>' : '') +
        '</div>' +
        '<span class="text-[0.65rem] font-semibold text-amber-600 flex items-center gap-1 whitespace-nowrap"><i class="bi bi-clock"></i> Pending</span>' +
        '<form method="POST" action="' + cancelAction + '" class="ajax-action-form" data-on-success="remove-row" data-decrement="#outgoing-heading">' +
          '<input type="hidden" name="_csrf" value="' + csrfValue() + '">' +
          '<button type="submit" class="btn bg-white border border-red-200 !text-red-600 hover:bg-red-50 !px-4 !py-1.5 text-xs whitespace-nowrap">Cancel Request</button>' +
        '</form>';

      var emptyMsg = document.getElementById('outgoing-empty');
      if (emptyMsg) emptyMsg.classList.add('hidden');
      list.insertBefore(row, emptyMsg);
      incrementCounter(document.getElementById('outgoing-heading'));
    }

    function addConnectionCard(row) {
      var grid = document.getElementById('connections-results');
      if (!grid) return;

      var name = roleText(row, 'name');
      var profileHref = roleHref(row, 'name');
      var avatarHtml = roleHtml(row, 'avatar');
      var headline = roleText(row, 'headline');
      var company = roleText(row, 'company');
      var cohort = roleText(row, 'cohort');
      var location = roleText(row, 'location');
      var idMatch = profileHref.match(/\/alumni\/(\d+)/);
      var userId = idMatch ? idMatch[1] : '';
      var removeAction = <?= json_encode(url('connections')) ?> + '/' + userId + '/remove';
      var blockAction = <?= json_encode(url('connections')) ?> + '/' + userId + '/block';
      var messageAction = <?= json_encode(url('messages')) ?> + '/' + userId;

      var card = document.createElement('div');
      card.className = 'conn-card card p-5';
      card.setAttribute('data-search', (name + ' ' + company + ' ' + headline).toLowerCase());
      card.innerHTML =
        '<div class="flex items-start justify-between mb-3">' +
          '<a href="' + profileHref + '">' + avatarHtml + '</a>' +
          '<div class="relative">' +
            '<button type="button" class="kebab-btn text-slate-300 hover:text-primary-navy px-1" title="More options"><i class="bi bi-three-dots-vertical"></i></button>' +
            '<div class="kebab-menu hidden absolute right-0 top-full mt-1 z-20 bg-white border border-slate-200 rounded-lg shadow-lg w-44 py-1">' +
              '<a href="' + profileHref + '" class="flex items-center gap-2 px-3 py-2 text-xs text-slate-700 hover:bg-slate-50"><i class="bi bi-person w-4"></i> View Profile</a>' +
              '<form method="POST" action="' + blockAction + '" class="ajax-action-form" data-confirm="Block this connection? They won\'t be able to send you a connection request again." data-on-success="remove-card" data-decrement="#connections-count">' +
                '<input type="hidden" name="_csrf" value="' + csrfValue() + '">' +
                '<button type="submit" class="w-full flex items-center gap-2 text-left px-3 py-2 text-xs text-slate-700 hover:bg-slate-50"><i class="bi bi-slash-circle w-4"></i> Block Connection</button>' +
              '</form>' +
              '<form method="POST" action="' + removeAction + '" class="ajax-action-form" data-confirm="Remove this connection?" data-on-success="remove-card" data-decrement="#connections-count">' +
                '<input type="hidden" name="_csrf" value="' + csrfValue() + '">' +
                '<button type="submit" class="w-full flex items-center gap-2 text-left px-3 py-2 text-xs text-red-600 hover:bg-red-50"><i class="bi bi-trash w-4"></i> Remove Connection</button>' +
              '</form>' +
            '</div>' +
          '</div>' +
        '</div>' +
        '<a href="' + profileHref + '" class="text-sm font-bold text-primary-navy hover:text-gold truncate block">' + name + '</a>' +
        '<p class="text-xs text-slate-500 truncate">' + headline + '</p>' +
        (company ? '<p class="text-xs text-slate-600 font-medium truncate">' + company + '</p>' : '') +
        (location ? '<p class="text-[0.68rem] text-slate-400 mt-1"><i class="bi bi-geo-alt"></i> ' + location + '</p>' : '') +
        (cohort ? '<span class="inline-block mt-2 px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-gold/10 text-gold">' + cohort + '</span>' : '') +
        '<p class="text-[0.65rem] text-slate-400 mt-3 pt-3 border-t border-slate-100">Connected just now</p>' +
        '<a href="' + messageAction + '" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !py-1.5 text-xs w-full mt-2"><i class="bi bi-chat-dots"></i> Message</a>';

      var connectionsEmptyMsg = document.getElementById('connections-empty');
      if (connectionsEmptyMsg) connectionsEmptyMsg.classList.add('hidden');
      grid.insertBefore(card, grid.firstChild);
      incrementCounter(document.getElementById('connections-count'));
    }

    // Event delegation so this also covers rows added dynamically below (Sent Requests, My Connections).
    document.addEventListener('submit', function (e) {
      var form = e.target;
      if (!form.classList || !form.classList.contains('ajax-action-form')) return;

      // Forms with data-confirm are handled by the site-wide confirm modal (see the bottom of
      // the page layout) first; it re-submits with data-confirmed="1" once approved, and only
      // then do we take over here. This avoids stacking our own confirm() on top of that modal.
      if (form.hasAttribute('data-confirm') && form.dataset.confirmed !== '1') return;

      e.preventDefault();
      var onSuccess = form.dataset.onSuccess;
      var btn = form.querySelector('button[type="submit"]');

      fetch(form.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: new FormData(form)
      })
        .then(function (r) { return r.json(); })
        .then(function () {
          if (onSuccess === 'mark-pending') {
            btn.disabled = true;
            btn.innerHTML = '<i class="bi bi-clock"></i> Requested';
            btn.classList.remove('btn-gold');
            btn.classList.add('bg-slate-100', '!text-slate-500', 'cursor-default');

            var suggestedCard = form.closest('.card');
            var requestIdMatch = form.action.match(/\/connections\/(\d+)\/request/);
            if (suggestedCard && requestIdMatch) addSentRequestRow(suggestedCard, requestIdMatch[1]);
            return;
          }

          if (onSuccess === 'add-connection-card') {
            var incomingRow = form.closest('[data-search]');
            if (incomingRow) addConnectionCard(incomingRow);
            // fall through: still remove the row from Incoming and decrement its counters below
          }

          var target = onSuccess === 'remove-card' ? form.closest('.card') : form.closest('[data-search]');
          var parent = target ? target.parentElement : null;
          if (target) target.remove();
          revealEmptyStateIfNowEmpty(parent);

          if (form.dataset.decrement) {
            form.dataset.decrement.split(',').forEach(function (sel) {
              decrementCounter(document.querySelector(sel.trim()));
            });
          }
        });
    });

    var titles = {
      connections: ['My Connections', 'Stay connected. Grow together.'],
      requests: ['Connection Requests', 'Manage and respond to your connection requests.'],
      suggested: ['Suggested Connections', 'Alumni you may want to connect with.'],
      following: ['Following', 'Alumni whose activity you follow.']
    };
    var panels = document.querySelectorAll('.conn-tab-panel');
    var tabLinks = document.querySelectorAll('.conn-tab-link');
    var titleEl = document.getElementById('connections-title');
    var subtitleEl = document.getElementById('connections-subtitle');

    function activate(tab) {
      if (!titles[tab]) return;
      panels.forEach(function (p) { p.classList.toggle('hidden', p.dataset.tab !== tab); });
      tabLinks.forEach(function (b) {
        var isActive = b.dataset.tab === tab;
        b.classList.toggle('text-gold', isActive);
        b.classList.toggle('border-gold', isActive);
        b.classList.toggle('text-slate-500', !isActive);
        b.classList.toggle('border-transparent', !isActive);
      });
      titleEl.textContent = titles[tab][0];
      subtitleEl.textContent = titles[tab][1];
    }

    tabLinks.forEach(function (btn) {
      btn.addEventListener('click', function () {
        activate(btn.dataset.tab);
        history.replaceState(null, '', '#' + btn.dataset.tab);
      });
    });

    var initialTab = (location.hash || '').replace('#', '');
    if (initialTab && titles[initialTab]) {
      activate(initialTab);
    }

    // Clicking a nav link to #requests/#suggested/#following while already on this page is a
    // same-document navigation (no page load), so it only fires hashchange, not our own tab clicks.
    window.addEventListener('hashchange', function () {
      var tab = (location.hash || '').replace('#', '');
      if (tab && titles[tab]) activate(tab);
    });

    var loadMoreBtn = document.getElementById('load-more-connections');
    if (loadMoreBtn) {
      loadMoreBtn.addEventListener('click', function () {
        document.querySelectorAll('.conn-extra').forEach(function (el) { el.classList.remove('hidden'); });
        loadMoreBtn.remove();
      });
    }

    var searchInput = document.getElementById('connections-search');
    if (searchInput) {
      searchInput.addEventListener('input', function () {
        var q = searchInput.value.trim().toLowerCase();
        document.querySelectorAll('.conn-tab-panel [data-search]').forEach(function (el) {
          el.classList.toggle('hidden', q !== '' && el.dataset.search.indexOf(q) === -1);
        });
      });
    }

    var results = document.getElementById('connections-results');
    var gridBtn = document.getElementById('grid-view-btn');
    var listBtn = document.getElementById('list-view-btn');
    if (results && gridBtn && listBtn) {
      gridBtn.addEventListener('click', function () {
        results.className = 'grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4';
        gridBtn.classList.add('bg-gold', 'text-white');
        gridBtn.classList.remove('bg-white', 'text-slate-400');
        listBtn.classList.remove('bg-gold', 'text-white');
        listBtn.classList.add('bg-white', 'text-slate-400');
      });
      listBtn.addEventListener('click', function () {
        results.className = 'grid grid-cols-1 gap-3';
        listBtn.classList.add('bg-gold', 'text-white');
        listBtn.classList.remove('bg-white', 'text-slate-400');
        gridBtn.classList.remove('bg-gold', 'text-white');
        gridBtn.classList.add('bg-white', 'text-slate-400');
      });
    }
  })();
</script>

<script>
  (function () {
    // Polls for connection activity that happened on the OTHER end (someone sent you a
    // request, or accepted one you sent) and pulls the affected lists in — without a page
    // reload, and without touching the sort/grid-list controls (only their data swaps in).
    var pollUrl = <?= json_encode(url('connections/poll')) ?>;
    var selfUrl = <?= json_encode(url('connections')) ?>;
    var lastCounts = {
      incoming: <?= (int) count($incoming) ?>,
      outgoing: <?= (int) count($outgoing) ?>,
      connections: <?= (int) count($connections) ?>
    };

    function swapHtml(id, doc) {
      var fresh = doc.getElementById(id);
      var local = document.getElementById(id);
      if (fresh && local) local.innerHTML = fresh.innerHTML;
    }

    function swapText(id, doc) {
      var fresh = doc.getElementById(id);
      var local = document.getElementById(id);
      if (fresh && local) {
        local.textContent = fresh.textContent;
        local.className = fresh.className;
      }
    }

    function refreshFromServer(data) {
      fetch(selfUrl)
        .then(function (r) { return r.text(); })
        .then(function (html) {
          var doc = new DOMParser().parseFromString(html, 'text/html');

          if (data.incoming !== lastCounts.incoming || data.outgoing !== lastCounts.outgoing) {
            swapHtml('incoming-list', doc);
            swapHtml('outgoing-list', doc);
            swapText('incoming-heading', doc);
            swapText('outgoing-heading', doc);
            swapText('requests-badge', doc);
          }
          if (data.connections !== lastCounts.connections) {
            swapHtml('connections-results', doc);
            swapText('connections-count', doc);
          }

          lastCounts = data;
        });
    }

    function poll() {
      fetch(pollUrl, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          var changed = data.incoming !== lastCounts.incoming
            || data.outgoing !== lastCounts.outgoing
            || data.connections !== lastCounts.connections;
          if (changed) {
            refreshFromServer(data);
          } else {
            lastCounts = data;
          }
        });
    }

    setInterval(poll, 20000);
  })();
</script>

<script>
  (function () {
    // Live online/offline status for every avatar dot on the page — recomputed each cycle
    // since cards here can be swapped in by the connections-activity poll above.
    var onlinePollUrl = <?= json_encode(url('directory/online-status')) ?>;

    function applyOnlineStatus(id, online) {
      document.querySelectorAll('[data-online-dot="' + id + '"]').forEach(function (dot) {
        dot.classList.toggle('bg-green-500', online);
        dot.classList.toggle('bg-slate-300', !online);
        dot.title = online ? 'Online' : 'Offline';
      });
      document.querySelectorAll('[data-online-text="' + id + '"]').forEach(function (el) {
        el.className = online ? 'inline-flex items-center gap-1 text-green-600' : 'inline-flex items-center gap-1 text-slate-400';
        el.innerHTML = '<span class="w-1.5 h-1.5 rounded-full ' + (online ? 'bg-green-500' : 'bg-slate-300') + '"></span>' + (online ? 'Online' : 'Offline');
      });
    }

    function pollOnlineStatus() {
      var ids = new Set();
      document.querySelectorAll('[data-online-dot]').forEach(function (el) { ids.add(el.dataset.onlineDot); });
      document.querySelectorAll('[data-online-text]').forEach(function (el) { ids.add(el.dataset.onlineText); });
      if (ids.size === 0) return;

      fetch(onlinePollUrl + '?ids=' + Array.from(ids).join(','))
        .then(function (r) { return r.json(); })
        .then(function (data) {
          Object.keys(data).forEach(function (id) { applyOnlineStatus(id, data[id]); });
        })
        .catch(function () {});
    }

    setInterval(pollOnlineStatus, 20000);
  })();
</script>
