<div class="flex justify-between items-center mb-4">
  <p class="text-sm text-slate-500"><?= number_format($totalCount) ?> email<?= $totalCount === 1 ? '' : 's' ?> queued</p>
</div>

<p class="text-sm text-slate-500 mb-4">Every email the app sends — account credentials, password resets, and notification emails — passes through this queue. A background worker sends <code>pending</code> rows; a row flips to <code>failed</code> after 5 failed attempts and can be retried below.</p>

<div class="card overflow-x-auto">
  <table class="table-base">
    <thead>
      <tr>
        <th>To</th>
        <th>Subject</th>
        <th>Status</th>
        <th>Attempts</th>
        <th>Created</th>
        <th>Sent</th>
        <th>Last Error</th>
        <th></th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($emails as $email): ?>
        <tr>
          <td class="font-medium text-primary-navy">
            <?= e($email['to_name'] ?: $email['to_email']) ?><br><span class="text-xs text-slate-400"><?= e($email['to_email']) ?></span>
            <?php if (!empty($email['bcc_email'])): ?><br><span class="text-[0.65rem] text-sky-600"><i class="fa-regular fa-eye"></i> BCC: <?= e($email['bcc_email']) ?></span><?php endif; ?>
          </td>
          <td class="text-sm text-slate-600"><?= e($email['subject']) ?></td>
          <td>
            <?php if ($email['status'] === 'sent'): ?>
              <span class="badge-green">Sent</span>
            <?php elseif ($email['status'] === 'failed'): ?>
              <span class="badge bg-red-100 text-red-700">Failed</span>
            <?php else: ?>
              <span class="badge-gold">Pending</span>
            <?php endif; ?>
          </td>
          <td class="text-sm text-slate-500"><?= e((string) $email['attempts']) ?></td>
          <td class="text-xs text-slate-400"><?= e(format_date($email['created_at'], 'M j, Y g:ia')) ?></td>
          <td class="text-xs text-slate-400"><?= $email['sent_at'] ? e(format_date($email['sent_at'], 'M j, Y g:ia')) : '—' ?></td>
          <td class="text-xs text-red-500 max-w-xs truncate" title="<?= e((string) $email['last_error']) ?>"><?= e($email['last_error'] ?: '—') ?></td>
          <td>
            <?php if ($email['status'] === 'failed'): ?>
              <form method="POST" action="<?= e(url('admin/emails/' . $email['id'] . '/retry')) ?>">
                <?= csrf_field() ?>
                <button type="submit" class="btn bg-sky-100 text-sky-700 hover:bg-sky-200 !px-3 !py-1 text-xs">Retry</button>
              </form>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$emails): ?>
        <tr><td colspan="8" class="text-center text-sm text-slate-400 py-6">No emails have been queued yet.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php if ($totalPages > 1): ?>
  <?php $qs = function (array $overrides) {
      $params = array_merge(['tab' => 'queue'], $overrides);
      return '?' . http_build_query($params);
  }; ?>
  <div class="flex items-center justify-between mt-4">
    <p class="text-xs text-slate-400">Showing <?= number_format(($page - 1) * $perPage + 1) ?>&ndash;<?= number_format(min($page * $perPage, $totalCount)) ?> of <?= number_format($totalCount) ?></p>
    <div class="flex items-center gap-1.5">
      <a href="<?= e(url('admin/emails') . $qs(['page' => max(1, $page - 1)])) ?>" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:text-primary-navy <?= $page <= 1 ? 'pointer-events-none opacity-40' : '' ?>"><i class="fa-solid fa-arrow-left text-xs"></i></a>
      <?php for ($p = 1; $p <= $totalPages; $p++): ?>
        <a href="<?= e(url('admin/emails') . $qs(['page' => $p])) ?>" class="w-8 h-8 rounded-lg flex items-center justify-center text-xs font-semibold <?= $p === $page ? 'bg-gold text-white' : 'border border-slate-200 text-slate-600 hover:bg-slate-50' ?>"><?= $p ?></a>
      <?php endfor; ?>
      <a href="<?= e(url('admin/emails') . $qs(['page' => min($totalPages, $page + 1)])) ?>" class="w-8 h-8 rounded-lg border border-slate-200 flex items-center justify-center text-slate-400 hover:text-primary-navy <?= $page >= $totalPages ? 'pointer-events-none opacity-40' : '' ?>"><i class="fa-solid fa-arrow-right text-xs"></i></a>
    </div>
  </div>
<?php endif; ?>
