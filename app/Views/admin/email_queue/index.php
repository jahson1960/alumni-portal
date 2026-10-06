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
          <td class="font-medium text-primary-navy"><?= e($email['to_name'] ?: $email['to_email']) ?><br><span class="text-xs text-slate-400"><?= e($email['to_email']) ?></span></td>
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
              <form method="POST" action="<?= e(url('admin/email-queue/' . $email['id'] . '/retry')) ?>">
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
