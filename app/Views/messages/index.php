<?php
$viewerId = (int) \App\Core\Auth::id();
$cohortText = function (array $user): string {
    return trim(($user['program'] ?? '') . ($user['graduation_year'] ? ' ' . $user['graduation_year'] : ''));
};

/** Groups a thread's messages into [dateLabel => messages[]], in chronological order. */
$groupByDay = function (array $messages): array {
    $groups = [];
    foreach ($messages as $m) {
        $day = date('Y-m-d', strtotime($m['created_at']));
        $today = date('Y-m-d');
        $yesterday = date('Y-m-d', strtotime('-1 day'));
        $label = $day === $today ? 'Today' : ($day === $yesterday ? 'Yesterday' : date('F j, Y', strtotime($m['created_at'])));
        $groups[$label][] = $m;
    }
    return $groups;
};
?>
<div class="max-w-[1400px] mx-auto px-4 md:px-8 py-4 md:py-8">

  <?php if ($partner): ?>
    <a href="<?= e($backUrl) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-flex items-center gap-1.5"><i class="fa-solid fa-arrow-left"></i> <?= e($backLabel) ?></a>
  <?php endif; ?>

  <div class="grid grid-cols-1 <?= $partner ? 'lg:grid-cols-[320px_1fr_300px]' : 'lg:grid-cols-[320px_1fr]' ?> gap-6 items-start">

    <!-- Conversation list: full width on mobile when no thread is open, hidden once one is (the thread takes over the screen) -->
    <div class="card overflow-hidden flex-col h-[calc(100vh-var(--header-height)-8rem)] lg:h-[75vh] <?= $partner ? 'hidden lg:flex' : 'flex' ?>">
      <div class="p-4 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
        <h2 class="text-base font-extrabold text-primary-navy">Messages</h2>
        <a href="<?= e(url('connections')) ?>" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-500 hover:text-gold hover:border-gold" title="Message a connection"><i class="bi bi-pencil-square"></i></a>
      </div>
      <div class="p-4 border-b border-slate-100 flex-shrink-0">
        <div class="relative">
          <i class="bi bi-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm"></i>
          <input type="text" id="conversation-search" placeholder="Search messages..." class="form-input !pl-9 text-sm">
        </div>
      </div>
      <div class="flex items-center gap-5 px-4 border-b border-slate-100 flex-shrink-0 overflow-x-auto overflow-y-hidden">
        <?php $unreadTotal = array_sum(array_column($conversations, 'unread_count')); ?>
        <button type="button" class="conv-tab-link text-xs font-semibold pb-2.5 border-b-2 -mb-px whitespace-nowrap text-gold border-gold" data-tab="all">All</button>
        <button type="button" class="conv-tab-link flex items-center gap-1.5 text-xs font-semibold pb-2.5 border-b-2 -mb-px whitespace-nowrap text-slate-500 border-transparent hover:text-primary-navy" data-tab="unread">Unread <?php if ($unreadTotal > 0): ?><span class="bg-gold text-white text-[10px] font-bold rounded-full w-4 h-4 flex items-center justify-center"><?= $unreadTotal ?></span><?php endif; ?></button>
        <button type="button" class="conv-tab-link text-xs font-semibold pb-2.5 border-b-2 -mb-px whitespace-nowrap text-slate-500 border-transparent hover:text-primary-navy" data-tab="connections">Connections</button>
        <button type="button" class="conv-tab-link text-xs font-semibold pb-2.5 border-b-2 -mb-px whitespace-nowrap text-slate-500 border-transparent hover:text-primary-navy" data-tab="requests">Requests</button>
      </div>

      <div id="conversation-list" class="flex-1 overflow-y-auto">
        <?php foreach ($conversations as $conv): $p = $conv['partner']; $last = $conv['last_message']; $isActive = $partner && (int) $partner['id'] === (int) $p['id']; ?>
          <a href="<?= e(url('messages/' . $p['id'])) ?>"
             class="conv-row flex items-start gap-3 px-4 py-3 border-b border-slate-50 last:border-0 <?= $isActive ? 'bg-gold/10' : 'hover:bg-slate-50' ?>"
             data-unread="<?= $conv['unread_count'] > 0 ? '1' : '0' ?>"
             data-search="<?= e(mb_strtolower($p['name'] . ' ' . ($last['body'] ?? ''))) ?>">
            <?= avatar_html($p, 'w-11 h-11') ?>
            <div class="min-w-0 flex-1">
              <div class="flex items-center justify-between gap-2">
                <span class="text-sm font-bold text-primary-navy truncate"><?= e($p['name']) ?></span>
                <span class="text-[0.65rem] text-slate-400 flex-shrink-0"><?= e(time_ago($last['created_at'])) ?></span>
              </div>
              <div class="flex items-center justify-between gap-2 mt-0.5">
                <p class="text-xs text-slate-500 truncate <?= $conv['unread_count'] > 0 ? 'font-semibold text-slate-700' : '' ?>">
                  <?= (int) $last['sender_id'] === $viewerId ? 'You: ' : '' ?><?= e($last['body']) ?>
                </p>
                <?php if ($conv['unread_count'] > 0): ?>
                  <span class="bg-gold text-white text-[0.65rem] font-bold w-5 h-5 rounded-full flex items-center justify-center flex-shrink-0"><?= $conv['unread_count'] ?></span>
                <?php endif; ?>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
        <?php if ($archived): ?>
          <p class="text-sm text-slate-400 text-center px-4 py-6">No archived conversations. Archiving a conversation isn't available yet.</p>
        <?php else: ?>
          <p id="conversations-empty" class="text-sm text-slate-400 text-center px-4 py-6 <?= empty($conversations) ? '' : 'hidden' ?>">No conversations yet. Message alumni from your <a href="<?= e(url('connections')) ?>" class="text-gold hover:underline">Connections</a> list.</p>
          <p id="requests-tab-empty" class="hidden text-sm text-slate-400 text-center px-4 py-6">Message requests will appear here. You can only message alumni you're connected with.</p>
        <?php endif; ?>
      </div>

      <div class="p-3 border-t border-slate-100 flex-shrink-0">
        <?php if ($archived): ?>
          <a href="<?= e(url('messages')) ?>" class="btn bg-white border border-slate-200 !text-slate-600 hover:bg-slate-50 !py-2 text-xs w-full"><i class="bi bi-arrow-left"></i> Back to Messages</a>
        <?php else: ?>
          <a href="<?= e(url('messages?archived=1')) ?>" class="btn bg-white border border-slate-200 !text-slate-600 hover:bg-slate-50 !py-2 text-xs w-full"><i class="bi bi-archive"></i> Archived Messages</a>
        <?php endif; ?>
      </div>
    </div>

    <!-- Thread: hidden on mobile until a conversation is selected, so the list gets the full screen until then -->
    <div class="card overflow-hidden flex-col h-[calc(100vh-var(--header-height)-8rem)] lg:h-[75vh] <?= $partner ? 'flex' : 'hidden lg:flex' ?>">
      <?php if ($partner): ?>
        <div class="p-4 border-b border-slate-100 flex items-center justify-between flex-shrink-0">
          <div class="flex items-center gap-3 min-w-0">
            <?= avatar_html($partner, 'w-10 h-10') ?>
            <div class="min-w-0">
              <a href="<?= e(url('alumni/' . $partner['id'])) ?>" class="text-sm font-bold text-primary-navy hover:text-gold truncate block"><?= e($partner['name']) ?></a>
              <span class="text-[0.68rem] font-semibold"><?= online_status_html($partner['last_active_at'] ?? null, (int) $partner['id']) ?></span>
            </div>
          </div>
          <div class="flex items-center gap-3 text-slate-400 flex-shrink-0">
            <i class="bi bi-telephone" title="Voice calling isn't available yet"></i>
            <button type="button" id="toggle-profile-panel" class="hover:text-gold" title="Conversation info"><i class="bi bi-info-circle"></i></button>
          </div>
        </div>

        <div id="message-thread" class="flex-1 overflow-y-auto p-4 flex flex-col gap-3" data-partner-id="<?= (int) $partner['id'] ?>" data-viewer-id="<?= $viewerId ?>">
          <?php foreach ($groupByDay($messages) as $dayLabel => $dayMessages): ?>
            <div class="flex items-center gap-3 my-1">
              <div class="flex-1 h-px bg-slate-100"></div>
              <span class="text-[0.65rem] text-slate-400 font-semibold"><?= e($dayLabel) ?></span>
              <div class="flex-1 h-px bg-slate-100"></div>
            </div>
            <?php foreach ($dayMessages as $m): $mine = (int) $m['sender_id'] === $viewerId; ?>
              <div class="flex <?= $mine ? 'justify-end' : 'justify-start' ?>" data-message-id="<?= (int) $m['id'] ?>">
                <div class="max-w-[75%] rounded-lg px-3 py-2 <?= $mine ? 'bg-gold/10 text-primary-navy' : 'bg-slate-100 text-slate-700' ?>">
                  <p class="text-sm whitespace-pre-line"><?= e($m['body']) ?></p>
                  <p class="text-[0.6rem] mt-1 flex items-center justify-end gap-1 <?= $mine ? 'text-slate-400' : 'text-slate-400' ?>">
                    <?= e(date('g:i A', strtotime($m['created_at']))) ?>
                    <?php if ($mine): ?>
                      <i class="bi <?= $m['read_at'] ? 'bi-check-all text-gold' : 'bi-check' ?>"></i>
                    <?php endif; ?>
                  </p>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endforeach; ?>
          <?php if (empty($messages)): ?>
            <p class="text-sm text-slate-400 text-center py-6 m-auto">Say hello to start the conversation.</p>
          <?php endif; ?>
        </div>

        <form method="POST" action="<?= e(url('messages/' . $partner['id'])) ?>" id="message-form" class="flex items-center gap-2 p-3 border-t border-slate-100 flex-shrink-0">
          <?= csrf_field() ?>
          <span class="text-slate-400 px-1" title="Attachments aren't available yet"><i class="bi bi-paperclip"></i></span>
          <input type="text" name="body" class="form-input flex-1" placeholder="Write a message..." required autocomplete="off">
          <button type="submit" class="btn-gold !px-4 !py-2 text-xs whitespace-nowrap">Send</button>
        </form>
      <?php else: ?>
        <div class="flex-1 flex flex-col items-center justify-center text-center px-6">
          <div class="w-14 h-14 rounded-full bg-gold/10 text-gold flex items-center justify-center text-2xl mb-3"><i class="bi bi-chat-dots"></i></div>
          <h3 class="text-sm font-bold text-primary-navy mb-1">Select a conversation</h3>
          <p class="text-xs text-slate-500">Choose someone from your messages, or start a new one from your <a href="<?= e(url('connections')) ?>" class="text-gold hover:underline">Connections</a>.</p>
        </div>
      <?php endif; ?>
    </div>

    <!-- Profile panel -->
    <?php if ($partner): ?>
      <div id="profile-panel" class="card p-5 hidden lg:block">
        <div class="text-center">
          <div class="inline-block relative"><?= avatar_html($partner, 'w-16 h-16') ?></div>
          <h3 class="text-sm font-bold text-primary-navy mt-2"><?= e($partner['name']) ?></h3>
          <span class="text-[0.68rem] font-semibold block"><?= online_status_html($partner['last_active_at'] ?? null, (int) $partner['id']) ?></span>
        </div>

        <div class="mt-4 text-xs text-slate-500 space-y-1.5">
          <?php if ($partner['headline'] || $partner['company']): ?>
            <p class="text-slate-700 font-medium"><?= e(trim(($partner['headline'] ?: '') . ($partner['company'] ? ' at ' . $partner['company'] : ''))) ?></p>
          <?php endif; ?>
          <?php if (location_display($partner)): ?><p><i class="bi bi-geo-alt"></i> <?= e(location_display($partner)) ?></p><?php endif; ?>
          <?php if ($cohortText($partner)): ?><p><i class="bi bi-mortarboard"></i> Rome Business School &bull; <?= e($cohortText($partner)) ?></p><?php endif; ?>
        </div>

        <?php if (!empty($partner['bio'])): ?>
          <div class="mt-4 pt-4 border-t border-slate-100">
            <h4 class="text-xs font-bold text-primary-navy uppercase tracking-wide mb-1.5">About</h4>
            <p class="text-xs text-slate-500 leading-relaxed"><?= e($partner['bio']) ?></p>
          </div>
        <?php endif; ?>

        <?php if ($mutualCount > 0): ?>
          <div class="mt-4 pt-4 border-t border-slate-100">
            <h4 class="text-xs font-bold text-primary-navy uppercase tracking-wide mb-2">Mutual Connections</h4>
            <div class="flex items-center -space-x-2">
              <?php foreach ($mutualConnections as $mu): ?>
                <?= avatar_html($mu, 'w-8 h-8 border-2 border-white') ?>
              <?php endforeach; ?>
              <?php if ($mutualCount > count($mutualConnections)): ?>
                <div class="w-8 h-8 rounded-full bg-slate-100 border-2 border-white flex items-center justify-center text-[0.6rem] font-bold text-slate-500">+<?= $mutualCount - count($mutualConnections) ?></div>
              <?php endif; ?>
            </div>
          </div>
        <?php endif; ?>

        <a href="<?= e(url('alumni/' . $partner['id'])) ?>" class="btn bg-white border border-gold !text-gold hover:bg-gold hover:!text-white !py-2 text-xs w-full mt-4">View Profile</a>

        <div class="mt-4 pt-4 border-t border-slate-100">
          <h4 class="text-xs font-bold text-primary-navy uppercase tracking-wide mb-2">Conversation Actions</h4>
          <div class="space-y-1">
            <a href="<?= e(url('alumni/' . $partner['id'])) ?>" class="flex items-center gap-2 px-2 py-1.5 rounded text-xs text-slate-600 hover:bg-slate-50"><i class="bi bi-person w-4"></i> View Full Profile</a>
            <button type="button" id="share-contact-btn" data-url="<?= e(url('alumni/' . $partner['id'])) ?>" class="flex items-center gap-2 px-2 py-1.5 rounded text-xs text-slate-600 hover:bg-slate-50 w-full text-left"><i class="bi bi-share w-4"></i> <span id="share-contact-label">Share Contact</span></button>
            <form method="POST" action="<?= e(url('connections/' . $partner['id'] . '/block')) ?>" data-confirm="Block <?= e($partner['name']) ?>? This removes your connection and they won't be able to message or reconnect with you.">
              <?= csrf_field() ?>
              <button type="submit" class="flex items-center gap-2 px-2 py-1.5 rounded text-xs text-red-600 hover:bg-red-50 w-full text-left"><i class="bi bi-shield-exclamation w-4"></i> Report / Block</button>
            </form>
          </div>
        </div>
      </div>
    <?php endif; ?>
  </div>
