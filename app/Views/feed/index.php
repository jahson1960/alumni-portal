<?php $authUser = \App\Core\Auth::user(); ?>
<div class="max-w-[1280px] mx-auto px-4 md:px-8 py-8">

  <div class="grid grid-cols-1 lg:grid-cols-[280px_1fr] gap-6 items-start">

    <!-- Sidebar -->
    <aside class="space-y-6 lg:sticky lg:top-[calc(var(--header-height)+1rem)]">
      <div>
        <h1 class="text-2xl font-extrabold text-primary-navy">Community Feed</h1>
        <p class="text-sm text-slate-500 mt-1">Connect, share, and learn from our global alumni community.</p>
      </div>

      <button type="button" id="scroll-to-composer" class="btn-gold !py-2.5 text-sm w-full flex items-center justify-center gap-2"><i class="bi bi-pencil-square"></i> Share an Update</button>

      <button type="button" id="sidebar-toggle" class="lg:hidden btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !py-2.5 text-sm w-full flex items-center justify-center gap-2 -mt-3">
        <i class="bi bi-list"></i> <span>Categories & Drafts</span>
        <i class="bi bi-chevron-down transition-transform" id="sidebar-toggle-icon"></i>
      </button>

      <div id="sidebar-collapsible" class="hidden lg:block space-y-6">
        <a href="<?= e(url('feed/drafts')) ?>" class="flex items-center justify-between gap-2 text-xs font-semibold text-slate-500 hover:text-gold px-1">
          <span class="flex items-center gap-1.5"><i class="bi bi-bookmark"></i> My Drafts</span>
          <?php if ($draftCount > 0): ?><span class="bg-slate-200 text-slate-600 text-[10px] font-bold rounded-full w-5 h-5 flex items-center justify-center"><?= $draftCount ?></span><?php endif; ?>
        </a>

        <div class="card p-2">
          <?php foreach ($categories as $key => $cat): ?>
            <a href="<?= e(url('feed') . '?' . http_build_query(array_filter(['type' => $key, 'sort' => $sort !== 'recent' ? $sort : null]))) ?>" class="flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-semibold <?= $activeType === $key ? 'bg-gold/10 text-gold' : 'text-slate-600 hover:bg-slate-50' ?>">
              <i class="bi <?= e($cat['icon']) ?>"></i> <?= e($cat['label']) ?>
            </a>
          <?php endforeach; ?>
        </div>

        <div class="card p-4 !bg-sky-50 !border-sky-100">
          <div class="flex items-center gap-2 mb-1.5">
            <i class="bi bi-shield-check text-sky-500"></i>
            <h3 class="text-xs font-bold text-primary-navy">Community Guidelines</h3>
          </div>
          <p class="text-xs text-slate-500 mb-2">Be respectful, supportive and helpful to fellow alumni.</p>
          <button type="button" id="guidelines-toggle" class="text-xs font-semibold text-sky-700 hover:underline flex items-center gap-1">View guidelines <i class="bi bi-arrow-right"></i></button>
          <div id="guidelines-full" class="hidden text-xs text-slate-500 mt-3 pt-3 border-t border-sky-100 space-y-1.5">
            <p>&bull; Keep posts relevant to alumni life, careers, and the RBSN community.</p>
            <p>&bull; No spam, harassment, or discriminatory content.</p>
            <p>&bull; Respect the privacy of fellow alumni &mdash; don't share others' details without consent.</p>
            <p>&bull; Job posts and business promotion belong in Careers &amp; Businesses, not the feed.</p>
          </div>
        </div>
      </div>
    </aside>

    <!-- Main -->
    <div>
      <div class="card p-6 mb-6" id="composer">
        <div class="flex flex-wrap items-start justify-between gap-4 pb-4 border-b border-slate-100">
          <div class="flex items-center gap-3">
            <div class="w-11 h-11 rounded-lg bg-gold/10 text-gold flex items-center justify-center flex-shrink-0 text-lg"><i class="bi bi-pencil-square"></i></div>
            <div>
              <h2 class="text-base font-bold text-primary-navy">Start a Discussion</h2>
              <p class="text-xs text-slate-500">Share updates, celebrate wins, ask questions or start a conversation with the alumni community.</p>
            </div>
          </div>
          <div class="relative flex-shrink-0">
            <i class="bi bi-globe absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-sm pointer-events-none"></i>
            <select name="visibility" form="composer-form" class="form-input !pl-9 text-xs !py-2">
              <option value="public">Public</option>
              <option value="connections">Connections Only</option>
            </select>
          </div>
        </div>

        <form method="POST" action="<?= e(url('feed')) ?>" enctype="multipart/form-data" id="composer-form" class="pt-4">
          <?= csrf_field() ?>
          <input type="hidden" name="post_type" id="composer-post-type" value="general">

          <div class="flex gap-3">
            <?= avatar_html($authUser, 'w-12 h-12') ?>
            <div class="flex-1 min-w-0">
              <textarea name="content" id="composer-content" rows="3" maxlength="2000" class="form-textarea !border-0 !ring-0 !shadow-none !p-0 text-sm resize-none" placeholder="What's on your mind?"></textarea>
              <div class="flex items-center justify-end gap-3 mt-1">
                <span id="composer-count" class="text-xs text-slate-400">0/2000</span>
                <div class="relative">
                  <button type="button" id="composer-emoji-btn" class="text-slate-400 hover:text-gold" title="Add an emoji"><i class="bi bi-emoji-smile"></i></button>
                  <div id="composer-emoji-panel" class="hidden absolute right-0 top-full mt-2 z-30 bg-white border border-slate-200 rounded-lg shadow-lg w-64 max-w-[calc(100vw-2rem)] overflow-hidden">
                    <div class="p-2 border-b border-slate-100">
                      <div class="relative">
                        <i class="bi bi-search absolute left-2.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
                        <input type="text" id="composer-emoji-search" placeholder="Search emoji..." autocomplete="off" class="form-input !pl-7 !py-1.5 text-xs">
                      </div>
                    </div>
                    <div id="composer-emoji-grid" class="grid grid-cols-7 gap-0.5 p-2 max-h-52 overflow-y-auto"></div>
                    <p id="composer-emoji-empty" class="hidden text-xs text-slate-400 text-center py-4">No emoji found.</p>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <div class="flex flex-wrap items-center gap-1.5 pt-4 mt-1 border-t border-slate-100">
            <?php foreach ($composerTypes as $key => $ct): ?>
              <button type="button" class="composer-type-btn flex items-center gap-1.5 px-3 py-2 rounded-lg text-xs font-semibold <?= $key === 'general' ? 'bg-gold/10 text-gold' : 'text-slate-500 hover:bg-slate-50' ?>" data-type="<?= e($key) ?>">
                <i class="bi <?= e($ct['icon']) ?>"></i> <?= e($ct['label']) ?>
              </button>
            <?php endforeach; ?>
          </div>

          <div class="flex flex-wrap items-center gap-3 pt-4 mt-4 border-t border-slate-100">
            <label id="composer-dropzone" class="flex-1 min-w-[220px] border border-dashed border-slate-300 rounded-lg px-4 py-3 flex items-center gap-2.5 text-slate-500 cursor-pointer hover:border-gold hover:text-gold">
              <i class="bi bi-paperclip"></i>
              <span>
                <span class="block text-sm font-semibold">Attach File</span>
                <span class="block text-[0.68rem] text-slate-400">Images only (Max 3MB)</span>
              </span>
              <input type="file" name="image" id="composer-image-input" accept=".jpg,.jpeg,.png,.webp" class="hidden">
            </label>

            <div id="composer-file-preview" class="hidden flex-1 min-w-[220px] border border-slate-200 rounded-lg px-4 py-3 items-center gap-3">
              <div class="w-9 h-9 rounded-lg bg-slate-100 flex items-center justify-center flex-shrink-0"><i class="bi bi-image text-slate-400"></i></div>
              <div class="min-w-0 flex-1">
                <p id="composer-file-name" class="text-sm font-medium text-slate-700 truncate"></p>
                <p id="composer-file-size" class="text-xs text-slate-400"></p>
              </div>
              <button type="button" id="composer-file-remove" class="text-slate-400 hover:text-red-500"><i class="bi bi-x-lg"></i></button>
            </div>
          </div>

          <div class="flex flex-wrap items-center justify-end gap-3 pt-5 mt-1">
            <button type="submit" name="action" value="draft" id="composer-draft-btn" class="btn bg-white border border-slate-200 !text-slate-600 hover:bg-slate-50 !px-4 !py-2.5 text-xs flex items-center gap-2" disabled><i class="bi bi-bookmark"></i> Save Draft</button>
            <button type="submit" name="action" value="post" id="composer-post-btn" class="btn-gold !px-5 !py-2.5 text-xs flex items-center gap-2" disabled><i class="bi bi-send"></i> Post</button>
          </div>
        </form>
      </div>

      <div class="flex flex-wrap items-center justify-between gap-3 mb-4">
        <p class="text-sm font-bold text-primary-navy">Latest from the community</p>
        <form method="GET" action="<?= e(url('feed')) ?>" id="sort-form" class="flex items-center gap-1.5">
          <input type="hidden" name="type" value="<?= e($activeType) ?>">
          <select name="sort" onchange="document.getElementById('sort-form').submit()" class="form-input !py-1.5 text-xs">
            <option value="recent" <?= $sort === 'recent' ? 'selected' : '' ?>>Most Recent</option>
            <option value="liked" <?= $sort === 'liked' ? 'selected' : '' ?>>Most Liked</option>
          </select>
        </form>
      </div>

      <div id="feed-posts" class="space-y-4">
        <?php foreach ($posts as $post): ?>
          <?php require dirname(__DIR__) . '/partials/post_card.php'; ?>
        <?php endforeach; ?>
      </div>
      <p id="feed-empty" class="text-sm text-slate-400 <?= empty($posts) ? '' : 'hidden' ?>">No posts yet &mdash; be the first to share something with the community.</p>

      <div id="feed-load-sentinel" class="py-6 text-center" data-page="<?= (int) $page ?>" data-has-more="<?= $hasMore ? '1' : '0' ?>" data-type="<?= e($activeType) ?>" data-sort="<?= e($sort) ?>">
        <?php if ($hasMore): ?>
          <i id="feed-load-spinner" class="fa-solid fa-spinner fa-spin text-slate-300 hidden"></i>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php require dirname(__DIR__) . '/partials/comment_modal.php'; ?>

