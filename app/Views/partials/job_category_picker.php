<?php
/**
 * Shared job-category chip picker, used on both registration forms, Profile Edit, and the
 * admin alumni page. Expects $allCategories (array of category rows) and $selectedIds (int[])
 * already in scope from the including view. $maxSelect caps how many can be checked (default 5);
 * at least 1 is always required before the containing <form> can submit. $scrollable caps the
 * grid at ~4 rows with a scrollbar instead of growing the page with every category added -
 * used on the registration forms, where this is the only thing on the page competing for space.
 * $allowCreate adds an inline "+ Add" box that creates a new job category via AJAX (admin-only -
 * CategoryController is admin-only - so only set this true where the current user is an admin).
 */
$maxSelect = $maxSelect ?? 5;
$scrollable = $scrollable ?? false;
$allowCreate = $allowCreate ?? false;
?>
<div class="job-category-picker" data-max-select="<?= (int) $maxSelect ?>" data-create-url="<?= e(url('admin/categories')) ?>" data-csrf="<?= e(\App\Core\Csrf::token()) ?>">
  <div class="job-category-grid grid grid-cols-1 sm:grid-cols-2 gap-2.5 <?= $scrollable ? 'max-h-48 overflow-y-auto pr-1 mega-scroll' : '' ?>">
    <?php foreach ($allCategories as $cat): ?>
      <?= job_category_chip_html($cat, in_array((int) $cat['id'], $selectedIds, true)) ?>
    <?php endforeach; ?>
    <?php if (empty($allCategories)): ?>
      <p class="job-category-empty-note text-xs text-slate-400 col-span-full">No job categories have been set up yet.</p>
    <?php endif; ?>
  </div>
  <p class="job-category-limit-note hidden text-xs text-amber-600 mt-2">You can select up to <?= (int) $maxSelect ?> categories.</p>
  <p class="job-category-required-note hidden text-xs text-red-500 mt-2">Please select at least one job category.</p>

  <?php if ($allowCreate): ?>
    <div class="job-category-add flex gap-2 mt-3">
      <input type="text" class="job-category-add-input form-input !py-2 !text-sm flex-1" placeholder="Add a new category&hellip;" maxlength="100">
      <button type="button" class="job-category-add-btn btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-3 !py-2 text-xs whitespace-nowrap flex-shrink-0">+ Add</button>
    </div>
    <p class="job-category-add-error hidden text-xs text-red-500 mt-1"></p>
  <?php endif; ?>
</div>

<script>
  (function () {
    document.querySelectorAll('.job-category-picker').forEach(function (picker) {
      if (picker.dataset.wired === '1') return;
      picker.dataset.wired = '1';

      var max = parseInt(picker.dataset.maxSelect, 10) || 5;
      var grid = picker.querySelector('.job-category-grid');
      var limitNote = picker.querySelector('.job-category-limit-note');
      var requiredNote = picker.querySelector('.job-category-required-note');

      function checkedCount() {
        return picker.querySelectorAll('.job-category-checkbox:checked').length;
      }

      function setChipState(chip, checked) {
        chip.classList.toggle('border-gold', checked);
        chip.classList.toggle('bg-gold/5', checked);
        chip.classList.toggle('text-gold', checked);
        chip.classList.toggle('border-slate-200', !checked);
        chip.classList.toggle('text-slate-600', !checked);
      }

      // Event delegation on the grid (not per-chip listeners) so a chip added later by the
      // "+ Add" flow below needs no extra wiring to behave exactly like the ones rendered on load.
      grid.addEventListener('change', function (e) {
        var checkbox = e.target;
        if (!checkbox.classList.contains('job-category-checkbox')) return;
        var chip = checkbox.closest('.job-category-chip');

        if (checkbox.checked && checkedCount() > max) {
          checkbox.checked = false;
          limitNote.classList.remove('hidden');
          return;
        }
        limitNote.classList.add('hidden');
        setChipState(chip, checkbox.checked);
        if (checkedCount() > 0) requiredNote.classList.add('hidden');
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

      var addInput = picker.querySelector('.job-category-add-input');
      var addBtn = picker.querySelector('.job-category-add-btn');
      if (!addInput || !addBtn) return;
      var addError = picker.querySelector('.job-category-add-error');

      function submitNewCategory() {
        var name = addInput.value.trim();
        addError.classList.add('hidden');
        if (name === '') return;

        addBtn.disabled = true;
        var body = new URLSearchParams({ type: 'job', name: name, _csrf: picker.dataset.csrf });
        fetch(picker.dataset.createUrl, {
          method: 'POST',
          headers: { 'X-Requested-With': 'XMLHttpRequest', 'Content-Type': 'application/x-www-form-urlencoded' },
          body: body.toString(),
        })
          .then(function (res) { return res.json().then(function (data) { return { ok: res.ok, data: data }; }); })
          .then(function (result) {
            addBtn.disabled = false;
            if (!result.ok) {
              addError.textContent = result.data.error || 'Could not add that category.';
              addError.classList.remove('hidden');
              return;
            }
            var emptyNote = grid.querySelector('.job-category-empty-note');
            if (emptyNote) emptyNote.remove();

            var wrapper = document.createElement('div');
            wrapper.innerHTML = result.data.html;
            var chip = wrapper.firstElementChild;
            grid.appendChild(chip);

            var checkbox = chip.querySelector('.job-category-checkbox');
            if (checkedCount() < max) {
              checkbox.checked = true;
              setChipState(chip, true);
              requiredNote.classList.add('hidden');
            }
            chip.scrollIntoView({ block: 'nearest' });
            addInput.value = '';
          })
          .catch(function () {
            addBtn.disabled = false;
            addError.textContent = 'Could not add that category - please try again.';
            addError.classList.remove('hidden');
          });
      }

      addBtn.addEventListener('click', submitNewCategory);
      addInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
          e.preventDefault();
          submitNewCategory();
        }
      });
    });
  })();
</script>
