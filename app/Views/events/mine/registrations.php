<h3 class="text-base font-bold text-primary-navy mb-3">My Registered Events (<?= count($upcoming) ?>)</h3>
<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-8">
  <?php foreach ($upcoming as $event): $cardMode = 'upcoming'; $isAttending = true; ?>
    <?php require __DIR__ . '/../partials/mine_card.php'; ?>
  <?php endforeach; ?>
  <?php if (empty($upcoming)): ?>
    <p class="text-sm text-slate-400 col-span-full">You haven't registered for any upcoming events yet.</p>
  <?php endif; ?>
</div>

<?php if (!empty($past)): ?>
  <h3 class="text-base font-bold text-primary-navy mb-3">Past Registrations (<?= count($past) ?>)</h3>
  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
    <?php foreach ($past as $event): $cardMode = 'past'; $isAttending = true; ?>
      <?php require __DIR__ . '/../partials/mine_card.php'; ?>
    <?php endforeach; ?>
  </div>
<?php endif; ?>
