<?php
/**
 * Checkbox-chip picker for the admin-managed mentorship areas list. Expects $allMentorshipAreas
 * (rows from MentorshipArea::all()) and $selectedMentorshipAreas (array of currently-selected
 * name strings) already in scope. Submits as mentorship_areas[] — no cap on selection count.
 */
?>
<div class="grid grid-cols-2 sm:grid-cols-3 gap-3" id="mentorship-area-chips">
  <?php foreach ($allMentorshipAreas as $area): $checked = in_array($area['name'], $selectedMentorshipAreas, true); ?>
    <label class="mentorship-area-chip flex items-center gap-2 text-sm font-medium border rounded-lg px-3 py-2.5 cursor-pointer <?= $checked ? 'border-gold bg-gold/5 text-gold' : 'border-slate-200 text-slate-600 hover:border-slate-300' ?>">
      <input type="checkbox" name="mentorship_areas[]" value="<?= e($area['name']) ?>" <?= $checked ? 'checked' : '' ?> class="hidden mentorship-area-checkbox">
      <?= e($area['name']) ?>
    </label>
  <?php endforeach; ?>
  <?php if (empty($allMentorshipAreas)): ?>
    <p class="text-xs text-slate-400 col-span-full">No mentorship areas have been set up yet.</p>
  <?php endif; ?>
</div>

<script>
  (function () {
    var chips = document.querySelectorAll('#mentorship-area-chips .mentorship-area-chip');
    function syncChip(chip) {
      var checked = chip.querySelector('.mentorship-area-checkbox').checked;
      chip.classList.toggle('border-gold', checked);
      chip.classList.toggle('bg-gold/5', checked);
      chip.classList.toggle('text-gold', checked);
      chip.classList.toggle('border-slate-200', !checked);
      chip.classList.toggle('text-slate-600', !checked);
    }
    chips.forEach(function (chip) {
      var checkbox = chip.querySelector('.mentorship-area-checkbox');
      chip.addEventListener('click', function (e) {
        e.preventDefault();
        checkbox.checked = !checkbox.checked;
        syncChip(chip);
      });
    });
  })();
</script>
