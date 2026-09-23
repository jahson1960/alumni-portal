<?php $flashSuccess = flash_get('success'); $flashError = flash_get('error'); ?>
<?php if ($flashSuccess): ?>
  <div class="max-w-[1280px] mx-auto px-4 md:px-8 pt-4">
    <div class="bg-green-50 border border-green-200 text-green-800 text-sm rounded px-4 py-3"><?= e($flashSuccess) ?></div>
  </div>
<?php endif; ?>
<?php if ($flashError): ?>
  <div class="max-w-[1280px] mx-auto px-4 md:px-8 pt-4">
    <div class="bg-red-50 border border-red-200 text-red-800 text-sm rounded px-4 py-3"><?= e($flashError) ?></div>
  </div>
<?php endif; ?>
