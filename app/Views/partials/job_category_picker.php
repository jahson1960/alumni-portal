<?php
/**
 * Shared job-category chip picker, used on both registration forms, Profile Edit, and the
 * admin alumni page. Expects $allCategories (array of category rows) and $selectedIds (int[])
 * already in scope from the including view. $maxSelect caps how many can be checked (default 5);
 * at least 1 is always required before the containing <form> can submit.
 */
$maxSelect = $maxSelect ?? 5;
?>
<div class="job-category-picker" data-max-select="<?= (int) $maxSelect ?>">
  <div class="grid grid-cols-1 sm:grid-cols-2 gap-2.5">
    <?php foreach ($allCategories as $cat): $checked = in_array((int) $cat['id'], $selectedIds, true); $pc = palette_classes($cat['slug']); ?>
      <label class="job-category-chip flex items-center gap-2.5 text-sm font-medium border rounded-lg px-3 py-2.5 cursor-pointer transition-colors <?= $checked ? 'border-gold bg-gold/5 text-gold' : 'border-slate-200 text-slate-600 hover:border-slate-300' ?>">
        <input type="checkbox" name="job_categories[]" value="<?= (int) $cat['id'] ?>" <?= $checked ? 'checked' : '' ?> class="hidden job-category-checkbox">
        <span class="badge <?= e($pc['bg']) ?> <?= e($pc['text']) ?> !text-[0.6rem] !px-1.5 !py-0.5 flex-shrink-0"><?= e(mb_strtoupper(mb_substr($cat['name'], 0, 2))) ?></span>
        <span class="truncate"><?= e($cat['name']) ?></span>
      </label>
    <?php endforeach; ?>
    <?php if (empty($allCategories)): ?>
      <p class="text-xs text-slate-400 col-span-full">No job categories have been set up yet.</p>
    <?php endif; ?>
  </div>
  <p class="job-category-limit-note hidden text-xs text-amber-600 mt-2">You can select up to <?= (int) $maxSelect ?> categories.</p>
  <p class="job-category-required-note hidden text-xs text-red-500 mt-2">Please select at least one job category.</p>
</div>

<script>
  (function () {
    document.querySelectorAll('.job-category-picker').forEach(function (picker) {
      if (picker.dataset.wired === '1') return;
      picker.dataset.wired = '1';

      var max = parseInt(picker.dataset.maxSelect, 10) || 5;
      var chips = picker.querySelectorAll('.job-category-chip');
      var limitNote = picker.querySelector('.job-category-limit-note');
      var requiredNote = picker.querySelector('.job-category-required-note');

      function checkedCount() {
        return picker.querySelectorAll('.job-category-checkbox:checked').length;
      }

      chips.forEach(function (chip) {
        var checkbox = chip.querySelector('.job-category-checkbox');
        checkbox.addEventListener('change', function () {
          if (checkbox.checked && checkedCount() > max) {
            checkbox.checked = false;
            limitNote.classList.remove('hidden');
            return;
          }
          limitNote.classList.add('hidden');
          chip.classList.toggle('border-gold', checkbox.checked);
          chip.classList.toggle('bg-gold/5', checkbox.checked);
          chip.classList.toggle('text-gold', checkbox.checked);
          chip.classList.toggle('border-slate-200', !checkbox.checked);
          chip.classList.toggle('text-slate-600', !checkbox.checked);
          if (checkedCount() > 0) requiredNote.classList.add('hidden');
        });
      });

      var form = picker.closest('form');
      if (form) {
        form.addEventListener('submit', function (e) {
          if (checkedCount() === 0) {
            e.preventDefault();
            requiredNote.classList.remove('hidden');
            picker.scrollIntoView({ behavior: 'smooth', block: 'center' });
          }
        });
      }
    });
  })();
</script>
