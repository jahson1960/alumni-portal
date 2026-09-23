<?php
$monthLabel = date('F Y', strtotime($month . '-01'));
$firstDayOfWeek = (int) date('w', strtotime($month . '-01'));
$daysInMonth = (int) date('t', strtotime($month . '-01'));
$todayStr = date('Y-m-d');
?>
<div class="card p-4 md:p-6">
  <div class="flex items-center justify-between mb-5">
    <a href="<?= e(url('events') . $qs(['display' => 'calendar', 'month' => $prevMonth])) ?>" class="w-9 h-9 rounded-lg border border-slate-200 flex items-center justify-center text-slate-500 hover:text-gold hover:border-gold flex-shrink-0"><i class="fa-solid fa-chevron-left"></i></a>
    <h3 class="text-base font-bold text-primary-navy"><?= e($monthLabel) ?></h3>
    <a href="<?= e(url('events') . $qs(['display' => 'calendar', 'month' => $nextMonth])) ?>" class="w-9 h-9 rounded-lg border border-slate-200 flex items-center justify-center text-slate-500 hover:text-gold hover:border-gold flex-shrink-0"><i class="fa-solid fa-chevron-right"></i></a>
  </div>

  <div class="grid grid-cols-7 gap-1 mb-1">
    <?php foreach (['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'] as $d): ?>
      <div class="text-center text-[0.6rem] sm:text-[0.65rem] font-bold text-slate-400 uppercase py-2"><?= $d ?></div>
    <?php endforeach; ?>
  </div>

  <div class="grid grid-cols-7 gap-1">
    <?php for ($i = 0; $i < $firstDayOfWeek; $i++): ?>
      <div class="min-h-[70px] sm:min-h-[90px] rounded-lg bg-slate-50/50"></div>
    <?php endfor; ?>
    <?php for ($day = 1; $day <= $daysInMonth; $day++): ?>
      <?php
        $dateStr = $month . '-' . str_pad((string) $day, 2, '0', STR_PAD_LEFT);
        $isToday = $dateStr === $todayStr;
        $dayEvents = $eventsByDay[$day] ?? [];
      ?>
      <div class="min-h-[70px] sm:min-h-[90px] rounded-lg border border-slate-100 p-1 sm:p-1.5 <?= $isToday ? '!border-gold !bg-gold/5' : '' ?>">
        <span class="text-[0.68rem] sm:text-xs font-bold <?= $isToday ? 'text-gold' : 'text-slate-400' ?>"><?= $day ?></span>
        <div class="space-y-1 mt-1">
          <?php foreach (array_slice($dayEvents, 0, 2) as $ev): $c = event_category_color($ev['category']); ?>
            <a href="<?= e(url('events/' . $ev['id'])) ?>" class="block text-[0.6rem] sm:text-[0.62rem] font-semibold <?= e($c['bg']) ?> <?= e($c['text']) ?> rounded px-1 sm:px-1.5 py-0.5 truncate" title="<?= e($ev['title']) ?>"><?= e($ev['title']) ?></a>
          <?php endforeach; ?>
          <?php if (count($dayEvents) > 2): ?>
            <p class="text-[0.58rem] sm:text-[0.6rem] text-slate-400 px-1">+<?= count($dayEvents) - 2 ?> more</p>
          <?php endif; ?>
        </div>
      </div>
    <?php endfor; ?>
  </div>

  <?php if (empty($events)): ?>
    <p class="text-sm text-slate-400 text-center mt-6">No events scheduled this month.</p>
  <?php endif; ?>
</div>
