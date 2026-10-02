<div class="max-w-2xl">
  <p class="text-sm text-slate-500 mb-4">These are the academic programs alumni can pick from when they register.</p>

  <div class="card p-4 mb-4">
    <form method="POST" action="<?= e(url('admin/programs')) ?>" class="flex gap-2">
      <?= csrf_field() ?>
      <input type="text" name="name" placeholder="e.g. Executive MBA" class="form-input flex-1" required>
      <button type="submit" class="btn-gold !px-4 !py-2 text-xs whitespace-nowrap">+ Add</button>
    </form>
  </div>

  <div class="card overflow-x-auto">
    <table class="table-base">
      <tbody>
        <?php foreach ($programs as $program): ?>
          <tr>
            <td class="font-medium text-primary-navy"><?= e($program['name']) ?></td>
            <td class="text-right whitespace-nowrap">
              <a href="<?= e(url('admin/programs/' . $program['id'] . '/edit')) ?>" class="text-sky-600 hover:underline mr-3">Edit</a>
              <form method="POST" action="<?= e(url('admin/programs/' . $program['id'] . '/delete')) ?>" class="inline" onsubmit="return confirm('Delete this program?');">
                <?= csrf_field() ?>
                <button type="submit" class="text-red-600 hover:underline">Delete</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($programs)): ?>
          <tr><td class="text-center text-slate-400 py-6">No programs yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
