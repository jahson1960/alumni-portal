<?php
/** @var array $field */
/** @var string $prev */
/** @var array $inputTypes */
$isSnapshot = in_array($field['profile_field'], ['education', 'experience'], true);
$fieldIcons = ['text' => 'fa-regular fa-pen-to-square', 'email' => 'fa-regular fa-envelope', 'phone' => 'fa-solid fa-phone', 'number' => 'fa-solid fa-hashtag', 'url' => 'fa-solid fa-link'];
$fid = 'field_' . (string) $field['id'];
?>
<div>
  <label class="flex items-center gap-2 text-sm font-semibold text-primary-navy mb-1.5" <?= $isSnapshot ? '' : 'for="' . e($fid) . '"' ?>>
    <?= e($field['label']) ?>
    <?php if ($field['is_required'] && !$isSnapshot): ?><span class="text-gold">*</span><?php endif; ?>
    <?php if ($field['profile_field'] && trim($prev) !== ''): ?>
      <span class="badge !bg-sky-50 !text-sky-600 !text-[0.6rem] font-semibold inline-flex items-center gap-1"><i class="fa-solid fa-user text-[0.55rem]"></i> From your profile</span>
    <?php endif; ?>
  </label>

  <?php if ($isSnapshot): ?>
    <?php if (trim($prev) === ''): ?>
      <div class="flex items-start gap-3 text-sm text-slate-400 border border-dashed border-slate-200 rounded-xl p-4">
        <i class="<?= $field['profile_field'] === 'education' ? 'fa-solid fa-graduation-cap' : 'fa-solid fa-briefcase' ?> text-slate-300 mt-0.5"></i>
        <span>Nothing on your profile yet. <a href="<?= e(url('profile/edit')) ?>" class="text-gold hover:underline font-semibold">Add it on your profile</a> to include it here.</span>
      </div>
    <?php else: ?>
      <div class="flex items-start gap-3 text-sm text-slate-700 whitespace-pre-line bg-slate-50 border border-slate-100 rounded-xl p-4">
        <i class="<?= $field['profile_field'] === 'education' ? 'fa-solid fa-graduation-cap' : 'fa-solid fa-briefcase' ?> text-slate-400 mt-0.5 flex-shrink-0"></i>
        <span><?= e($prev) ?></span>
      </div>
      <input type="hidden" name="<?= e($fid) ?>" value="<?= e($prev) ?>">
    <?php endif; ?>
    <p class="text-xs text-slate-400 mt-1.5"><a href="<?= e(url('profile/edit')) ?>" class="text-gold hover:underline">Update your profile</a> to change this.</p>

  <?php elseif ($field['field_type'] === 'textarea'): ?>
    <textarea id="<?= e($fid) ?>" name="<?= e($fid) ?>" rows="4" class="form-textarea !rounded-xl !border-slate-200 !py-2.5" <?= $field['is_required'] ? 'required' : '' ?>><?= e($prev) ?></textarea>

  <?php elseif ($field['field_type'] === 'select'): ?>
    <select id="<?= e($fid) ?>" name="<?= e($fid) ?>" class="form-input !rounded-xl !border-slate-200 !py-2.5" <?= $field['is_required'] ? 'required' : '' ?>>
      <option value="">Select an option&hellip;</option>
      <?php foreach (array_filter(array_map('trim', explode(',', (string) $field['options']))) as $choice): ?>
        <option value="<?= e($choice) ?>" <?= $prev === $choice ? 'selected' : '' ?>><?= e($choice) ?></option>
      <?php endforeach; ?>
    </select>

  <?php elseif ($field['field_type'] === 'file'): ?>
    <input type="file" id="<?= e($fid) ?>" name="<?= e($fid) ?>" accept=".pdf,.doc,.docx" class="hidden apply-doc-input" <?= $field['is_required'] && !$prev ? 'required' : '' ?>>

    <div class="apply-doc-dropzone border-2 border-dashed border-slate-200 rounded-xl px-5 py-6 text-center cursor-pointer transition-colors hover:border-gold hover:bg-gold/5">
      <i class="fa-solid fa-cloud-arrow-up text-2xl text-slate-300 mb-1.5"></i>
      <p class="text-sm font-semibold text-slate-600">Drag &amp; drop your file, or <span class="text-gold">browse</span></p>
      <p class="text-xs text-slate-400 mt-1">PDF, DOC, or DOCX &bull; Max 5MB</p>
    </div>

    <div class="apply-doc-preview hidden items-center gap-3 border border-slate-200 rounded-xl px-4 py-3">
      <div class="w-10 h-10 rounded-lg bg-rose-50 text-rose-500 flex items-center justify-center flex-shrink-0"><i class="fa-regular fa-file-lines"></i></div>
      <div class="min-w-0 flex-1">
        <p class="apply-doc-filename text-sm font-medium text-slate-700 truncate"></p>
        <p class="apply-doc-filesize text-xs text-slate-400"></p>
      </div>
      <button type="button" class="apply-doc-remove text-slate-400 hover:text-red-500 flex-shrink-0" title="Remove"><i class="fa-solid fa-xmark text-lg"></i></button>
    </div>

    <p class="apply-doc-error hidden items-center gap-1.5 text-xs text-red-500 mt-1.5"><i class="fa-solid fa-circle-exclamation"></i> <span></span></p>

    <?php if ($prev): ?>
      <p class="text-xs text-slate-400 mt-1.5 flex items-center gap-1.5"><i class="fa-regular fa-circle-check text-emerald-500"></i> <?= $field['profile_field'] === 'resume' ? 'Using your profile resume' : 'Previously submitted' ?>: <a href="<?= e($prev) ?>" target="_blank" rel="noopener noreferrer" class="text-gold hover:underline font-medium">view file</a> &mdash; choose a new file above to replace it, or leave blank to keep it.</p>
    <?php endif; ?>

  <?php else: ?>
    <div class="relative">
      <?php if (isset($fieldIcons[$field['field_type']])): ?>
        <i class="<?= e($fieldIcons[$field['field_type']]) ?> absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-300 text-sm pointer-events-none"></i>
      <?php endif; ?>
      <input type="<?= e($inputTypes[$field['field_type']] ?? 'text') ?>" id="<?= e($fid) ?>" name="<?= e($fid) ?>" class="form-input !rounded-xl !border-slate-200 !py-2.5 <?= isset($fieldIcons[$field['field_type']]) ? '!pl-10' : '' ?>" value="<?= e($prev) ?>" <?= $field['is_required'] ? 'required' : '' ?>>
    </div>
  <?php endif; ?>
