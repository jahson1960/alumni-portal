<div class="flex justify-between items-center mb-4">
  <h2 class="text-sm font-bold text-primary-navy">Events</h2>
  <a href="<?= e(url('admin/events/create')) ?>" class="btn-gold !px-4 !py-2 text-xs">+ New Event</a>
</div>

<?php require dirname(__DIR__, 2) . '/partials/admin_pagination.php'; ?>

<div class="card overflow-x-auto">
  <table class="table-base">
    <thead>
      <tr>
        <th>Title</th>
        <th>Date</th>
        <th>Location</th>
        <th>Category</th>
        <th>Featured</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($events as $event): ?>
        <tr>
          <td class="font-medium text-primary-navy"><?= e($event['title']) ?></td>
          <td class="text-slate-500"><?= e(format_date($event['event_date'], 'M j, Y')) ?> &bull; <?= e($event['event_time']) ?></td>
          <td class="text-slate-500"><?= e($event['location']) ?><?= $event['is_virtual'] ? ' (Virtual)' : '' ?></td>
          <td class="text-slate-500"><?= e($event['category'] ?: '—') ?></td>
          <td><?php if (!empty($event['is_featured'])): ?><span class="badge-green">Featured</span><?php else: ?><span class="text-slate-300">&mdash;</span><?php endif; ?></td>
          <td class="text-right whitespace-nowrap">
            <a href="<?= e(url('admin/events/' . $event['id'] . '/edit')) ?>" class="text-sky-600 hover:underline mr-3">Edit</a>
            <form method="POST" action="<?= e(url('admin/events/' . $event['id'] . '/delete')) ?>" class="inline" onsubmit="return confirm('Delete this event?');">
              <?= csrf_field() ?>
              <button type="submit" class="text-red-600 hover:underline">Delete</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($events)): ?>
        <tr><td colspan="6" class="text-center text-slate-400 py-6">No events yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
