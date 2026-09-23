<?php
/**
 * @var array $event
 * @var string $cardMode 'upcoming'|'past'|'saved'
 * @var bool $isAttending
 */
?>
<div class="card !p-0 overflow-hidden flex">
  <div class="relative w-[130px] sm:w-[160px] flex-shrink-0 bg-slate-200 overflow-hidden">
    <?php if (!empty($event['image'])): ?>
      <img src="<?= e($event['image']) ?>" alt="" class="absolute inset-0 w-full h-full object-cover">
      <div class="absolute inset-0 bg-black/35"></div>
    <?php else: ?>
      <div class="absolute inset-0 bg-gradient-to-br from-primary-navy to-slate-900"></div>
    <?php endif; ?>
    <div class="absolute top-3 left-3 bg-slate-900/80 rounded-lg px-2.5 py-1.5 text-center">
      <p class="text-[0.55rem] font-semibold text-slate-300 uppercase leading-tight"><?= e(format_date($event['event_date'], 'M')) ?></p>
      <p class="text-lg font-extrabold text-white leading-none my-0.5"><?= e(format_date($event['event_date'], 'd')) ?></p>
      <p class="text-[0.55rem] font-semibold text-slate-300 uppercase leading-tight"><?= e(format_date($event['event_date'], 'D')) ?></p>
    </div>
  </div>

  <div class="flex-1 min-w-0 p-4 relative">
    <?php if ($cardMode !== 'saved'): ?>
      <div class="absolute top-3 right-3">
        <button type="button" class="kebab-btn text-slate-400 hover:text-primary-navy px-1"><i class="bi bi-three-dots-vertical"></i></button>
        <div class="kebab-menu hidden absolute right-0 top-full mt-1 z-20 bg-white border border-slate-200 rounded-lg shadow-lg w-44 py-1">
          <a href="<?= e(url('events/' . $event['id'])) ?>" class="block px-3 py-2 text-xs text-slate-700 hover:bg-slate-50">View Details</a>
          <?php if ($cardMode === 'upcoming'): ?>
            <form method="POST" action="<?= e(url('events/' . $event['id'] . '/rsvp')) ?>">
              <?= csrf_field() ?>
              <button type="submit" class="w-full text-left px-3 py-2 text-xs text-red-600 hover:bg-red-50">Cancel Registration</button>
            </form>
          <?php endif; ?>
        </div>
      </div>
    <?php endif; ?>

    <h5 class="text-sm font-bold text-primary-navy leading-snug line-clamp-2 mb-1.5 pr-6"><?= e($event['title']) ?></h5>
    <p class="text-xs text-slate-500 mb-0.5"><i class="fa-solid fa-location-dot"></i> <?= e($event['is_virtual'] ? 'Virtual Event' : ($event['location'] ?: 'TBA')) ?></p>
    <p class="text-xs text-slate-500 mb-2.5"><i class="fa-regular fa-clock"></i> <?= e($event['event_time'] ?: 'TBA') ?></p>
    <p class="text-xs text-slate-500 line-clamp-2 mb-3"><?= e($event['description']) ?></p>

    <?php if ($cardMode === 'upcoming'): ?>
      <span class="badge bg-emerald-100 text-emerald-700 inline-flex items-center gap-1.5"><i class="fa-solid fa-circle-check"></i> Registered</span>
    <?php elseif ($cardMode === 'past'): ?>
      <div class="flex items-center justify-between">
        <span class="badge bg-sky-100 text-sky-700 inline-flex items-center gap-1.5"><i class="fa-solid fa-circle-check"></i> Attended</span>
        <a href="<?= e(url('events/' . $event['id'])) ?>" class="text-xs font-semibold text-sky-600 hover:underline">View Details &rarr;</a>
      </div>
    <?php else: ?>
      <div class="flex items-center justify-between">
        <?php if ($isAttending): ?>
          <span class="badge bg-emerald-100 text-emerald-700 inline-flex items-center gap-1.5"><i class="fa-solid fa-circle-check"></i> Registered</span>
        <?php else: ?>
          <form method="POST" action="<?= e(url('events/' . $event['id'] . '/rsvp')) ?>">
            <?= csrf_field() ?>
            <button type="submit" class="btn !bg-gold !text-white !px-3 !py-1.5 text-[0.7rem]">Register</button>
          </form>
        <?php endif; ?>
        <form method="POST" action="<?= e(url('events/' . $event['id'] . '/save')) ?>">
          <?= csrf_field() ?>
          <button type="submit" class="text-sm text-gold hover:text-slate-400" title="Unsave"><i class="bi bi-bookmark-fill"></i></button>
        </form>
      </div>
    <?php endif; ?>
  </div>
</div>
