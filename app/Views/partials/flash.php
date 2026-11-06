<?php $flashSuccess = flash_get('success'); $flashError = flash_get('error'); ?>
<?php if ($flashSuccess): ?>
  <div class="max-w-[1280px] mx-auto px-4 md:px-8 pt-4 js-flash">
    <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded px-4 py-3"><?= e($flashSuccess) ?></div>
  </div>
<?php endif; ?>
<?php if ($flashError): ?>
  <div class="max-w-[1280px] mx-auto px-4 md:px-8 pt-4 js-flash">
    <div class="bg-red-50 border border-red-200 text-red-800 text-sm rounded px-4 py-3"><?= e($flashError) ?></div>
  </div>
<?php endif; ?>
<?php if ($flashSuccess || $flashError): ?>
  <script>
    document.querySelectorAll('.js-flash').forEach(function (el) {
      setTimeout(function () {
        el.style.transition = 'opacity 0.4s ease';
        el.style.opacity = '0';
        setTimeout(function () { el.remove(); }, 400);
      }, 4000);
    });
  </script>
<?php endif; ?>
