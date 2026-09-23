<?php
$existingAreas = array_values(array_filter(array_map('trim', explode(',', (string) ($user['mentorship_areas'] ?? '')))));
?>
<div class="max-w-[1200px] mx-auto px-4 md:px-8 py-8">
  <a href="<?= e(url('mentorship')) ?>" class="text-sm text-slate-500 hover:text-gold mb-4 inline-flex items-center gap-2"><i class="fa-solid fa-arrow-left"></i> Back to Find a Mentor</a>

  <div class="grid grid-cols-1 lg:grid-cols-[1fr_360px] gap-8 items-center mb-8">
    <div>
      <h1 class="text-3xl md:text-4xl font-extrabold text-primary-navy">Become a Mentor</h1>
      <div class="w-12 h-1 bg-gold rounded-full my-3"></div>
      <p class="text-base font-semibold text-slate-700">Share your knowledge. Empower fellow alumni.</p>
      <p class="text-sm text-slate-500 mt-2 max-w-md">Join our mentor community and help fellow alumni navigate their careers, grow their skills, and achieve their goals.</p>
    </div>
    <div class="hidden lg:block">
      <svg viewBox="0 0 360 220" class="w-full h-auto">
        <ellipse cx="180" cy="120" rx="175" ry="95" fill="#EEF0F4"/>
        <path d="M20 205 Q40 180 30 160" stroke="#D9DEE7" stroke-width="6" fill="none" stroke-linecap="round"/>
        <path d="M28 195 Q45 175 55 178" stroke="#D9DEE7" stroke-width="6" fill="none" stroke-linecap="round"/>
        <rect x="40" y="175" width="280" height="10" rx="5" fill="#0B1F3A"/>
        <rect x="150" y="130" width="70" height="46" rx="6" fill="#E4E8EF"/>
        <rect x="158" y="138" width="54" height="30" rx="3" fill="#0B1F3A"/>
        <circle cx="95" cy="80" r="26" fill="#1E293B"/>
        <circle cx="95" cy="72" r="20" fill="#F0B45A"/>
        <path d="M75 62 Q95 40 115 62 L113 78 Q95 66 77 78 Z" fill="#2A2530"/>
        <rect x="66" y="95" width="58" height="70" rx="18" fill="#EFA23B"/>
        <rect x="80" y="150" width="30" height="30" rx="6" fill="#3A3F4B"/>
        <circle cx="255" cy="78" r="25" fill="#C9A876"/>
        <path d="M233 68 Q255 48 277 68 L275 82 Q255 70 235 82 Z" fill="#1B1B1B"/>
        <rect x="228" y="94" width="56" height="68" rx="16" fill="#12233F"/>
        <rect x="240" y="96" width="32" height="30" rx="4" fill="#F4F6F8"/>
        <rect x="180" y="55" width="42" height="26" rx="6" fill="#F0B45A"/>
        <circle cx="188" cy="68" r="2.5" fill="#0B1F3A"/>
        <circle cx="198" cy="68" r="2.5" fill="#0B1F3A"/>
        <circle cx="208" cy="68" r="2.5" fill="#0B1F3A"/>
        <path d="M195 81 L188 90 L202 81 Z" fill="#F0B45A"/>
        <rect x="150" y="88" width="34" height="22" rx="6" fill="#DCE3F0"/>
        <path d="M158 110 L152 118 L164 110 Z" fill="#DCE3F0"/>
      </svg>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-[1fr_320px] gap-6 items-start">
    <div class="card p-6">
      <div class="flex items-center gap-3 mb-5">
        <div class="w-10 h-10 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-user"></i></div>
        <h2 class="text-lg font-bold text-primary-navy">Mentor Profile</h2>
      </div>

      <?php require dirname(__DIR__) . '/partials/errors.php'; ?>

      <form method="POST" action="<?= e(url('mentorship/become')) ?>" class="space-y-5">
        <?= csrf_field() ?>

        <label class="rounded-lg border border-slate-200 bg-slate-50 p-4 flex flex-wrap items-center justify-between gap-4 cursor-pointer">
          <span class="flex items-start gap-3">
            <input type="checkbox" name="is_mentor" value="1" <?= !empty($user['is_mentor']) || empty($user['mentorship_areas']) ? 'checked' : '' ?> class="mt-0.5 w-5 h-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500 flex-shrink-0">
            <span>
              <span class="block text-sm font-semibold text-primary-navy">I'm available to mentor fellow alumni</span>
              <span class="block text-xs text-slate-500 mt-0.5">Your profile will be visible in the mentor directory once enabled.</span>
            </span>
          </span>
          <?php if ($sampleMentors): ?>
            <span class="flex items-center gap-3 flex-shrink-0">
              <span class="flex items-center">
                <?php foreach ($sampleMentors as $mentor): ?>
                  <span class="-ml-2 first:ml-0"><?= avatar_html($mentor, 'w-8 h-8 ring-2 ring-white') ?></span>
                <?php endforeach; ?>
              </span>
              <span>
                <span class="block text-sm font-extrabold text-primary-navy leading-none"><?= (int) $mentorCount ?>+</span>
                <span class="block text-[0.65rem] text-slate-400">mentors in our community</span>
              </span>
            </span>
          <?php endif; ?>
        </label>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div>
            <label class="form-label flex items-center gap-1.5" for="mentorship-tag-entry">Mentorship Areas <i class="fa-regular fa-circle-question text-slate-300 text-xs" title="Add areas of expertise you can mentor in"></i></label>
            <div id="mentorship-tags-input" class="form-input flex flex-wrap items-center gap-2 cursor-text">
              <?php foreach ($existingAreas as $tag): ?>
                <span class="tag-chip inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-700 text-xs font-semibold pl-2.5 pr-1.5 py-1 rounded-full" data-value="<?= e($tag) ?>"><?= e($tag) ?> <button type="button" class="tag-remove text-indigo-400 hover:text-indigo-700 leading-none">&times;</button></span>
              <?php endforeach; ?>
              <input type="text" id="mentorship-tag-entry" class="flex-1 min-w-[100px] border-0 outline-none text-sm bg-transparent py-0.5" placeholder="<?= empty($existingAreas) ? 'Type and press Enter...' : '' ?>">
            </div>
            <input type="hidden" name="mentorship_areas" id="mentorship_areas" value="<?= e($user['mentorship_areas'] ?? '') ?>">
            <p class="text-xs text-slate-400 mt-1">Add areas of expertise separated by commas. These will appear as tags on your profile.</p>
          </div>

          <div>
            <label class="form-label" for="mentor_availability">Availability</label>
            <select id="mentor_availability" name="mentor_availability" class="form-input">
              <?php foreach ($availabilityOptions as $key => $label): ?>
                <option value="<?= e($key) ?>" <?= ($user['mentor_availability'] ?? 'open') === $key ? 'selected' : '' ?>><?= e($label) ?></option>
              <?php endforeach; ?>
            </select>
            <p class="text-xs text-slate-400 mt-1">Set your availability status for mentorship requests.</p>
          </div>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
          <div class="rounded-lg bg-indigo-50 border border-indigo-100 p-4 flex items-start gap-3">
            <div class="w-9 h-9 rounded-lg bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0"><i class="fa-regular fa-eye"></i></div>
            <div>
              <p class="text-sm font-bold text-indigo-700">Visible to Alumni</p>
              <p class="text-xs text-slate-500 mt-1">Your profile will appear in the mentor directory and can be discovered by alumni.</p>
            </div>
          </div>
          <div class="rounded-lg bg-emerald-50 border border-emerald-100 p-4 flex items-start gap-3">
            <div class="w-9 h-9 rounded-lg bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-shield-halved"></i></div>
            <div>
              <p class="text-sm font-bold text-emerald-700">You're in Control</p>
              <p class="text-xs text-slate-500 mt-1">You can update your availability or pause mentorship anytime from your dashboard.</p>
            </div>
          </div>
        </div>

        <div class="flex flex-wrap items-center justify-between gap-4 pt-2">
          <p class="text-xs text-slate-400 flex items-center gap-2"><i class="fa-solid fa-lock"></i> All information is private and will only be shared as per your preferences.</p>
          <button type="submit" class="btn-gold !px-6 !py-3 text-sm flex items-center gap-2 flex-shrink-0"><?= !empty($user['is_mentor']) ? 'Update Mentor Profile' : 'Become a Mentor' ?> <i class="fa-solid fa-arrow-right"></i></button>
        </div>
      </form>

      <?php if (!empty($user['is_mentor'])): ?>
        <form method="POST" action="<?= e(url('mentorship/become')) ?>" class="mt-4" data-confirm="Stop appearing in the mentor directory?">
          <?= csrf_field() ?>
          <input type="hidden" name="mentorship_areas" value="<?= e($user['mentorship_areas'] ?? '') ?>">
          <button type="submit" class="text-xs text-red-500 hover:underline">Remove me from the mentor directory</button>
        </form>
      <?php endif; ?>
    </div>

    <div>
      <div class="card p-5 mb-5">
        <h3 class="text-base font-bold text-primary-navy mb-4">Why become a mentor?</h3>
        <div class="space-y-4">
          <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-people-group"></i></div>
            <div>
              <p class="text-sm font-bold text-primary-navy">Make an Impact</p>
              <p class="text-xs text-slate-500 mt-0.5">Help alumni gain clarity, confidence and achieve their career goals.</p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-book-open"></i></div>
            <div>
              <p class="text-sm font-bold text-primary-navy">Share Your Expertise</p>
              <p class="text-xs text-slate-500 mt-0.5">Give back by sharing your knowledge and real-world experience.</p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-people-roof"></i></div>
            <div>
              <p class="text-sm font-bold text-primary-navy">Build Connections</p>
              <p class="text-xs text-slate-500 mt-0.5">Expand your network and create meaningful professional relationships.</p>
            </div>
          </div>
          <div class="flex items-start gap-3">
            <div class="w-10 h-10 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-award"></i></div>
            <div>
              <p class="text-sm font-bold text-primary-navy">Enhance Your Leadership</p>
              <p class="text-xs text-slate-500 mt-0.5">Strengthen your mentoring and leadership skills.</p>
            </div>
          </div>
        </div>
      </div>

      <div class="card p-5 !bg-indigo-50 !border-indigo-100">
        <i class="fa-solid fa-quote-left text-indigo-300 text-2xl"></i>
        <p class="text-sm text-indigo-900 italic mt-2 leading-relaxed">Mentorship is a two-way street. You grow as much as those you guide.</p>
      </div>
    </div>
  </div>
