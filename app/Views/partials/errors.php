<?php $formErrors = errors_get(); ?>
<?php if ($formErrors): ?>
  <div class="bg-red-50 border border-red-200 text-red-800 text-sm rounded px-4 py-3 mb-4">
    <ul class="list-disc list-inside space-y-0.5">
      <?php foreach ($formErrors as $err): ?>
        <li><?= e($err) ?></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>
