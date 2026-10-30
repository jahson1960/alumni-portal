<?php
/**
 * Live email format + availability check, used on Register Alumni and Create Staff. Expects
 * the including form to have an <input id="email"> and a submit button; disables that button
 * and shows why directly under the email field while the address is malformed, already taken,
 * or still being checked. The server still re-validates on submit regardless - this is purely
 * a faster/friendlier front end for the same rule.
 */
?>
<p id="email-check-message" class="hidden text-xs mt-1"></p>

<script>
  document.addEventListener('DOMContentLoaded', function () {
    var input = document.getElementById('email');
    var messageEl = document.getElementById('email-check-message');
    var form = input ? input.closest('form') : null;
    var submitBtn = form ? form.querySelector('button[type="submit"]') : null;
    if (!input || !messageEl || !form || !submitBtn) return;

    var checkUrl = <?= json_encode(url('admin/check-email')) ?>;
    var debounceTimer = null;
    var requestSeq = 0;

    function setStatus(message, isError) {
      if (!message) {
        messageEl.classList.add('hidden');
        messageEl.textContent = '';
        submitBtn.disabled = false;
        submitBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        return;
      }
      messageEl.textContent = message;
      messageEl.className = 'text-xs mt-1 ' + (isError ? 'text-red-500' : 'text-slate-400');
      submitBtn.disabled = Boolean(isError);
      submitBtn.classList.toggle('opacity-50', Boolean(isError));
      submitBtn.classList.toggle('cursor-not-allowed', Boolean(isError));
    }

    function check() {
      var email = input.value.trim();
      if (email === '') {
        setStatus(null);
        return;
      }

      // Quick client-side pass before hitting the server, so an obviously bad address doesn't
      // wait on a round trip to get flagged.
      if (!/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) {
        setStatus('Please enter a valid email address.', true);
        return;
      }

      var mySeq = ++requestSeq;
      setStatus('Checking email…', false);

      var statusMessages = {
        invalid_format: 'Please enter a valid email address.',
        unreachable_domain: "We couldn't find a mail server for that email's domain.",
        taken: 'An account with that email already exists.',
      };

      fetch(checkUrl + '?email=' + encodeURIComponent(email))
        .then(function (r) { return r.json(); })
        .then(function (data) {
          if (mySeq !== requestSeq) return; // a newer check already superseded this one
          if (data.status === 'ok') {
            setStatus(null);
          } else {
            setStatus(statusMessages[data.status] || 'Please enter a valid email address.', true);
          }
        })
        .catch(function () {
          if (mySeq !== requestSeq) return;
          // Don't permanently block submission over a dropped request - the server
          // re-validates on submit regardless.
          setStatus(null);
        });
    }

    input.addEventListener('blur', check);
    input.addEventListener('input', function () {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(check, 500);
    });
  });
</script>
