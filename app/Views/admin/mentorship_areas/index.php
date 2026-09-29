<div class="max-w-2xl">
  <p class="text-sm text-slate-500 mb-4">These are the areas alumni can pick from when they become a mentor, on the "Become a Mentor" page and their profile.</p>

  <div class="card p-4 mb-4">
    <form method="POST" action="<?= e(url('admin/mentorship-areas')) ?>" class="flex gap-2">
      <?= csrf_field() ?>
      <input type="text" name="name" placeholder="e.g. Entrepreneurship" class="form-input flex-1" required>
      <button type="submit" class="btn-gold !px-4 !py-2 text-xs whitespace-nowrap">+ Add</button>
    </form>
  </div>

  <div class="card overflow-x-auto">
    <table class="table-base">
      <tbody>
        <?php foreach ($areas as $area): ?>
          <tr>
            <td class="font-medium text-primary-navy"><?= e($area['name']) ?></td>
            <td class="text-right whitespace-nowrap">
              <a href="<?= e(url('admin/mentorship-areas/' . $area['id'] . '/edit')) ?>" class="text-sky-600 hover:underline mr-3">Edit</a>
              <form method="POST" action="<?= e(url('admin/mentorship-areas/' . $area['id'] . '/delete')) ?>" class="inline" onsubmit="return confirm('Delete this mentorship area?');">
                <?= csrf_field() ?>
                <button type="submit" class="text-red-600 hover:underline">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($areas)): ?>
          <tr><td class="text-center text-slate-400 py-6">No mentorship areas yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
