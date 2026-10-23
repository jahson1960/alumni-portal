<?php
$isEdit = $template !== null;
$fieldsBySection = array_fill_keys(array_keys(\App\Models\ApplicationTemplate::SECTIONS), []);
foreach ($fields as $field) {
    $fieldsBySection[$field['section']][] = $field;
}
$sectionHints = [
    'profile_info' => 'Basic contact questions, plus any other general question you want to ask.',
    'education' => 'Questions about the candidate\'s education. On the Quick Apply template, this section also shows their education history pulled straight from their profile.',
    'experience' => 'Questions about the candidate\'s work history. On the Quick Apply template, this section also shows their work experience pulled straight from their profile.',
    'additional_info' => 'Optional extras like salary expectation or notice period. Candidates can skip anything here.',
];
?>
<div class="max-w-[900px] mx-auto px-4 md:px-8 py-8">
  <a href="<?= e(url('application-templates')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to Templates</a>

  <h1 class="text-xl font-extrabold text-primary-navy mb-1"><?= $isEdit ? 'Edit Application Template' : 'New Application Template' ?></h1>
  <p class="text-sm text-slate-500 mb-6">Add the questions you want every candidate to answer, grouped into Profile Info, Education and Experience. You can reuse this template on any job post.</p>

  <?php require dirname(__DIR__) . '/partials/errors.php'; ?>

  <form method="POST" action="<?= $isEdit ? e(url('application-templates/' . $template['id'])) : e(url('application-templates')) ?>" class="card p-6 space-y-6">
    <?= csrf_field() ?>

    <div>
      <label class="form-label" for="name">Template Name</label>
      <input type="text" id="name" name="name" class="form-input max-w-md" placeholder="e.g. Standard Application" value="<?= e($template['name'] ?? '') ?>" required>
    </div>

    <?php foreach (\App\Models\ApplicationTemplate::SECTIONS as $sectionKey => $sectionLabel): ?>
      <div class="pt-4 border-t border-slate-100">
        <label class="form-label"><?= e($sectionLabel) ?></label>
        <p class="text-xs text-slate-400 mb-3"><?= e($sectionHints[$sectionKey]) ?></p>
        <div id="template-fields-<?= e($sectionKey) ?>" class="space-y-3">
          <?php foreach ($fieldsBySection[$sectionKey] as $field): ?>
            <div class="template-field-row card !bg-slate-50 p-3">
              <input type="hidden" name="field_section[]" value="<?= e($sectionKey) ?>">
              <?php if (!empty($field['profile_field'])): ?>
                <span class="badge bg-sky-100 text-sky-700 mb-2 inline-flex items-center gap-1"><i class="fa-solid fa-id-badge"></i> Prefills from the candidate's profile</span>
                <input type="hidden" name="field_profile[]" value="<?= e($field['profile_field']) ?>">
              <?php else: ?>
                <input type="hidden" name="field_profile[]" value="">
              <?php endif; ?>
              <div class="grid grid-cols-1 sm:grid-cols-[1fr_160px_110px_auto] gap-2 items-start">
                <input type="text" name="field_label[]" class="form-input text-sm" placeholder="Question / label" value="<?= e($field['label']) ?>" required>
                <select name="field_type[]" class="form-input text-sm field-type-select">
                  <?php foreach (\App\Models\ApplicationTemplate::FIELD_TYPES as $type): ?>
                    <option value="<?= e($type) ?>" <?= $field['field_type'] === $type ? 'selected' : '' ?>><?= e(ucfirst($type)) ?></option>
                  <?php endforeach; ?>
                </select>
                <select name="field_required[]" class="form-input text-sm">
                  <option value="0" <?= !$field['is_required'] ? 'selected' : '' ?>>Optional</option>
                  <option value="1" <?= $field['is_required'] ? 'selected' : '' ?>>Required</option>
                </select>
                <button type="button" class="remove-field-btn btn bg-white border border-slate-200 !text-red-600 hover:bg-red-50 !px-3 !py-2 text-xs">Remove</button>
              </div>
              <input type="text" name="field_options[]" class="form-input text-sm mt-2 field-options-input <?= $field['field_type'] === 'select' ? '' : 'hidden' ?>" placeholder="Choices, comma-separated (e.g. Yes, No, Maybe)" value="<?= e($field['options'] ?? '') ?>">
            </div>
          <?php endforeach; ?>
        </div>
        <button type="button" class="add-field-btn btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-3 !py-2 text-xs mt-3" data-section="<?= e($sectionKey) ?>">+ Add Question to <?= e($sectionLabel) ?></button>
      </div>
    <?php endforeach; ?>

    <div class="pt-4 border-t border-slate-100">
      <button type="submit" class="btn-gold !px-5 !py-2.5 text-sm"><?= $isEdit ? 'Save Template' : 'Create Template' ?></button>
    </div>
  </form>
</div>

<template id="template-field-row-template">
  <div class="template-field-row card !bg-slate-50 p-3">
    <input type="hidden" name="field_section[]" value="">
    <input type="hidden" name="field_profile[]" value="">
    <div class="grid grid-cols-1 sm:grid-cols-[1fr_160px_110px_auto] gap-2 items-start">
      <input type="text" name="field_label[]" class="form-input text-sm" placeholder="Question / label" required>
      <select name="field_type[]" class="form-input text-sm field-type-select">
        <?php foreach (\App\Models\ApplicationTemplate::FIELD_TYPES as $type): ?>
          <option value="<?= e($type) ?>"><?= e(ucfirst($type)) ?></option>
        <?php endforeach; ?>
      </select>
      <select name="field_required[]" class="form-input text-sm">
        <option value="0" selected>Optional</option>
        <option value="1">Required</option>
      </select>
      <button type="button" class="remove-field-btn btn bg-white border border-slate-200 !text-red-600 hover:bg-red-50 !px-3 !py-2 text-xs">Remove</button>
    </div>
    <input type="text" name="field_options[]" class="form-input text-sm mt-2 field-options-input hidden" placeholder="Choices, comma-separated (e.g. Yes, No, Maybe)">
  </div>
</template>

<script>
  (function () {
    var rowTemplate = document.getElementById('template-field-row-template');

    function wireRow(row) {
      var typeSelect = row.querySelector('.field-type-select');
      var optionsInput = row.querySelector('.field-options-input');
      typeSelect.addEventListener('change', function () {
        optionsInput.classList.toggle('hidden', typeSelect.value !== 'select');
      });
      row.querySelector('.remove-field-btn').addEventListener('click', function () {
        row.remove();
      });
    }

    document.querySelectorAll('.template-field-row').forEach(wireRow);

    document.querySelectorAll('.add-field-btn').forEach(function (addBtn) {
      addBtn.addEventListener('click', function () {
        var container = document.getElementById('template-fields-' + addBtn.dataset.section);
        var clone = rowTemplate.content.cloneNode(true);
        clone.querySelector('input[name="field_section[]"]').value = addBtn.dataset.section;
        container.appendChild(clone);
        wireRow(container.lastElementChild);
      });
    });
  })();
</script>
