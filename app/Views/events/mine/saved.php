<h3 class="text-base font-bold text-primary-navy mb-3">Saved Events (<?= count($events) ?>)</h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4">
  <?php foreach ($events as $event): $cardMode = 'saved'; $isAttending = in_array((int) $event['id'], $attendingEventIds, true); ?>
    <?php require __DIR__ . '/../partials/mine_card.php'; ?>
  <?php endforeach; ?>
  <?php if (empty($events)): ?>
    <p class="text-sm text-slate-400 col-span-full">You haven't saved any events yet. Browse events and tap the bookmark icon to save them here.</p>
  <?php endif; ?>
</div>
