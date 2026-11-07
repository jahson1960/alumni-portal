<a href="<?= e(url('admin/alumni-roster')) ?>" class="text-xs text-slate-500 hover:text-gold mb-4 inline-block"><i class="fa-solid fa-arrow-left"></i> Back to <?= e(term_plural()) ?> Roster</a>

<?php require dirname(dirname(__DIR__)) . '/partials/errors.php'; ?>

<div class="card p-6 max-w-2xl space-y-4">
  <div>
    <h3 class="text-sm font-bold text-primary-navy mb-1">Import <?= e(term_plural()) ?> Roster</h3>
    <p class="text-xs text-slate-500">Upload a .csv or .xlsx file with one row per <?= e(term_singular_lc()) ?>. The first row must be column headers — <strong>Matric Number</strong>, <strong>Full Name</strong>, <strong>Graduation Year</strong>, and <strong>Cohort</strong> are required; <strong>Email</strong> and <strong>Program</strong> are optional (an email lets you register that row with one click afterward). A matric number already on the roster is updated from the file unless it's already been claimed by a registered account — claimed rows are always left untouched.</p>
  </div>

  <a href="<?= e(url('admin/alumni-roster/import/sample')) ?>" class="text-xs text-gold hover:underline inline-flex items-center gap-1"><i class="fa-solid fa-download"></i> Download a sample template</a>

  <form method="POST" action="<?= e(url('admin/alumni-roster/import')) ?>" enctype="multipart/form-data" class="space-y-4 pt-2 border-t border-slate-100">
    <?= csrf_field() ?>
    <div>
      <label class="form-label" for="roster_file">Spreadsheet File</label>
      <input type="file" id="roster_file" name="roster_file" accept=".csv,.xlsx" class="text-sm block" required>
      <p class="text-xs text-slate-400 mt-1">Only the first sheet is read. Max 5MB.</p>
    </div>
    <button type="submit" class="btn-gold">Upload &amp; Import</button>
  </form>
</div>
