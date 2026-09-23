<?php
$isPast = $event['event_date'] < date('Y-m-d');
$titleWords = explode(' ', $event['title']);
$titleAccent = array_pop($titleWords);
$titleLead = implode(' ', $titleWords);
$eventUrl = url('events/' . $event['id']);
$tagline = $event['description'] ? mb_strimwidth($event['description'], 0, 70, '…') : null;
?>
<div class="max-w-[1120px] mx-auto px-4 md:px-8 py-8">
  <a href="<?= e(url('events')) ?>" class="text-sm text-slate-500 hover:text-gold mb-4 inline-flex items-center gap-2"><i class="fa-solid fa-arrow-left"></i> Back to Events</a>

  <div class="card p-5 md:p-6 mb-6 relative">
    <button type="button" id="share-jump" class="absolute right-5 top-5 md:right-6 md:top-6 w-9 h-9 rounded-lg border border-slate-200 flex items-center justify-center text-slate-500 hover:text-gold hover:border-gold z-10" title="Share this event"><i class="fa-solid fa-share-nodes"></i></button>

    <div class="grid grid-cols-1 lg:grid-cols-[0.85fr_1.15fr] gap-6">
      <div class="rounded-xl bg-gradient-to-br from-primary-navy to-slate-900 p-5 min-h-[260px] flex flex-col justify-between relative overflow-hidden">
        <div class="bg-white rounded-lg px-3.5 py-2.5 w-fit shadow-sm">
          <p class="text-[0.6rem] font-bold text-slate-400 uppercase tracking-wide"><?= e(format_date($event['event_date'], 'l')) ?></p>
          <p class="text-xs font-extrabold text-gold uppercase"><?= e(format_date($event['event_date'], 'M')) ?></p>
          <p class="text-2xl font-extrabold text-primary-navy leading-none mt-0.5"><?= e(format_date($event['event_date'], 'd')) ?></p>
        </div>
        <div>
          <h2 class="text-2xl font-extrabold uppercase leading-tight text-white">
            <?= e($titleLead) ?> <?php if ($titleLead !== ''): ?><br><?php endif; ?><span class="text-gold"><?= e($titleAccent) ?></span>
          </h2>
          <?php if ($tagline): ?>
            <p class="text-xs text-slate-300 mt-3 flex items-center gap-2"><i class="fa-solid fa-gift text-gold"></i> <?= e($tagline) ?></p>
          <?php endif; ?>
        </div>
      </div>

      <div>
        <span class="badge <?= $isPast ? 'bg-slate-100 text-slate-500' : 'bg-amber-100 text-amber-700' ?> uppercase tracking-wide mb-3 inline-block"><?= $isPast ? 'Past Event' : 'Upcoming Event' ?></span>
        <h1 class="text-2xl font-extrabold text-primary-navy pr-10"><?= e($event['title']) ?></h1>
        <p class="text-sm text-slate-500 mt-2 flex items-center gap-2"><i class="fa-regular fa-calendar text-slate-400"></i> <?= e(format_date($event['event_date'], 'l, F j, Y')) ?></p>

        <div class="flex flex-wrap items-start gap-x-6 gap-y-3 mt-4 pt-4 border-t border-slate-100">
          <span class="flex items-center gap-2 text-sm text-slate-600"><i class="fa-solid fa-location-dot text-slate-400"></i> <?= e($event['is_virtual'] ? 'Virtual' : ($event['location'] ?: 'TBA')) ?></span>
          <span class="flex items-center gap-2 text-sm text-slate-600"><i class="fa-regular fa-clock text-slate-400"></i> <?= e($event['event_time'] ?: 'TBA') ?></span>
          <div class="flex items-start gap-2">
            <i class="fa-solid fa-user-group text-slate-400 mt-0.5"></i>
            <div>
              <p class="text-sm text-slate-600"><?= count($attendees) ?> registered</p>
              <?php if (empty($attendees) && !$isPast): ?><p class="text-xs font-semibold text-gold">Be the first!</p><?php endif; ?>
            </div>
          </div>
        </div>

        <h3 class="section-title text-xs mt-5 mb-2">About This Event</h3>
        <p class="text-sm text-slate-600 whitespace-pre-line"><?= e($event['description']) ?></p>

        <?php if (!$isPast): ?>
          <div class="flex flex-wrap items-center gap-3 mt-6">
            <form method="POST" action="<?= e(url('events/' . $event['id'] . '/rsvp')) ?>">
              <?= csrf_field() ?>
              <button type="submit" class="btn <?= $isAttending ? 'bg-emerald-100 text-emerald-700' : 'btn-gold' ?> !px-5 !py-2.5 text-sm flex items-center gap-2">
                <?= $isAttending ? '✓ Registered' : 'Register Now' ?> <?php if (!$isAttending): ?><i class="fa-solid fa-arrow-right"></i><?php endif; ?>
              </button>
            </form>
            <a href="<?= e(url('events/' . $event['id'] . '/ics')) ?>" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-5 !py-2.5 text-sm flex items-center gap-2"><i class="fa-regular fa-calendar-plus"></i> Add to Calendar</a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-[1fr_340px] gap-6 items-start">
    <div class="card p-5 md:p-6">
      <h3 class="text-sm font-bold text-primary-navy mb-1">Event Details</h3>
      <div class="w-8 h-0.5 bg-gold rounded-full mb-4"></div>

      <div class="space-y-3.5 mb-5">
        <div class="flex items-center gap-3 text-sm">
          <i class="fa-regular fa-calendar w-4 text-slate-400"></i>
          <span class="text-slate-400 w-24 flex-shrink-0">Date</span>
          <span class="text-slate-700 font-medium"><?= e(format_date($event['event_date'], 'l, F j, Y')) ?></span>
        </div>
        <div class="flex items-center gap-3 text-sm">
          <i class="fa-regular fa-clock w-4 text-slate-400"></i>
          <span class="text-slate-400 w-24 flex-shrink-0">Time</span>
          <span class="text-slate-700 font-medium"><?= e($event['event_time'] ?: 'TBA') ?></span>
        </div>
        <div class="flex items-center gap-3 text-sm">
          <i class="fa-solid fa-location-dot w-4 text-slate-400"></i>
          <span class="text-slate-400 w-24 flex-shrink-0">Location</span>
          <span class="text-slate-700 font-medium">
            <?= e($event['is_virtual'] ? 'Virtual' : ($event['location'] ?: 'TBA')) ?>
            <?php if (!$event['is_virtual'] && $event['location']): ?>
              <a href="#event-map" class="block text-xs text-sky-600 hover:underline">View on map</a>
            <?php endif; ?>
          </span>
        </div>
        <div class="flex items-center gap-3 text-sm">
          <i class="fa-solid fa-people-group w-4 text-slate-400"></i>
          <span class="text-slate-400 w-24 flex-shrink-0">Event Type</span>
          <span class="text-slate-700 font-medium"><?= $event['is_virtual'] ? 'Virtual' : 'In-Person' ?></span>
        </div>
        <div class="flex items-center gap-3 text-sm">
          <i class="fa-solid fa-tag w-4 text-slate-400"></i>
          <span class="text-slate-400 w-24 flex-shrink-0">Category</span>
          <span class="text-slate-700 font-medium"><?= e($event['category'] ?: 'General') ?></span>
        </div>
      </div>

      <div id="event-map" class="scroll-mt-24">
        <?php if (!$event['is_virtual'] && $event['location']): ?>
          <div class="rounded-lg overflow-hidden border border-slate-200 h-[180px]">
            <iframe src="https://www.google.com/maps?q=<?= urlencode($event['location']) ?>&output=embed" class="w-full h-full border-0" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Map showing <?= e($event['location']) ?>"></iframe>
          </div>
          <p class="text-sm font-semibold text-primary-navy mt-2"><?= e($event['location']) ?></p>
          <a href="https://www.google.com/maps/dir/?api=1&destination=<?= urlencode($event['location']) ?>" target="_blank" rel="noopener noreferrer" class="text-xs text-sky-600 hover:underline">Get Directions &rarr;</a>
        <?php else: ?>
          <div class="rounded-lg border border-dashed border-slate-200 h-[140px] flex flex-col items-center justify-center text-slate-400 gap-2">
            <i class="fa-solid fa-video text-2xl"></i>
            <p class="text-xs">This is a virtual event</p>
          </div>
        <?php endif; ?>
      </div>
    </div>

    <div>
      <div class="card p-5 mb-5">
        <h3 class="text-sm font-bold text-primary-navy mb-1">Who's Registered (<?= count($attendees) ?>)</h3>
        <div class="w-8 h-0.5 bg-gold rounded-full mb-4"></div>

        <?php if (empty($attendees)): ?>
          <div class="text-center py-2">
            <div class="w-11 h-11 rounded-full bg-amber-100 text-gold flex items-center justify-center mx-auto mb-3"><i class="fa-solid fa-user-group"></i></div>
            <p class="text-sm font-semibold text-primary-navy">No one has registered yet.</p>
            <p class="text-xs text-slate-400 mt-1 mb-4">Be the first to register and join!</p>
            <?php if (!$isPast): ?>
              <form method="POST" action="<?= e(url('events/' . $event['id'] . '/rsvp')) ?>">
                <?= csrf_field() ?>
                <button type="submit" class="btn bg-white border border-gold !text-gold hover:bg-gold/5 !px-4 !py-2 text-xs w-full"><?= $isAttending ? '✓ Registered' : 'Register Now' ?></button>
              </form>
            <?php endif; ?>
          </div>
        <?php else: ?>
          <div class="grid grid-cols-2 gap-3">
            <?php foreach (array_slice($attendees, 0, 6) as $person): ?>
              <a href="<?= e(url('alumni/' . $person['id'])) ?>" class="flex items-center gap-2 min-w-0">
                <?= avatar_html($person, 'w-8 h-8 flex-shrink-0') ?>
                <span class="text-xs text-slate-700 truncate"><?= e($person['name']) ?></span>
              </a>
            <?php endforeach; ?>
          </div>
          <?php if (count($attendees) > 6): ?>
            <p class="text-xs text-slate-400 mt-3">+<?= count($attendees) - 6 ?> more</p>
          <?php endif; ?>
        <?php endif; ?>
      </div>

      <div id="share-event" class="card p-5 scroll-mt-24">
        <h3 class="text-sm font-bold text-primary-navy mb-1">Share this event</h3>
        <div class="w-8 h-0.5 bg-gold rounded-full mb-4"></div>
        <div class="flex items-center gap-2.5">
          <a href="mailto:?subject=<?= urlencode($event['title']) ?>&body=<?= urlencode($eventUrl) ?>" class="w-9 h-9 rounded-full border border-slate-200 flex items-center justify-center text-slate-500 hover:text-gold hover:border-gold" title="Share by email"><i class="fa-regular fa-envelope"></i></a>
          <a href="https://www.linkedin.com/sharing/share-offsite/?url=<?= urlencode($eventUrl) ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full border border-slate-200 flex items-center justify-center text-slate-500 hover:text-sky-700 hover:border-sky-700" title="Share on LinkedIn"><i class="fa-brands fa-linkedin-in"></i></a>
          <a href="https://twitter.com/intent/tweet?url=<?= urlencode($eventUrl) ?>&text=<?= urlencode($event['title']) ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full border border-slate-200 flex items-center justify-center text-slate-500 hover:text-sky-500 hover:border-sky-500" title="Share on X"><i class="fa-brands fa-x-twitter"></i></a>
          <a href="https://wa.me/?text=<?= urlencode($event['title'] . ' ' . $eventUrl) ?>" target="_blank" rel="noopener noreferrer" class="w-9 h-9 rounded-full border border-slate-200 flex items-center justify-center text-slate-500 hover:text-emerald-600 hover:border-emerald-600" title="Share on WhatsApp"><i class="fa-brands fa-whatsapp"></i></a>
          <button type="button" id="copy-event-link" data-url="<?= e($eventUrl) ?>" class="w-9 h-9 rounded-full border border-slate-200 flex items-center justify-center text-slate-500 hover:text-gold hover:border-gold" title="Copy link"><i class="fa-solid fa-link"></i></button>
        </div>
        <p id="copy-event-link-msg" class="hidden text-xs text-emerald-600 mt-2">Link copied!</p>
      </div>
    </div>
  </div>

  <?php if (!$isPast && !$isAttending): ?>
    <div class="card !bg-amber-50 !border-amber-100 p-5 mt-6 flex flex-wrap items-center justify-between gap-4">
      <div class="flex items-center gap-3">
        <div class="w-10 h-10 rounded-lg bg-amber-100 text-gold flex items-center justify-center flex-shrink-0"><i class="fa-regular fa-calendar"></i></div>
        <div>
          <p class="text-sm font-bold text-primary-navy">Don't miss out!</p>
          <p class="text-xs text-slate-500">Spaces may be limited. Register now to secure your spot.</p>
        </div>
      </div>
      <form method="POST" action="<?= e(url('events/' . $event['id'] . '/rsvp')) ?>">
        <?= csrf_field() ?>
        <button type="submit" class="btn-gold !px-5 !py-2.5 text-sm flex items-center gap-2">Register Now <i class="fa-solid fa-arrow-right"></i></button>
      </form>
    </div>
  <?php endif; ?>
</div>

<script>
  (function () {
    var shareJump = document.getElementById('share-jump');
    if (shareJump) {
      shareJump.addEventListener('click', function () {
        var target = document.getElementById('share-event');
        if (target) target.scrollIntoView({ behavior: 'smooth', block: 'start' });
      });
    }

    var copyBtn = document.getElementById('copy-event-link');
    var copyMsg = document.getElementById('copy-event-link-msg');
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
