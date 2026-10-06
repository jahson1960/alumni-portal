<?php
/**
 * Graduation Year / Cohort / Program fields, shared by the public registration form and the
 * admin register-alumni form. Expects $years, $cohorts, $programs already in scope.
 */
?>
<div class="grid grid-cols-2 gap-4">
  <div>
    <label class="form-label" for="graduation_year">Graduation Year</label>
    <select id="graduation_year" name="graduation_year" class="form-input" required>
      <option value="">Select year</option>
      <?php foreach ($years as $year): ?>
        <option value="<?= e((string) $year) ?>" <?= old('graduation_year') === e((string) $year) ? 'selected' : '' ?>><?= e((string) $year) ?></option>
      <?php endforeach; ?>
    </select>
  </div>
  <div class="relative">
    <label class="form-label" for="cohort_search">Cohort</label>
    <input type="text" id="cohort_search" class="form-input" placeholder="Type to search cohorts..." value="<?= old('cohort') ?>" autocomplete="off" required>
    <input type="hidden" id="cohort" name="cohort" value="<?= old('cohort') ?>">
    <div id="cohort_options" class="hidden absolute z-20 left-0 right-0 mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-48 overflow-y-auto"></div>
    <p id="cohort_no_match" class="hidden text-xs text-red-500 mt-1">No matching cohort &mdash; check your spelling or contact support.</p>
  </div>
</div>
<div>
  <label class="form-label" for="program">Program</label>
  <select id="program" name="program" class="form-input">
    <option value="">Select program</option>
    <?php foreach ($programs as $program): ?>
      <option value="<?= e($program['name']) ?>" <?= old('program') === e($program['name']) ? 'selected' : '' ?>><?= e($program['name']) ?></option>
    <?php endforeach; ?>
  </select>
</div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var cohorts = <?= json_encode(array_values($cohorts)) ?>;
    var searchInput = document.getElementById('cohort_search');
    var hiddenInput = document.getElementById('cohort');
    var optionsBox = document.getElementById('cohort_options');
    var noMatch = document.getElementById('cohort_no_match');
    if (!searchInput || !hiddenInput || !optionsBox) return;

    var highlighted = -1;
    var visible = [];

    // Word-based match: every word typed must appear somewhere in the cohort name, so
    // "exec mba 19" matches "Executive MBA 2019" without needing an exact/prefix match.
    function matches(text, query) {
      var words = query.toLowerCase().trim().split(/\s+/).filter(Boolean);
      if (!words.length) return true;
      var lower = text.toLowerCase();
      return words.every(function (w) { return lower.indexOf(w) !== -1; });
    }

    function render(query) {
      visible = cohorts.filter(function (c) { return matches(c, query); });
      highlighted = -1;
      optionsBox.innerHTML = '';
      noMatch.classList.add('hidden');

      if (!visible.length) {
        if (query.trim() !== '') noMatch.classList.remove('hidden');
        optionsBox.classList.add('hidden');
        return;
      }

      visible.forEach(function (cohort, i) {
        var item = document.createElement('button');
        item.type = 'button';
        item.className = 'block w-full text-left px-3 py-2 text-sm text-slate-700 hover:bg-gold/10';
        item.textContent = cohort;
        item.dataset.index = i;
        item.addEventListener('click', function () { select(cohort); });
        optionsBox.appendChild(item);
      });
      optionsBox.classList.remove('hidden');
    }

    function select(cohort) {
      hiddenInput.value = cohort;
      searchInput.value = cohort;
      optionsBox.classList.add('hidden');
      noMatch.classList.add('hidden');
    }

    function setHighlight(index) {
      var items = optionsBox.querySelectorAll('button');
      items.forEach(function (el, i) {
        el.classList.toggle('bg-gold/10', i === index);
      });
      highlighted = index;
    }

    searchInput.addEventListener('focus', function () { render(searchInput.value); });
    searchInput.addEventListener('input', function () {
      hiddenInput.value = '';
      render(searchInput.value);
    });

    searchInput.addEventListener('keydown', function (e) {
      if (optionsBox.classList.contains('hidden') || !visible.length) return;
      if (e.key === 'ArrowDown') {
        e.preventDefault();
        setHighlight(Math.min(highlighted + 1, visible.length - 1));
      } else if (e.key === 'ArrowUp') {
        e.preventDefault();
        setHighlight(Math.max(highlighted - 1, 0));
      } else if (e.key === 'Enter') {
        e.preventDefault();
        // Only confirm a highlighted item, or the single match when there's exactly one —
        // never silently guess among several still-ambiguous matches.
        if (highlighted >= 0) {
          select(visible[highlighted]);
        } else if (visible.length === 1) {
          select(visible[0]);
        }
      } else if (e.key === 'Escape') {
        optionsBox.classList.add('hidden');
      }
    });

    document.addEventListener('click', function (e) {
      if (e.target !== searchInput && !optionsBox.contains(e.target)) {
        optionsBox.classList.add('hidden');
        // Give feedback as soon as they leave the field without a real selection, rather
        // than waiting until they try to submit the whole form.
        if (hiddenInput.value.trim() === '' && searchInput.value.trim() !== '') {
          noMatch.textContent = 'Please select a cohort from the list.';
          noMatch.classList.remove('hidden');
        }
      }
    });

    searchInput.closest('form').addEventListener('submit', function (e) {
      if (hiddenInput.value.trim() === '') {
        e.preventDefault();
        searchInput.focus();
        noMatch.textContent = 'Please select a cohort from the list.';
        noMatch.classList.remove('hidden');
      }
    });
  });
</script>
