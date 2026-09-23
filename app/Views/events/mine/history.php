<h3 class="text-base font-bold text-primary-navy mb-3">Event History (<?= count($events) ?>)</h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  <?php foreach ($events as $event): $cardMode = 'past'; $isAttending = true; ?>
    <?php require __DIR__ . '/../partials/mine_card.php'; ?>
  <?php endforeach; ?>
  <?php if (empty($events)): ?>
    <p class="text-sm text-slate-400 col-span-full">You haven't attended any events yet.</p>
  <?php endif; ?>
</div>