</div>

<script>
  (function () {
    var container = document.getElementById('mentorship-tags-input');
    var entry = document.getElementById('mentorship-tag-entry');
    var hidden = document.getElementById('mentorship_areas');
    if (!container || !entry || !hidden) return;

    function syncHidden() {
      var tags = Array.prototype.map.call(container.querySelectorAll('.tag-chip'), function (chip) { return chip.dataset.value; });
      hidden.value = tags.join(', ');
    }

    function addTag(value) {
      value = value.trim().replace(/,+$/, '').trim();
      if (!value) return;
      var existing = Array.prototype.map.call(container.querySelectorAll('.tag-chip'), function (c) { return c.dataset.value.toLowerCase(); });
      if (existing.indexOf(value.toLowerCase()) !== -1) return;
      var chip = document.createElement('span');
      chip.className = 'tag-chip inline-flex items-center gap-1.5 bg-indigo-50 text-indigo-700 text-xs font-semibold pl-2.5 pr-1.5 py-1 rounded-full';
      chip.dataset.value = value;
      var label = document.createElement('span');
      label.textContent = value;
      var btn = document.createElement('button');
      btn.type = 'button';
      btn.className = 'tag-remove text-indigo-400 hover:text-indigo-700 leading-none';
      btn.innerHTML = '&times;';
      chip.appendChild(label);
      chip.appendChild(btn);
      container.insertBefore(chip, entry);
      syncHidden();
    }

    entry.addEventListener('keydown', function (e) {
      if (e.key === 'Enter' || e.key === ',') {
        e.preventDefault();
        addTag(entry.value);
        entry.value = '';
      } else if (e.key === 'Backspace' && entry.value === '') {
        var chips = container.querySelectorAll('.tag-chip');
        if (chips.length) chips[chips.length - 1].remove();
        syncHidden();
      }
    });
    entry.addEventListener('blur', function () {
      if (entry.value.trim()) {
        addTag(entry.value);
        entry.value = '';
      }
    });
    container.addEventListener('click', function (e) {
      if (e.target.classList.contains('tag-remove')) {
        e.target.closest('.tag-chip').remove();
        syncHidden();
      } else if (e.target === container) {
        entry.focus();
      }
    });
  })();
</script>