</div>

<?php if ($field['field_type'] === 'file' && !$isSnapshot): ?>
  <script>
    (function () {
      var wrapper = document.getElementById('<?= e($fid) ?>').closest('div');
      var input = wrapper.querySelector('.apply-doc-input');
      var dropzone = wrapper.querySelector('.apply-doc-dropzone');
      var preview = wrapper.querySelector('.apply-doc-preview');
      var filenameEl = wrapper.querySelector('.apply-doc-filename');
      var filesizeEl = wrapper.querySelector('.apply-doc-filesize');
      var removeBtn = wrapper.querySelector('.apply-doc-remove');
      var errorEl = wrapper.querySelector('.apply-doc-error');
      var ALLOWED_EXT = ['pdf', 'doc', 'docx'];
      var MAX_SIZE = 5 * 1024 * 1024;

      function showError(msg) {
        errorEl.querySelector('span').textContent = msg;
        errorEl.classList.remove('hidden');
        errorEl.classList.add('flex');
      }
      function clearError() {
        errorEl.classList.add('hidden');
        errorEl.classList.remove('flex');
      }
      function showPreview(name, sizeText) {
        dropzone.classList.add('hidden');
        preview.classList.remove('hidden');
        preview.classList.add('flex');
        filenameEl.textContent = name;
        filesizeEl.textContent = sizeText;
      }
      function hidePreview() {
        preview.classList.add('hidden');
        preview.classList.remove('flex');
        dropzone.classList.remove('hidden');
      }
      function handleFile(file) {
        clearError();
        var ext = (file.name.split('.').pop() || '').toLowerCase();
        if (ALLOWED_EXT.indexOf(ext) === -1) {
          showError('Only PDF, DOC, or DOCX files are allowed.');
          input.value = '';
          return;
        }
        if (file.size > MAX_SIZE) {
          showError('File must be smaller than 5MB.');
          input.value = '';
          return;
        }
        showPreview(file.name, Math.max(1, Math.round(file.size / 1024)) + ' KB');
      }

      dropzone.addEventListener('click', function () { input.click(); });
      input.addEventListener('change', function () {
        if (this.files && this.files[0]) handleFile(this.files[0]);
      });
      ['dragenter', 'dragover'].forEach(function (evt) {
        dropzone.addEventListener(evt, function (e) {
          e.preventDefault();
          e.stopPropagation();
          dropzone.classList.add('border-gold', 'bg-gold/5');
        });
      });
      ['dragleave', 'drop'].forEach(function (evt) {
        dropzone.addEventListener(evt, function (e) {
          e.preventDefault();
          e.stopPropagation();
          dropzone.classList.remove('border-gold', 'bg-gold/5');
        });
      });
      dropzone.addEventListener('drop', function (e) {
        var files = e.dataTransfer.files;
        if (files && files[0]) {
          input.files = files;
          handleFile(files[0]);
        }
      });
      removeBtn.addEventListener('click', function (e) {
        e.stopPropagation();
        input.value = '';
        clearError();
        hidePreview();
      });
    })();
  </script>
<?php endif; ?>