</div>

<script>
  (function () {
    var searchInput = document.getElementById('conversation-search');
    var tabLinks = document.querySelectorAll('.conv-tab-link');
    var rows = document.querySelectorAll('.conv-row');
    var emptyMsg = document.getElementById('conversations-empty');
    var requestsEmptyMsg = document.getElementById('requests-tab-empty');
    var activeTab = 'all';

    function applyFilters() {
      var q = (searchInput.value || '').trim().toLowerCase();
      var visibleCount = 0;

      requestsEmptyMsg.classList.toggle('hidden', activeTab !== 'requests');

      rows.forEach(function (row) {
        var matchesTab = activeTab === 'all' || activeTab === 'connections'
          || (activeTab === 'unread' && row.dataset.unread === '1');
        var matchesSearch = q === '' || row.dataset.search.indexOf(q) !== -1;
        var show = matchesTab && matchesSearch && activeTab !== 'requests';
        row.classList.toggle('hidden', !show);
        if (show) visibleCount++;
      });

      if (rows.length > 0) {
        emptyMsg.classList.toggle('hidden', visibleCount > 0 || activeTab === 'requests');
      }
    }

    tabLinks.forEach(function (btn) {
      btn.addEventListener('click', function () {
        activeTab = btn.dataset.tab;
        tabLinks.forEach(function (b) {
          var isActive = b === btn;
          b.classList.toggle('text-gold', isActive);
          b.classList.toggle('border-gold', isActive);
          b.classList.toggle('text-slate-500', !isActive);
          b.classList.toggle('border-transparent', !isActive);
        });
        applyFilters();
      });
    });

    if (searchInput) searchInput.addEventListener('input', applyFilters);

    var toggleBtn = document.getElementById('toggle-profile-panel');
    var profilePanel = document.getElementById('profile-panel');
    if (toggleBtn && profilePanel) {
      toggleBtn.addEventListener('click', function () {
        // An inline style beats the "hidden lg:block" responsive default at every breakpoint,
        // so this toggles correctly whether the panel started hidden (mobile) or shown (desktop).
        var isHidden = getComputedStyle(profilePanel).display === 'none';
        profilePanel.style.display = isHidden ? 'block' : 'none';
        // On mobile the panel renders below the thread (there's no room beside it), so bring it
        // into view instead of leaving the user to find it by scrolling.
        if (isHidden && window.innerWidth < 1024) {
          profilePanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
        }
      });
    }

    var shareBtn = document.getElementById('share-contact-btn');
    if (shareBtn) {
      shareBtn.addEventListener('click', function () {
        var url = location.origin + shareBtn.dataset.url;
        var label = document.getElementById('share-contact-label');
        navigator.clipboard.writeText(url).then(function () {
          label.textContent = 'Link copied!';
          setTimeout(function () { label.textContent = 'Share Contact'; }, 2000);
        });
      });
    }
  })();