<script>
  (function () {
    var sidebarToggle = document.getElementById('sidebar-toggle');
    var sidebarIcon = document.getElementById('sidebar-toggle-icon');
    var sidebar = document.getElementById('sidebar-collapsible');
    if (sidebarToggle && sidebar) {
      sidebarToggle.addEventListener('click', function () {
        var isOpen = sidebar.classList.toggle('hidden') === false;
        sidebarIcon.classList.toggle('rotate-180', isOpen);
      });
    }

    var content = document.getElementById('composer-content');
    var count = document.getElementById('composer-count');
    var emojiBtn = document.getElementById('composer-emoji-btn');
    var emojiPanel = document.getElementById('composer-emoji-panel');
    var emojiGrid = document.getElementById('composer-emoji-grid');
    var emojiSearch = document.getElementById('composer-emoji-search');
    var emojiEmpty = document.getElementById('composer-emoji-empty');
    var draftBtn = document.getElementById('composer-draft-btn');
    var postBtn = document.getElementById('composer-post-btn');
    var typeButtons = document.querySelectorAll('.composer-type-btn');
    var typeInput = document.getElementById('composer-post-type');
    var dropzone = document.getElementById('composer-dropzone');
    var imageInput = document.getElementById('composer-image-input');
    var filePreview = document.getElementById('composer-file-preview');
    var fileName = document.getElementById('composer-file-name');
    var fileSize = document.getElementById('composer-file-size');
    var fileRemove = document.getElementById('composer-file-remove');

    function syncSubmit() {
      var hasText = content.value.trim().length > 0;
      draftBtn.disabled = !hasText;
      postBtn.disabled = !hasText;
      count.textContent = content.value.length + '/2000';
    }
    content.addEventListener('input', syncSubmit);

    function insertAtCursor(text) {
      var start = content.selectionStart ?? content.value.length;
      var end = content.selectionEnd ?? content.value.length;
      content.value = content.value.slice(0, start) + text + content.value.slice(end);
      content.focus();
      content.selectionStart = content.selectionEnd = start + text.length;
      syncSubmit();
    }

    var EMOJIS = [
      ['😀', 'grinning happy smile'], ['😃', 'grinning happy smile'], ['😄', 'happy smile joy'], ['😁', 'grinning happy'],
      ['😆', 'laughing happy'], ['😅', 'sweat smile relief'], ['🤣', 'rofl laughing funny'], ['😂', 'joy laughing tears funny'],
      ['🙂', 'smile slight'], ['🙃', 'upside down silly'], ['😉', 'wink'], ['😊', 'smile blush happy'],
      ['😇', 'angel innocent halo'], ['🥰', 'love hearts smile'], ['😍', 'heart eyes love'], ['🤩', 'star eyes excited'],
      ['😘', 'kiss love'], ['😗', 'kiss'], ['😚', 'kiss closed eyes'], ['😙', 'kiss smile'],
      ['🥲', 'smile tear happy sad'], ['😋', 'yum tongue tasty'], ['😛', 'tongue playful'], ['😜', 'wink tongue silly'],
      ['🤪', 'zany crazy silly'], ['😝', 'tongue closed eyes'], ['🤑', 'money face rich'], ['🤗', 'hug hands'],
      ['🤭', 'giggle oops hand mouth'], ['🤫', 'shush quiet secret'], ['🤔', 'thinking hmm'], ['🤐', 'zipper mouth quiet secret'],
      ['🫡', 'salute respect'], ['🤨', 'raised eyebrow suspicious'], ['😐', 'neutral meh'], ['😑', 'expressionless blank'],
      ['😶', 'no mouth speechless'], ['🙄', 'eye roll annoyed'], ['😏', 'smirk sly'], ['😣', 'persevere struggle'],
      ['😥', 'sad relief disappointed'], ['😮', 'wow surprised open mouth'], ['😯', 'hushed surprised'],
      ['😪', 'sleepy tired'], ['😫', 'tired exhausted frustrated'], ['🥱', 'yawn tired bored'], ['😴', 'sleep zzz tired'],
      ['😌', 'relieved calm content'],
      ['🤤', 'drool hungry'], ['😒', 'unamused annoyed'], ['😓', 'sweat stressed'], ['😔', 'sad pensive down'],
      ['😕', 'confused unsure'], ['🙁', 'frown sad'], ['☹️', 'frown sad'], ['😖', 'confounded frustrated'],
      ['😞', 'disappointed sad'], ['😟', 'worried concerned'], ['😤', 'triumph frustrated proud'], ['😢', 'cry sad tear'],
      ['😭', 'sob crying loud sad'], ['😦', 'frown open mouth surprised'], ['😧', 'anguished shocked'], ['😨', 'fearful scared'],
      ['😩', 'weary tired frustrated'], ['🤯', 'mind blown shocked'], ['😬', 'grimace awkward'], ['😰', 'anxious sweat worried'],
      ['😱', 'scream shocked scared'], ['🥵', 'hot heat sweating'], ['🥶', 'cold freezing'], ['😳', 'flushed embarrassed'],
      ['😵', 'dizzy confused'], ['😵‍💫', 'dizzy spiral'], ['🤢', 'nauseated sick'],
      ['🤮', 'vomit sick'], ['🤧', 'sneeze sick'], ['😷', 'mask sick'], ['🤒', 'thermometer sick'],
      ['🤕', 'injured hurt bandage'], ['🥳', 'party celebrate birthday'], ['🥸', 'disguise glasses'], ['😎', 'cool sunglasses'],
      ['🤓', 'nerd glasses smart'], ['🧐', 'monocle curious'],
      ['👍', 'thumbs up like yes good'], ['👎', 'thumbs down dislike no bad'], ['👏', 'clap applause congrats'], ['🙌', 'praise hands celebrate yay'],
      ['🙏', 'pray thanks please hope'], ['👋', 'wave hello hi bye'], ['🤝', 'handshake deal partnership agree'], ['✌️', 'peace victory'],
      ['🤞', 'fingers crossed hope luck'], ['💪', 'muscle strong flex'], ['👊', 'fist bump'], ['✊', 'fist power raised'],
      ['👌', 'ok perfect'], ['🤙', 'call me shaka'], ['👆', 'point up'], ['👇', 'point down'],
      ['👉', 'point right'], ['👈', 'point left'], ['🖐️', 'hand stop wave'], ['✋', 'stop hand high five'],
      ['🤟', 'love you gesture'], ['🫶', 'heart hands love'], ['🤲', 'open hands offer'],
      ['🧠', 'brain smart idea'], ['👀', 'eyes look watching'], ['💡', 'idea lightbulb bright'], ['🔥', 'fire hot lit awesome'],
      ['✨', 'sparkles shiny magic'], ['⭐', 'star favorite'], ['🌟', 'glowing star special'], ['💯', 'hundred perfect score'],
      ['💥', 'boom explosion impact'], ['🚀', 'rocket launch fast growth'], ['🎉', 'party celebrate confetti'], ['🎊', 'confetti party celebrate'],
      ['🎈', 'balloon party celebrate'], ['🎁', 'gift present'], ['🏆', 'trophy win award winner'], ['🥇', 'gold medal first place win'],
      ['🥈', 'silver medal second place'], ['🥉', 'bronze medal third place'], ['🎓', 'graduation cap education degree'], ['📚', 'books study read learning'],
      ['📈', 'chart growth increase success'], ['📉', 'chart decline decrease'], ['📊', 'bar chart stats data'], ['💼', 'briefcase work job business'],
      ['💻', 'laptop computer work tech'], ['📱', 'phone mobile'], ['📝', 'memo note write'], ['✅', 'check done complete yes'],
      ['☑️', 'check box done'], ['✔️', 'check mark done'], ['❌', 'cross no wrong'], ['⚡', 'lightning fast energy'],
      ['🌍', 'globe world earth'], ['🌐', 'globe network world'], ['🗓️', 'calendar date schedule event'], ['📅', 'calendar date schedule'],
      ['⏰', 'alarm clock time reminder'], ['⌛', 'hourglass time waiting'], ['📌', 'pin important note'], ['📍', 'pin location'],
      ['🔗', 'link chain connect'], ['💬', 'speech bubble chat comment'], ['🗨️', 'chat message'], ['📣', 'megaphone announce loud'],
      ['📢', 'loudspeaker announce'], ['🔔', 'bell notification alert'], ['🎯', 'target goal aim'], ['🧩', 'puzzle piece solve'],
      ['❤️', 'red heart love'], ['🧡', 'orange heart love'], ['💛', 'yellow heart love'], ['💚', 'green heart love'],
      ['💙', 'blue heart love'], ['💜', 'purple heart love'], ['🖤', 'black heart love'], ['🤍', 'white heart love'],
      ['🤎', 'brown heart love'], ['💔', 'broken heart sad'], ['💕', 'two hearts love'], ['💖', 'sparkling heart love'],
      ['💗', 'growing heart love'], ['💓', 'beating heart love'], ['🎂', 'birthday cake celebrate'],
      ['☕', 'coffee drink break'], ['🍕', 'pizza food'], ['🍾', 'champagne celebrate party'], ['🥂', 'cheers toast drinks celebrate'],
      ['☀️', 'sun sunny weather'], ['🌈', 'rainbow colorful'], ['🌙', 'moon night'], ['⛅', 'cloud weather'],
      ['🇳🇬', 'nigeria flag'], ['🌎', 'earth world globe americas']
    ];

    function renderEmojis(list) {
      emojiGrid.innerHTML = '';
      list.forEach(function (item) {
        var btn = document.createElement('button');
        btn.type = 'button';
        btn.className = 'composer-emoji-option text-lg leading-none p-1.5 rounded hover:bg-slate-100';
        btn.title = item[1].split(' ')[0];
        btn.textContent = item[0];
        btn.addEventListener('click', function () {
          insertAtCursor(item[0]);
          emojiPanel.classList.add('hidden');
        });
        emojiGrid.appendChild(btn);
      });
      emojiEmpty.classList.toggle('hidden', list.length > 0);
    }
    renderEmojis(EMOJIS);

    emojiSearch.addEventListener('input', function () {
      var q = emojiSearch.value.trim().toLowerCase();
      if (q === '') {
        renderEmojis(EMOJIS);
        return;
      }
      renderEmojis(EMOJIS.filter(function (item) { return item[1].indexOf(q) !== -1; }));
    });

    emojiBtn.addEventListener('click', function (e) {
      e.stopPropagation();
      var willOpen = emojiPanel.classList.contains('hidden');
      emojiPanel.classList.toggle('hidden');
      if (willOpen) {
        emojiSearch.value = '';
        renderEmojis(EMOJIS);
        emojiSearch.focus();
      }
    });

    document.addEventListener('click', function (e) {
      if (!emojiPanel.classList.contains('hidden') && !emojiPanel.contains(e.target) && e.target !== emojiBtn) {
        emojiPanel.classList.add('hidden');
      }
    });

    typeButtons.forEach(function (btn) {
      btn.addEventListener('click', function () {
        typeInput.value = btn.dataset.type;
        typeButtons.forEach(function (b) {
          var active = b === btn;
          b.classList.toggle('bg-gold/10', active);
          b.classList.toggle('text-gold', active);
          b.classList.toggle('text-slate-500', !active);
        });
      });
    });

    imageInput.addEventListener('change', function () {
      var file = imageInput.files[0];
      if (!file) {
        return;
      }
      fileName.textContent = file.name;
      fileSize.textContent = Math.max(1, Math.round(file.size / 1024)) + ' KB';
      dropzone.classList.add('hidden');
      filePreview.classList.remove('hidden');
      filePreview.classList.add('flex');
    });

    fileRemove.addEventListener('click', function () {
      imageInput.value = '';
      filePreview.classList.add('hidden');
      filePreview.classList.remove('flex');
      dropzone.classList.remove('hidden');
    });

    document.getElementById('scroll-to-composer').addEventListener('click', function () {
      document.getElementById('composer').scrollIntoView({ behavior: 'smooth', block: 'center' });
      content.focus();
    });

    var guidelinesToggle = document.getElementById('guidelines-toggle');
    var guidelinesFull = document.getElementById('guidelines-full');
    guidelinesToggle.addEventListener('click', function () {
      guidelinesFull.classList.toggle('hidden');
    });

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
  })();
