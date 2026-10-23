<?php $isEdit = $template !== null; ?>
<div class="max-w-[900px] mx-auto px-4 md:px-8 py-8">
  <a href="<?= e(url('application-templates')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to Templates</a>

  <h1 class="text-xl font-extrabold text-primary-navy mb-1"><?= $isEdit ? 'Edit Application Template' : 'New Application Template' ?></h1>
  <p class="text-sm text-slate-500 mb-6">Add the questions you want every candidate to answer. You can reuse this template on any job post.</p>

  <?php require dirname(__DIR__) . '/partials/errors.php'; ?>

  <form method="POST" action="<?= $isEdit ? e(url('application-templates/' . $template['id'])) : e(url('application-templates')) ?>" class="card p-6 space-y-5">
    <?= csrf_field() ?>

    <div>
      <label class="form-label" for="name">Template Name</label>
      <input type="text" id="name" name="name" class="form-input max-w-md" placeholder="e.g. Standard Application" value="<?= e($template['name'] ?? '') ?>" required>
    </div>

    <div>
      <label class="form-label">Questions</label>
      <div id="template-fields" class="space-y-3">
        <?php foreach ($fields as $field): ?>
          <div class="template-field-row card !bg-slate-50 p-3">
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
      <button type="button" id="add-field-btn" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-3 !py-2 text-xs mt-3">+ Add Question</button>
      <p class="text-xs text-slate-400 mt-2">Every application already asks for the candidate's name and email from their profile — you only need to add extra questions here (e.g. years of experience, a portfolio link, a resume upload).</p>
    </div>

    <div class="pt-4 border-t border-slate-100">
      <button type="submit" class="btn-gold !px-5 !py-2.5 text-sm"><?= $isEdit ? 'Save Template' : 'Create Template' ?></button>
    </div>
  </form>
</div>

<template id="template-field-row-template">
  <div class="template-field-row card !bg-slate-50 p-3">
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
    var container = document.getElementById('template-fields');
    var addBtn = document.getElementById('add-field-btn');
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

    container.querySelectorAll('.template-field-row').forEach(wireRow);

    addBtn.addEventListener('click', function () {
      var clone = rowTemplate.content.cloneNode(true);
      container.appendChild(clone);
      wireRow(container.lastElementChild);
    });
  })();
</script>
