<div class="max-w-[1400px] mx-auto px-4 md:px-8 py-8">
  <a href="<?= e(url('knowledge')) ?>" class="text-sm text-slate-500 hover:text-gold mb-4 inline-flex items-center gap-2"><i class="fa-solid fa-arrow-left"></i> Back to Knowledge Hub</a>

  <div class="relative rounded-2xl overflow-hidden mb-6 border border-amber-100 bg-amber-50">
    <div class="absolute inset-0 bg-cover bg-right" style="background-image: linear-gradient(90deg, #fffbeb 0%, #fffbeb 42%, rgba(255,251,235,0.55) 65%, rgba(255,251,235,0) 100%), url('https://images.unsplash.com/photo-1498050108023-c5249f4df085?auto=format&fit=crop&w=1200&q=80');"></div>
    <div class="relative p-6 md:p-8 max-w-xl">
      <div class="w-14 h-14 rounded-full bg-amber-100 text-gold flex items-center justify-center text-xl mb-4"><i class="fa-solid fa-pen-to-square"></i></div>
      <p class="text-xs font-extrabold text-gold uppercase tracking-wide mb-1.5">Share Your Expertise</p>
      <h1 class="text-2xl md:text-3xl font-extrabold text-primary-navy mb-3">Submit an Article</h1>
      <p class="text-sm text-slate-600">Share your insights, experiences and expertise with the RBS alumni community.</p>
      <p class="text-sm text-slate-600">Your article can inspire, inform and create value for fellow alumni around the world.</p>
    </div>
  </div>

  <?php require dirname(__DIR__) . '/partials/errors.php'; ?>

  <form method="POST" action="<?= e(url('knowledge/submit')) ?>" enctype="multipart/form-data" class="mb-12">
    <?= csrf_field() ?>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 items-start">
      <!-- Form -->
      <div class="lg:col-span-2">
        <div class="card p-5 md:p-6">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-5">
            <div>
              <label class="form-label" for="title">Article Title <span class="text-red-500">*</span></label>
              <input type="text" id="title" name="title" maxlength="150" placeholder="Enter a compelling title for your article..." class="form-input" required>
              <p class="text-right text-xs text-slate-400 mt-1"><span id="title-count">0</span>/150</p>
            </div>
            <div>
              <label class="form-label" for="category">Category <span class="text-red-500">*</span></label>
              <div class="relative">
                <i class="bi bi-grid absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-sm z-10"></i>
                <select id="category" name="category" class="form-input !pl-9" required>
                  <?php foreach ($categories as $cat): ?>
                    <option value="<?= e($cat) ?>"><?= e(article_category_label($cat)) ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
          </div>

          <div class="mb-5">
            <label class="form-label">Featured Image <span class="text-slate-400 font-normal">(Optional)</span></label>
            <div class="grid grid-cols-1 sm:grid-cols-[1fr_260px] gap-4">
              <label for="image" id="image-dropzone" class="border-2 border-dashed border-slate-200 rounded-lg py-7 px-4 text-center cursor-pointer hover:border-gold transition-colors flex flex-col items-center justify-center gap-1">
                <div id="image-preview" class="w-11 h-11 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-lg mb-1"><i class="fa-regular fa-image"></i></div>
                <span class="text-sm font-semibold text-primary-navy" id="image-upload-label">Click to upload or drag and drop</span>
                <span class="text-xs text-slate-400" id="image-upload-hint">JPG, PNG (max 3 MB)</span>
                <input type="file" id="image" name="image" accept=".jpg,.jpeg,.png" class="hidden">
              </label>
              <div class="rounded-lg bg-slate-50 p-4 flex items-start gap-3">
                <div class="w-9 h-9 rounded-lg bg-white text-slate-400 flex items-center justify-center flex-shrink-0 border border-slate-200"><i class="fa-regular fa-image"></i></div>
                <div>
                  <p class="text-sm font-bold text-primary-navy">Recommended size</p>
                  <p class="text-xs text-slate-500">1200 x 630 px</p>
                  <p class="text-xs text-slate-500 mt-1.5">A featured image makes your article more engaging and increases visibility.</p>
                </div>
              </div>
            </div>
          </div>

          <div>
            <label class="form-label">Article Content <span class="text-red-500">*</span></label>
            <div data-rich-editor="body"></div>
            <textarea id="body" name="body" class="hidden"></textarea>
            <p class="text-xs text-slate-400 mt-1" id="word-count">0 words</p>
          </div>

          <label class="flex items-start gap-2.5 mt-5 cursor-pointer">
            <input type="checkbox" name="original_confirm" value="1" class="mt-0.5 w-4 h-4 rounded border-slate-300 text-gold focus:ring-gold flex-shrink-0">
            <span class="text-xs text-slate-600">I confirm that this content is original and does not infringe on any copyright.</span>
          </label>

          <div class="flex flex-col sm:flex-row sm:items-center sm:justify-end gap-3 mt-6 pt-5 border-t border-slate-100">
            <button type="submit" name="action" value="draft" formnovalidate class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-5 !py-2.5 text-sm w-full sm:w-auto">Save as Draft</button>
            <button type="submit" name="action" value="submit" class="btn-gold !px-5 !py-2.5 text-sm flex items-center justify-center gap-2 w-full sm:w-auto"><i class="fa-regular fa-paper-plane"></i> Submit Article</button>
          </div>
        </div>
      </div>

      <!-- Sidebar -->
      <div class="lg:col-span-1 space-y-6 lg:sticky lg:top-[calc(var(--header-height)+1rem)] lg:self-start">
        <div class="card p-5">
          <div class="flex items-center gap-3 mb-4">
            <div class="w-10 h-10 rounded-full bg-amber-100 text-gold flex items-center justify-center flex-shrink-0"><i class="fa-regular fa-lightbulb"></i></div>
            <h3 class="text-sm font-bold text-primary-navy">Submission Guidelines</h3>
          </div>
          <ul class="space-y-3">
            <li class="flex items-start gap-2.5">
              <i class="fa-solid fa-circle-check text-gold mt-0.5"></i>
              <span class="text-xs text-slate-600">Keep your article relevant to the RBS community (e.g. career, leadership, industry insights, alumni stories).</span>
            </li>
            <li class="flex items-start gap-2.5">
              <i class="fa-solid fa-circle-check text-gold mt-0.5"></i>
              <span class="text-xs text-slate-600">Maintain a professional and respectful tone.</span>
            </li>
            <li class="flex items-start gap-2.5">
              <i class="fa-solid fa-circle-check text-gold mt-0.5"></i>
              <span class="text-xs text-slate-600">Include images where applicable (high quality and with proper attribution if not your own).</span>
            </li>
            <li class="flex items-start gap-2.5">
              <i class="fa-solid fa-circle-check text-gold mt-0.5"></i>
              <span class="text-xs text-slate-600">Your article will be reviewed by the alumni team before publishing.</span>
            </li>
          </ul>
        </div>

        <div class="card p-5">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-user-group"></i></div>
            <h3 class="text-sm font-bold text-primary-navy">Why Share?</h3>
          </div>
          <p class="text-xs font-semibold text-slate-500 mb-2">Your story can:</p>
          <ul class="space-y-2">
            <li class="flex items-center gap-2 text-xs text-slate-600"><i class="fa-solid fa-check text-sky-600"></i> Inspire fellow alumni</li>
            <li class="flex items-center gap-2 text-xs text-slate-600"><i class="fa-solid fa-check text-sky-600"></i> Build your professional brand</li>
            <li class="flex items-center gap-2 text-xs text-slate-600"><i class="fa-solid fa-check text-sky-600"></i> Create valuable networking opportunities</li>
            <li class="flex items-center gap-2 text-xs text-slate-600"><i class="fa-solid fa-check text-sky-600"></i> Give back to the RBS community</li>
          </ul>
        </div>

        <div class="card !bg-amber-50 !border-amber-100 p-5">
          <div class="flex items-center gap-3 mb-3">
            <div class="w-10 h-10 rounded-full bg-amber-100 text-gold flex items-center justify-center flex-shrink-0"><i class="fa-regular fa-envelope"></i></div>
            <h3 class="text-sm font-bold text-primary-navy">Need Help?</h3>
          </div>
          <p class="text-xs text-slate-600 mb-4">For any support or questions, contact the alumni team at <a href="mailto:<?= e(\App\Models\Setting::get('giving_contact_email', 'alumni@rbs.edu.ng')) ?>" class="text-gold hover:underline"><?= e(\App\Models\Setting::get('giving_contact_email', 'alumni@rbs.edu.ng')) ?></a>.</p>
          <a href="mailto:<?= e(\App\Models\Setting::get('giving_contact_email', 'alumni@rbs.edu.ng')) ?>" class="btn-gold !px-4 !py-2.5 text-xs w-full flex items-center justify-center gap-2"><i class="fa-regular fa-envelope"></i> Contact Alumni Team</a>
        </div>
      </div>
    </div>
  </form>

  <h3 id="my-submissions" class="section-title text-xs mb-3 scroll-mt-24">My Submissions</h3>
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    <?php foreach ($myArticles as $article): ?>
      <?php
        $statusClasses = [
          'published' => 'bg-green-100 text-green-700',
          'pending' => 'bg-amber-100 text-amber-700',
          'draft' => 'bg-slate-100 text-slate-500',
        ];
      ?>
      <div class="card p-4">
        <?php if (!empty($article['image'])): ?>
          <img src="<?= e($article['image']) ?>" alt="" class="w-full h-28 object-cover rounded-lg mb-3">
        <?php endif; ?>
        <h4 class="text-sm font-bold text-primary-navy mb-1"><?= e($article['title']) ?></h4>
        <span class="badge <?= e($statusClasses[$article['status']] ?? 'bg-slate-100 text-slate-500') ?>"><?= e(ucfirst($article['status'])) ?></span>
      </div>
    <?php endforeach; ?>
    <?php if (empty($myArticles)): ?>
      <p class="text-sm text-slate-400 col-span-full">You haven't submitted anything yet.</p>
    <?php endif; ?>
  </div>
