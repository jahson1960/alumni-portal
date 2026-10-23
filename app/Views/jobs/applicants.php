<?php
$statusMeta = [
    'new' => ['label' => 'New', 'class' => 'bg-sky-100 text-sky-700'],
    'reviewed' => ['label' => 'Reviewed', 'class' => 'bg-amber-100 text-amber-700'],
    'shortlisted' => ['label' => 'Shortlisted', 'class' => 'bg-emerald-100 text-emerald-700'],
    'rejected' => ['label' => 'Rejected', 'class' => 'bg-red-100 text-red-700'],
];
?>
<div class="max-w-[960px] mx-auto px-4 md:px-8 py-8">
  <a href="<?= e(url('jobs/' . $job['id'])) ?>" class="text-sm text-slate-500 hover:text-gold mb-4 inline-flex items-center gap-2"><i class="fa-solid fa-arrow-left"></i> Back to Job</a>

  <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
      <h1 class="text-xl font-extrabold text-primary-navy">Applicants</h1>
      <p class="text-sm text-slate-500 mt-1"><?= e($job['title']) ?> &bull; <?= count($applicants) ?> applicant<?= count($applicants) === 1 ? '' : 's' ?></p>
    </div>
  </div>

  <div class="space-y-4">
    <?php foreach ($applicants as $applicant): $meta = $statusMeta[$applicant['status']] ?? $statusMeta['new']; ?>
      <div class="card p-5">
        <div class="flex flex-wrap items-start justify-between gap-3 mb-3">
          <a href="<?= e(url('alumni/' . $applicant['user_id'])) ?>" class="flex items-center gap-3 min-w-0">
            <?= avatar_html(['name' => $applicant['applicant_name'], 'avatar' => $applicant['applicant_avatar']], 'w-11 h-11 flex-shrink-0') ?>
            <div class="min-w-0">
              <p class="text-sm font-bold text-primary-navy truncate"><?= e($applicant['applicant_name']) ?></p>
              <p class="text-xs text-slate-500 truncate"><?= e($applicant['applicant_email']) ?><?= $applicant['applicant_headline'] ? ' &bull; ' . e($applicant['applicant_headline']) : '' ?></p>
            </div>
          </a>
          <div class="flex items-center gap-2 flex-shrink-0">
            <span class="badge <?= e($meta['class']) ?>"><?= e($meta['label']) ?></span>
            <p class="text-[0.68rem] text-slate-400"><?= e(time_ago($applicant['created_at'])) ?></p>
          </div>
        </div>

        <?php if ($applicant['responses']): ?>
          <?php
            $responsesBySection = ['profile_info' => [], 'education' => [], 'experience' => []];
            foreach ($applicant['responses'] as $response) {
                $responsesBySection[$response['section'] ?? 'profile_info'][] = $response;
            }
          ?>
          <div class="space-y-5 pl-14">
            <?php foreach (\App\Models\ApplicationTemplate::SECTIONS as $sectionKey => $sectionLabel): ?>
              <?php if (!empty($responsesBySection[$sectionKey])): ?>
                <div class="space-y-3">
                  <p class="text-[0.65rem] font-extrabold text-slate-400 uppercase tracking-wide"><?= e($sectionLabel) ?></p>
                  <?php foreach ($responsesBySection[$sectionKey] as $response): ?>
                    <div>
                      <p class="text-xs font-semibold text-slate-500"><?= e($response['label']) ?></p>
                      <?php if ($response['type'] === 'file'): ?>
                        <a href="<?= e($response['value']) ?>" target="_blank" rel="noopener noreferrer" class="text-sm text-gold hover:underline inline-flex items-center gap-1.5"><i class="fa-regular fa-file-lines"></i> View file</a>
                      <?php elseif ($response['type'] === 'url'): ?>
                        <a href="<?= e($response['value']) ?>" target="_blank" rel="noopener noreferrer" class="text-sm text-gold hover:underline break-all"><?= e($response['value']) ?></a>
                      <?php else: ?>
                        <p class="text-sm text-slate-700 whitespace-pre-line"><?= e($response['value']) ?></p>
                      <?php endif; ?>
                    </div>
                  <?php endforeach; ?>
                </div>
              <?php endif; ?>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <p class="text-xs text-slate-400 pl-14">Clicked Apply — no additional answers submitted with this method.</p>
        <?php endif; ?>

        <form method="POST" action="<?= e(url('jobs/' . $job['id'] . '/applicants/' . $applicant['id'] . '/status')) ?>" class="flex items-center gap-2 mt-4 pt-3 border-t border-slate-100 pl-14">
          <?= csrf_field() ?>
          <label class="text-xs text-slate-500 flex-shrink-0" for="status_<?= e((string) $applicant['id']) ?>">Status</label>
          <select id="status_<?= e((string) $applicant['id']) ?>" name="status" class="form-input !py-1.5 text-xs max-w-[160px]">
            <?php foreach ($statusMeta as $key => $m): ?>
              <option value="<?= e($key) ?>" <?= $applicant['status'] === $key ? 'selected' : '' ?>><?= e($m['label']) ?></option>
            <?php endforeach; ?>
          </select>
          <button type="submit" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-3 !py-1.5 text-xs">Update</button>
        </form>
      </div>
    <?php endforeach; ?>
    <?php if (empty($applicants)): ?>
      <p class="text-sm text-slate-400">No one has applied yet.</p>
    <?php endif; ?>
  </div>
</div>