</script>

<script>
  (function () {
    // Like button — AJAX, so liking a post never navigates/scrolls the page.
    document.addEventListener('submit', function (e) {
      var form = e.target.closest('.like-form');
      if (!form) return;
      e.preventDefault();
      fetch(form.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: new FormData(form)
      })
        .then(function (r) { return r.json(); })
        .then(function (data) {
          var btn = form.querySelector('.like-btn');
          btn.classList.toggle('text-gold', data.liked);
          btn.classList.toggle('font-semibold', data.liked);
          btn.classList.toggle('hover:text-gold', !data.liked);
          btn.querySelector('.like-count').textContent = data.count;
          btn.querySelector('.like-label').textContent = 'Like' + (data.count === 1 ? '' : 's');
        })
        .catch(function () {});
    });

    // Follow/unfollow button — AJAX, so it never navigates/scrolls the page.
    document.addEventListener('submit', function (e) {
      var form = e.target.closest('.follow-form');
      if (!form) return;
      e.preventDefault();
      var willFollow = form.dataset.following !== '1';
      fetch(form.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: new FormData(form)
      })
        .then(function (r) { return r.json(); })
        .then(function () {
          form.dataset.following = willFollow ? '1' : '0';
          form.action = willFollow ? form.dataset.unfollowUrl : form.dataset.followUrl;
          var btn = form.querySelector('.follow-btn');
          btn.textContent = willFollow ? 'Following' : '+ Follow';
          btn.classList.toggle('border-slate-200', willFollow);
          btn.classList.toggle('!text-slate-500', willFollow);
          btn.classList.toggle('border-gold', !willFollow);
          btn.classList.toggle('!text-gold', !willFollow);
          btn.classList.toggle('hover:bg-gold', !willFollow);
          btn.classList.toggle('hover:!text-white', !willFollow);
        })
        .catch(function () {});
    });

    // Share popup (<details>) — close it when clicking anywhere outside.
    document.addEventListener('click', function (e) {
      document.querySelectorAll('.share-toggle[open]').forEach(function (d) {
        if (!d.contains(e.target)) d.removeAttribute('open');
      });
    });

    // Infinite scroll — replaces the old "Load More" button.
    var feedPosts = document.getElementById('feed-posts');
    var sentinel = document.getElementById('feed-load-sentinel');
    var loadSpinner = document.getElementById('feed-load-spinner');
    var feedListUrl = <?= json_encode(url('feed')) ?>;
    var loadingMore = false;

    function loadNextPage() {
      if (loadingMore || sentinel.dataset.hasMore !== '1') return;
      loadingMore = true;
      if (loadSpinner) loadSpinner.classList.remove('hidden');

      var nextPage = parseInt(sentinel.dataset.page, 10) + 1;
      var params = { page: nextPage };
      if (sentinel.dataset.type) params.type = sentinel.dataset.type;
      if (sentinel.dataset.sort && sentinel.dataset.sort !== 'recent') params.sort = sentinel.dataset.sort;

      fetch(feedListUrl + '?' + new URLSearchParams(params).toString(), {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
      })
        .then(function (r) {
          sentinel.dataset.hasMore = r.headers.get('X-Has-More') === '1' ? '1' : '0';
          return r.text();
        })
        .then(function (html) {
          feedPosts.insertAdjacentHTML('beforeend', html.trim());
          sentinel.dataset.page = nextPage;
          if (loadSpinner) loadSpinner.classList.toggle('hidden', sentinel.dataset.hasMore !== '1');
          loadingMore = false;
        })
        .catch(function () {
          loadingMore = false;
          if (loadSpinner) loadSpinner.classList.add('hidden');
        });
    }

    if (sentinel.dataset.hasMore === '1' && 'IntersectionObserver' in window) {
      var observer = new IntersectionObserver(function (entries) {
        entries.forEach(function (entry) {
          if (entry.isIntersecting) loadNextPage();
        });
      }, { rootMargin: '400px' });
      observer.observe(sentinel);
    }

    // Comment modal.
    var feedBaseUrl = <?= json_encode(url('feed/')) ?>;
    var modal = document.getElementById('comment-modal');
    var modalTitle = document.getElementById('comment-modal-title');
    var modalBody = document.getElementById('comment-modal-body');
    var modalForm = document.getElementById('comment-modal-form');
    var modalInput = document.getElementById('comment-modal-input');
    var modalClose = document.getElementById('comment-modal-close');
    var currentPostId = null;

    function syncCardCommentCount(postId) {
      var countEl = modalBody.querySelector('[data-modal-comment-count]');
      if (!countEl) return;
      var count = countEl.textContent.trim();
      document.querySelectorAll('[data-comment-count="' + postId + '"]').forEach(function (el) {
        el.textContent = count;
        var label = el.parentElement.querySelector('.comment-label');
        if (label) label.textContent = 'Comment' + (count === '1' ? '' : 's');
      });
    }

    function loadComments(postId) {
      modalBody.innerHTML = '<p class="text-sm text-slate-400 text-center py-6">Loading&hellip;</p>';
      fetch(feedBaseUrl + postId + '/comments', { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(function (r) { return r.text(); })
        .then(function (html) {
          modalBody.innerHTML = html;
          syncCardCommentCount(postId);
        })
        .catch(function () {
          modalBody.innerHTML = '<p class="text-sm text-red-500 text-center py-6">Couldn&rsquo;t load comments.</p>';
        });
    }

    function openModal(postId, author) {
      currentPostId = postId;
      modalTitle.textContent = author + "’s Post";
      modalForm.action = feedBaseUrl + postId + '/comment';
      modal.classList.remove('hidden');
      modal.classList.add('flex');
      document.body.style.overflow = 'hidden';
      loadComments(postId);
      modalInput.value = '';
      modalInput.focus();
    }

    function closeModal() {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
      document.body.style.overflow = '';
      currentPostId = null;
    }

    document.addEventListener('click', function (e) {
      var trigger = e.target.closest('.comment-trigger');
      if (trigger) {
        openModal(trigger.dataset.postId, trigger.dataset.author);
      }
    });

    modalClose.addEventListener('click', closeModal);
    modal.addEventListener('click', function (e) {
      if (e.target === modal) closeModal();
    });
    document.addEventListener('keydown', function (e) {
      if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
    });

    modalForm.addEventListener('submit', function (e) {
      e.preventDefault();
      var text = modalInput.value.trim();
      if (!text || !currentPostId) return;
      fetch(modalForm.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: new FormData(modalForm)
      })
        .then(function (r) { return r.text(); })
        .then(function (html) {
          modalBody.innerHTML = html;
          modalBody.scrollTop = modalBody.scrollHeight;
          syncCardCommentCount(currentPostId);
          modalInput.value = '';
        })
        .catch(function () {});
    });

    modalBody.addEventListener('submit', function (e) {
      var form = e.target.closest('.comment-delete-form');
      if (!form) return;
      e.preventDefault();
      if (!window.confirm('Delete this comment?')) return;
      fetch(form.action, {
        method: 'POST',
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        body: new FormData(form)
      })
        .then(function (r) { return r.text(); })
        .then(function (html) {
          modalBody.innerHTML = html;
          syncCardCommentCount(form.dataset.postId);
        })
        .catch(function () {});
    });
  })();
</script>