</div>

<script>
  (function () {
    var titleInput = document.getElementById('title');
    var titleCount = document.getElementById('title-count');
    if (titleInput && titleCount) {
      titleInput.addEventListener('input', function () {
        titleCount.textContent = titleInput.value.length;
      });
    }

    var imageInput = document.getElementById('image');
    var imagePreview = document.getElementById('image-preview');
    var imageLabel = document.getElementById('image-upload-label');
    var imageHint = document.getElementById('image-upload-hint');
    var dropzone = document.getElementById('image-dropzone');
    if (imageInput && imagePreview) {
      imageInput.addEventListener('change', function () {
        var file = imageInput.files && imageInput.files[0];
        if (!file) return;
        imagePreview.innerHTML = '<img src="' + URL.createObjectURL(file) + '" alt="" class="w-11 h-11 rounded-lg object-cover">';
        if (imageLabel) imageLabel.textContent = file.name;
        if (imageHint) imageHint.textContent = 'Click to choose a different image';
      });
    }
    if (dropzone && imageInput) {
      ['dragover', 'dragleave', 'drop'].forEach(function (evt) {
        dropzone.addEventListener(evt, function (e) { e.preventDefault(); });
      });
      dropzone.addEventListener('dragover', function () { dropzone.classList.add('border-gold'); });
      dropzone.addEventListener('dragleave', function () { dropzone.classList.remove('border-gold'); });
      dropzone.addEventListener('drop', function (e) {
        dropzone.classList.remove('border-gold');
        var file = e.dataTransfer.files && e.dataTransfer.files[0];
        if (!file) return;
        imageInput.files = e.dataTransfer.files;
        imageInput.dispatchEvent(new Event('change'));
      });
    }

    // Quill is set up by admin.js on DOMContentLoaded; waiting for window `load`
    // guarantees that handler has already run and window.richEditors.body exists.
    window.addEventListener('load', function () {
      var quill = window.richEditors && window.richEditors.body;
      var wordCount = document.getElementById('word-count');
      if (!quill || !wordCount) return;
      var updateCount = function () {
        var words = quill.getText().trim();
        wordCount.textContent = (words === '' ? 0 : words.split(/\s+/).length) + ' words';
      };
      quill.on('text-change', updateCount);
      updateCount();
    });
  })();
</script>
