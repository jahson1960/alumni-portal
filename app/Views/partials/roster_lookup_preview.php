<?php
/**
 * Live preview shown under the #matric_number field on the public and admin-assisted
 * registration forms. Registration no longer asks for name/program/cohort/graduation year —
 * this confirms what will be pulled from the alumni roster before the form is submitted.
 */
?>
<div id="roster_lookup_result" class="hidden mt-2 text-xs rounded-lg p-2.5 border"></div>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var input = document.getElementById('matric_number');
    var result = document.getElementById('roster_lookup_result');
    if (!input || !result) return;

    var lookupUrl = <?= json_encode(url('roster-lookup')) ?>;
    var debounceTimer = null;

    function escapeHtml(value) {
      var div = document.createElement('div');
      div.textContent = String(value);
      return div.innerHTML;
    }

    function showError(message) {
      result.className = 'mt-2 text-xs rounded-lg p-2.5 border bg-red-50 border-red-100 text-red-600';
      result.innerHTML = '<i class="fa-solid fa-circle-xmark"></i> ' + escapeHtml(message);
    }

    function showMatch(data) {
      var bits = [data.name];
      if (data.program) bits.push(data.program);
      if (data.cohort) bits.push(data.cohort);
      if (data.graduation_year) bits.push('Class of ' + data.graduation_year);
      result.className = 'mt-2 text-xs rounded-lg p-2.5 border bg-emerald-50 border-emerald-100 text-emerald-700';
      result.innerHTML = '<i class="fa-solid fa-circle-check"></i> Welcome, ' + bits.map(escapeHtml).join(' &middot; ') + '.';
    }

    function clear() {
      result.classList.add('hidden');
      result.innerHTML = '';
    }

    function check() {
      var matric = input.value.trim();
      if (matric === '') {
        clear();
        return;
      }

      fetch(lookupUrl + '?matric_number=' + encodeURIComponent(matric))
        .then(function (r) { return r.json(); })
        .then(function (data) {
          result.classList.remove('hidden');
          if (!data.found) {
            showError('No alumni roster record was found for this matric number. Contact the alumni office if you believe this is an error.');
          } else if (data.claimed) {
            showError('An account has already been created for this matric number.');
          } else {
            showMatch(data);
          }
        })
        .catch(clear);
    }

    input.addEventListener('blur', check);
    input.addEventListener('input', function () {
      clear();
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(check, 600);
    });
  });
</script>
