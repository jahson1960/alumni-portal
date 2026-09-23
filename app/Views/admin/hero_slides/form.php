<?php $isEdit = $slide !== null; ?>
<a href="<?= e(url('admin/hero-slides')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to Hero Slides</a>

<?php require dirname(dirname(__DIR__)) . '/partials/errors.php'; ?>

<form method="POST" action="<?= e($isEdit ? url('admin/hero-slides/' . $slide['id']) : url('admin/hero-slides')) ?>" enctype="multipart/form-data" class="card p-6 max-w-2xl space-y-4">
  <?= csrf_field() ?>

  <div class="flex items-center justify-between">
    <label class="flex items-center gap-2">
      <input type="checkbox" name="enabled" value="1" <?= ($slide === null || !empty($slide['enabled'])) ? 'checked' : '' ?> class="rounded border-slate-300 text-gold focus:ring-gold">
      <span class="text-sm text-slate-700">Show this slide</span>
    </label>
    <div class="w-28">
      <label class="form-label" for="sort_order">Order</label>
      <input type="number" id="sort_order" name="sort_order" class="form-input" value="<?= e($slide['sort_order'] ?? '') ?>">
    </div>
  </div>

  <div>
    <label class="form-label">Background Image<?= $isEdit ? '' : ' (required)' ?></label>
    <?php if (!empty($slide['image'])): ?>
      <img src="<?= e($slide['image']) ?>" alt="" class="w-40 h-24 object-cover rounded mb-2">
    <?php endif; ?>
    <input type="file" name="image" accept=".jpg,.jpeg,.png,.webp" class="text-sm block" <?= $isEdit ? '' : 'required' ?>>
  </div>

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="form-label" for="title">Title (line 1)</label>
      <input type="text" id="title" name="title" class="form-input" value="<?= e($slide['title'] ?? '') ?>">
    </div>
    <div>
      <label class="form-label" for="highlight">Highlight (gold line)</label>
      <input type="text" id="highlight" name="highlight" class="form-input" value="<?= e($slide['highlight'] ?? '') ?>">
    </div>
  </div>

  <div>
    <label class="form-label" for="subtitle">Subtitle</label>
    <input type="text" id="subtitle" name="subtitle" class="form-input" value="<?= e($slide['subtitle'] ?? '') ?>">
  </div>

  <div>
    <label class="form-label" for="description">Description</label>
    <textarea id="description" name="description" rows="2" class="form-textarea"><?= e($slide['description'] ?? '') ?></textarea>
  </div>

  <div>
    <label class="form-label" for="content_align">Content Alignment</label>
    <select id="content_align" name="content_align" class="form-input max-w-xs">
      <option value="left" <?= ($slide['content_align'] ?? 'left') === 'left' ? 'selected' : '' ?>>Left</option>
      <option value="center" <?= ($slide['content_align'] ?? '') === 'center' ? 'selected' : '' ?>>Center</option>
      <option value="right" <?= ($slide['content_align'] ?? '') === 'right' ? 'selected' : '' ?>>Right</option>
    </select>
  </div>

  <div class="border-t border-slate-100 pt-4">
    <h3 class="section-title text-xs mb-3">Slide Buttons</h3>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div class="space-y-2 border border-slate-200 rounded-lg p-3">
        <label class="form-label" for="btn1_text">Button 1 Text</label>
        <input type="text" id="btn1_text" name="btn1_text" class="form-input" placeholder="e.g. Explore Opportunities" value="<?= e($slide['btn1_text'] ?? '') ?>">
        <label class="form-label" for="btn1_link">Button 1 Link</label>
        <input type="text" id="btn1_link" name="btn1_link" class="form-input" placeholder="/jobs (leave blank for default)" value="<?= e($slide['btn1_link'] ?? '') ?>">
        <label class="flex items-center gap-2 text-sm text-slate-700">
          <input type="checkbox" name="btn1_show" value="1" <?= ($slide === null || !empty($slide['btn1_show'])) ? 'checked' : '' ?> class="rounded border-slate-300 text-gold focus:ring-gold">
          Show this button
        </label>
      </div>
      <div class="space-y-2 border border-slate-200 rounded-lg p-3">
        <label class="form-label" for="btn2_text">Button 2 Text</label>
        <input type="text" id="btn2_text" name="btn2_text" class="form-input" placeholder="e.g. Update Your Profile" value="<?= e($slide['btn2_text'] ?? '') ?>">
        <label class="form-label" for="btn2_link">Button 2 Link</label>
        <input type="text" id="btn2_link" name="btn2_link" class="form-input" placeholder="/register (leave blank for default)" value="<?= e($slide['btn2_link'] ?? '') ?>">
        <label class="flex items-center gap-2 text-sm text-slate-700">
          <input type="checkbox" name="btn2_show" value="1" <?= ($slide === null || !empty($slide['btn2_show'])) ? 'checked' : '' ?> class="rounded border-slate-300 text-gold focus:ring-gold">
          Show this button
        </label>
      </div>
    </div>
  </div>

  <button type="submit" class="btn-gold"><?= $isEdit ? 'Save Changes' : 'Add Slide' ?></button>
</form>
