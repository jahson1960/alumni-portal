<?php /** @var array $events */ ?>
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
  <?php foreach ($events as $i => $event): ?>
    <?php
      $isSaved = in_array((int) $event['id'], $savedEventIds, true);
      $isAttending = in_array((int) $event['id'], $attendingEventIds, true);
      $colors = event_category_color($event['category'], $i);
      $preview = $attendeePreviews[$event['id']] ?? ['attendees' => [], 'count' => 0];
    ?>
    <div class="card !p-0 overflow-hidden relative border-l-4 <?= e($colors['border']) ?>">
      <a href="<?= e(url('events/' . $event['id'])) ?>" class="absolute inset-0 z-0" aria-label="<?= e($event['title']) ?>"></a>
      <div class="p-4 pointer-events-none">
        <div class="flex items-start gap-3 mb-3">
          <div class="bg-primary-navy text-white rounded p-1.5 text-center min-w-[48px] flex flex-col items-center justify-center flex-shrink-0 py-1.5">
            <span class="text-[0.55rem] font-bold uppercase leading-tight"><?= e(format_date($event['event_date'], 'M')) ?></span>
            <span class="text-base font-extrabold leading-none my-0.5"><?= e(format_date($event['event_date'], 'd')) ?></span>
            <span class="text-[0.55rem] font-bold uppercase leading-tight"><?= e(format_date($event['event_date'], 'D')) ?></span>
          </div>
          <div class="w-10 h-10 rounded-full <?= e($colors['bg']) ?> <?= e($colors['text']) ?> flex items-center justify-center flex-shrink-0"><i class="fa-solid <?= e(event_category_icon($event['category'], $i)) ?>"></i></div>
          <div class="min-w-0 flex-1">
            <h5 class="text-sm font-bold text-primary-navy leading-snug line-clamp-2"><?= e($event['title']) ?></h5>
            <p class="text-xs text-slate-500 truncate mt-1"><i class="fa-solid fa-location-dot"></i> <?= e($event['is_virtual'] ? 'Virtual' : ($event['location'] ?: 'TBA')) ?></p>
            <p class="text-xs text-slate-500"><i class="fa-regular fa-clock"></i> <?= e($event['event_time']) ?></p>
          </div>
        </div>
        <p class="text-xs text-slate-500 mb-3 line-clamp-2"><?= e($event['description']) ?></p>
        <div class="flex items-center justify-between pointer-events-auto relative z-10">
          <div class="flex items-center min-w-0">
            <?php foreach ($preview['attendees'] as $person): ?>
              <div class="-ml-2 first:ml-0"><?= avatar_html($person, 'w-6 h-6 ring-2 ring-white') ?></div>
            <?php endforeach; ?>
            <?php if ($preview['count'] > 0): ?><span class="text-[0.65rem] text-slate-400 ml-2 whitespace-nowrap">+<?= $preview['count'] ?> going</span><?php endif; ?>
          </div>
          <div class="flex items-center gap-2 flex-shrink-0">
            <form method="POST" action="<?= e(url('events/' . $event['id'] . '/rsvp')) ?>">
              <?= csrf_field() ?>
              <button type="submit" class="btn !px-3 !py-1.5 text-[0.7rem] whitespace-nowrap <?= $isAttending ? '!bg-emerald-100 !text-emerald-700' : '!bg-gold !text-white hover:!bg-gold/90' ?>"><?= $isAttending ? '✓ Registered' : 'Register' ?></button>
            </form>
            <?php if (\App\Core\Auth::check()): ?>
              <form method="POST" action="<?= e(url('events/' . $event['id'] . '/save')) ?>">
                <?= csrf_field() ?>
                <button type="submit" class="text-sm <?= $isSaved ? 'text-gold' : 'text-slate-300 hover:text-gold' ?>" title="<?= $isSaved ? 'Unsave' : 'Save' ?>"><i class="bi <?= $isSaved ? 'bi-bookmark-fill' : 'bi-bookmark' ?>"></i></button>
              </form>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>
  <?php endforeach; ?>
  <?php if (empty($events)): ?>
    <p class="text-sm text-slate-400 col-span-full">No events found matching those filters.</p>
  <?php endif; ?>
</div>

<?php if (($totalCount ?? 0) > count($events)): ?>
  <div class="flex justify-center mt-6">
    <a href="<?= e(url('events') . $qs(['per_page' => $perPage + 9])) ?>" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-5 !py-2.5 text-sm flex items-center gap-2">View More Events <i class="bi bi-chevron-down"></i></a>
  </div>
<?php endif; ?>
