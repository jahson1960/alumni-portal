<?php $isEdit = $event !== null; ?>
<a href="<?= e(url('admin/events')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to Events</a>

<?php require dirname(dirname(__DIR__)) . '/partials/errors.php'; ?>

<form method="POST" action="<?= e($isEdit ? url('admin/events/' . $event['id']) : url('admin/events')) ?>" class="card p-6 max-w-2xl space-y-4">
  <?= csrf_field() ?>

  <div>
    <label class="form-label" for="title">Event Title</label>
    <input type="text" id="title" name="title" class="form-input" value="<?= e($event['title'] ?? '') ?>" required>
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="form-label" for="event_date">Date</label>
      <input type="date" id="event_date" name="event_date" class="form-input" value="<?= e($event['event_date'] ?? '') ?>" required>
    </div>
    <div>
      <label class="form-label" for="event_time">Time</label>
      <input type="text" id="event_time" name="event_time" class="form-input" placeholder="e.g. 6:00 PM WAT" value="<?= e($event['event_time'] ?? '') ?>">
    </div>
  </div>

  <div>
    <label class="form-label" for="image">Image URL</label>
    <input type="text" id="image" name="image" class="form-input" placeholder="https://..." value="<?= e($event['image'] ?? '') ?>">
    <p class="text-xs text-slate-400 mt-1">Shown as the event's photo on cards and My Events.</p>
  </div>

  <div>
    <label class="form-label" for="location">Location</label>
    <input type="text" id="location" name="location" class="form-input" value="<?= e($event['location'] ?? '') ?>">
  </div>

  <div>
    <label class="form-label" for="category">Category</label>
    <select id="category" name="category" class="form-input">
      <option value="">General (shows only under All Events)</option>
      <?php foreach (\App\Models\Event::CATEGORIES as $cat): ?>
        <option value="<?= e($cat) ?>" <?= ($event['category'] ?? '') === $cat ? 'selected' : '' ?>><?= e($cat) ?></option>
      <?php endforeach; ?>
    </select>
  </div>

  <div class="flex items-center gap-2">
    <input type="checkbox" id="is_virtual" name="is_virtual" value="1" <?= !empty($event['is_virtual']) ? 'checked' : '' ?> class="rounded border-slate-300 text-gold focus:ring-gold">
    <label class="text-sm text-slate-700" for="is_virtual">This is a virtual event</label>
  </div>

  <div class="flex items-center gap-2">
    <input type="checkbox" id="is_featured" name="is_featured" value="1" <?= !empty($event['is_featured']) ? 'checked' : '' ?> class="rounded border-slate-300 text-gold focus:ring-gold">
    <label class="text-sm text-slate-700" for="is_featured">Feature on the Featured Events tab</label>
  </div>

  <div>
    <label class="form-label" for="description">Description</label>
    <textarea id="description" name="description" rows="5" class="form-textarea"><?= e($event['description'] ?? '') ?></textarea>
  </div>

  <button type="submit" class="btn-gold"><?= $isEdit ? 'Save Changes' : 'Create Event' ?></button>
</form>