</script>

<script>
  (function () {
    // Live online/offline status: covers the conversation list's avatar dots plus, when a
    // thread is open, the header and profile-panel status text — no page reload needed to
    // notice someone came online. Ids are recomputed each cycle in case the list changes.
    var pollUrl = <?= json_encode(url('directory/online-status')) ?>;

    function applyStatus(id, online) {
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

    function poll() {
      var ids = new Set();
      document.querySelectorAll('[data-online-dot]').forEach(function (el) { ids.add(el.dataset.onlineDot); });
      document.querySelectorAll('[data-online-text]').forEach(function (el) { ids.add(el.dataset.onlineText); });
      if (ids.size === 0) return;

      fetch(pollUrl + '?ids=' + Array.from(ids).join(','))
        .then(function (r) { return r.json(); })
        .then(function (data) {
          Object.keys(data).forEach(function (id) { applyStatus(id, data[id]); });
        })
        .catch(function () {});
    }

    setInterval(poll, 20000);
  })();
</script>

<?php if ($partner): ?>
<script>
  (function () {
    var thread = document.getElementById('message-thread');
    var partnerId = thread.dataset.partnerId;
    var viewerId = thread.dataset.viewerId;
    var pollUrl = <?= json_encode(url('messages/' . $partner['id'] . '/poll')) ?>;

    function lastMessageId() {
      var nodes = thread.querySelectorAll('[data-message-id]');
      if (!nodes.length) return 0;
      return nodes[nodes.length - 1].dataset.messageId;
    }

    function scrollToBottom() {
      thread.scrollTop = thread.scrollHeight;
    }

    function appendMessage(m) {
      var mine = String(m.sender_id) === String(viewerId);
      var wrap = document.createElement('div');
      wrap.className = 'flex ' + (mine ? 'justify-end' : 'justify-start');
      wrap.dataset.messageId = m.id;
      var bubble = document.createElement('div');
      bubble.className = 'max-w-[75%] rounded-lg px-3 py-2 ' + (mine ? 'bg-gold/10 text-primary-navy' : 'bg-slate-100 text-slate-700');
      var p = document.createElement('p');
      p.className = 'text-sm whitespace-pre-line';
      p.textContent = m.body;
      bubble.appendChild(p);
      wrap.appendChild(bubble);
      thread.appendChild(wrap);
    }

    function poll() {
      fetch(pollUrl + '?since=' + lastMessageId())
        .then(function (res) { return res.json(); })
        .then(function (data) {
          if (data.messages && data.messages.length) {
            data.messages.forEach(appendMessage);
            scrollToBottom();
          }
        })
        .catch(function () {});
    }

    scrollToBottom();
    setInterval(poll, 15000);
  })();
</script>
<?php endif; ?>
