<?php
/** @var array $field */
/** @var string $prev */
/** @var array $inputTypes */
$isSnapshot = in_array($field['profile_field'], ['education', 'experience'], true);
?>
<div>
  <label class="form-label" <?= $isSnapshot ? '' : 'for="field_' . e((string) $field['id']) . '"' ?>>
    <?= e($field['label']) ?> <?php if ($field['is_required'] && !$isSnapshot): ?><span class="text-red-500">*</span><?php endif; ?>
    <?php if ($field['profile_field']): ?><span class="text-slate-400 font-normal text-xs">&mdash; from your profile</span><?php endif; ?>
  </label>

  <?php if ($isSnapshot): ?>
    <?php if (trim($prev) === ''): ?>
      <p class="text-sm text-slate-400 border border-dashed border-slate-200 rounded-lg p-3">Nothing on your profile yet. <a href="<?= e(url('profile/edit')) ?>" class="text-gold hover:underline">Add it on your profile</a> to include it here.</p>
    <?php else: ?>
      <div class="text-sm text-slate-700 whitespace-pre-line bg-slate-50 border border-slate-200 rounded-lg p-3"><?= e($prev) ?></div>
      <input type="hidden" name="field_<?= e((string) $field['id']) ?>" value="<?= e($prev) ?>">
    <?php endif; ?>
    <p class="text-xs text-slate-400 mt-1"><a href="<?= e(url('profile/edit')) ?>" class="text-gold hover:underline">Update your profile</a> to change this.</p>

  <?php elseif ($field['field_type'] === 'textarea'): ?>
    <textarea id="field_<?= e((string) $field['id']) ?>" name="field_<?= e((string) $field['id']) ?>" rows="4" class="form-textarea" <?= $field['is_required'] ? 'required' : '' ?>><?= e($prev) ?></textarea>

  <?php elseif ($field['field_type'] === 'select'): ?>
    <select id="field_<?= e((string) $field['id']) ?>" name="field_<?= e((string) $field['id']) ?>" class="form-input" <?= $field['is_required'] ? 'required' : '' ?>>
      <option value="">Select an option&hellip;</option>
      <?php foreach (array_filter(array_map('trim', explode(',', (string) $field['options']))) as $choice): ?>
        <option value="<?= e($choice) ?>" <?= $prev === $choice ? 'selected' : '' ?>><?= e($choice) ?></option>
      <?php endforeach; ?>
    </select>

  <?php elseif ($field['field_type'] === 'file'): ?>
    <input type="file" id="field_<?= e((string) $field['id']) ?>" name="field_<?= e((string) $field['id']) ?>" accept=".pdf,.doc,.docx" class="text-sm block" <?= $field['is_required'] && !$prev ? 'required' : '' ?>>
    <?php if ($prev): ?>
      <p class="text-xs text-slate-400 mt-1"><?= $field['profile_field'] === 'resume' ? 'Using your profile resume' : 'Previously submitted' ?>: <a href="<?= e($prev) ?>" target="_blank" rel="noopener noreferrer" class="text-gold hover:underline">view file</a> &mdash; choose a new file to replace it, or leave blank to keep it.</p>
    <?php endif; ?>

  <?php else: ?>
    <input type="<?= e($inputTypes[$field['field_type']] ?? 'text') ?>" id="field_<?= e((string) $field['id']) ?>" name="field_<?= e((string) $field['id']) ?>" class="form-input" value="<?= e($prev) ?>" <?= $field['is_required'] ? 'required' : '' ?>>
  <?php endif; ?>
</div>
