<div class="card p-6 max-w-2xl">
  <h3 class="section-title text-xs mb-1">SMTP Connection</h3>
  <p class="text-xs text-slate-400 mb-5">The server this app connects to in order to actually send mail. This is read-only here — it's set in <code>config/config.php</code> on the server (under the <code>'mail'</code> key), not in the database, so it has to be edited directly on each environment (local vs. production each have their own copy of that file).</p>

  <?php if (empty($mailConfig['host']) || empty($mailConfig['username'])): ?>
    <div class="bg-red-50 border border-red-200 text-red-800 text-sm rounded px-4 py-3 mb-4">
      <i class="fa-solid fa-triangle-exclamation"></i> Mail is not configured — <code>config/config.php</code> is missing an SMTP host/username. No email will be able to send until this is set.
    </div>
  <?php endif; ?>

  <dl class="grid grid-cols-2 gap-4 text-sm">
    <div><dt class="text-slate-400 text-xs">Host</dt><dd class="text-slate-700 font-mono"><?= e($mailConfig['host'] ?? '—') ?></dd></div>
    <div><dt class="text-slate-400 text-xs">Port</dt><dd class="text-slate-700 font-mono"><?= e((string) ($mailConfig['port'] ?? '—')) ?></dd></div>
    <div><dt class="text-slate-400 text-xs">Encryption</dt><dd class="text-slate-700 font-mono uppercase"><?= e($mailConfig['encryption'] ?? '—') ?></dd></div>
    <div><dt class="text-slate-400 text-xs">Username</dt><dd class="text-slate-700 font-mono"><?= e($mailConfig['username'] ?? '—') ?></dd></div>
    <div><dt class="text-slate-400 text-xs">Password</dt><dd class="text-slate-700 font-mono"><?= e($mailConfig['password'] !== '' ? $mailConfig['password'] : '—') ?></dd></div>
    <div><dt class="text-slate-400 text-xs">From Email</dt><dd class="text-slate-700 font-mono"><?= e($mailConfig['from_email'] ?? '—') ?></dd></div>
    <div class="col-span-2"><dt class="text-slate-400 text-xs">From Name</dt><dd class="text-slate-700"><?= e($mailConfig['from_name'] ?? '—') ?></dd></div>
  </dl>
</div>
