<?php require dirname(__DIR__, 3) . '/partials/errors.php'; ?>

<form method="POST" action="<?= e(url('admin/emails/connection')) ?>" class="card p-6 max-w-2xl">
  <?= csrf_field() ?>
  <h3 class="section-title text-xs mb-1">SMTP Connection</h3>
  <p class="text-xs text-slate-400 mb-5">The server this app connects to in order to actually send mail. Any field you save here overrides the default in <code>config/config.php</code> on this environment; leaving a field blank falls back to that file's value for it.</p>

  <?php if (empty($mailConfig['host']) || empty($mailConfig['username'])): ?>
    <div class="bg-red-50 border border-red-200 text-red-800 text-sm rounded px-4 py-3 mb-4">
      <i class="fa-solid fa-triangle-exclamation"></i> Mail is not configured — an SMTP host and username are required. No email will be able to send until these are set.
    </div>
  <?php endif; ?>

  <div class="grid grid-cols-2 gap-4">
    <div>
      <label class="form-label" for="smtp_host">Host</label>
      <input type="text" id="smtp_host" name="smtp_host" class="form-input" placeholder="smtp.example.com" value="<?= e($mailConfig['host'] ?? '') ?>">
    </div>
    <div>
      <label class="form-label" for="smtp_port">Port</label>
      <input type="number" id="smtp_port" name="smtp_port" min="1" max="65535" class="form-input" placeholder="465" value="<?= e((string) ($mailConfig['port'] ?? '')) ?>">
    </div>
    <div>
      <label class="form-label" for="smtp_encryption">Encryption</label>
      <select id="smtp_encryption" name="smtp_encryption" class="form-input">
        <option value="tls" <?= ($mailConfig['encryption'] ?? '') === 'tls' ? 'selected' : '' ?>>TLS (STARTTLS)</option>
        <option value="ssl" <?= ($mailConfig['encryption'] ?? '') === 'ssl' ? 'selected' : '' ?>>SSL</option>
      </select>
    </div>
    <div>
      <label class="form-label" for="smtp_username">Username</label>
      <input type="text" id="smtp_username" name="smtp_username" class="form-input" value="<?= e($mailConfig['username'] ?? '') ?>">
    </div>
    <div>
      <label class="form-label" for="smtp_password">Password</label>
      <input type="password" id="smtp_password" name="smtp_password" class="form-input" autocomplete="new-password" placeholder="<?= $mailConfig['password_set'] ? 'Leave blank to keep the current password' : 'No password set' ?>">
    </div>
    <div>
      <label class="form-label" for="smtp_from_email">From Email</label>
      <input type="email" id="smtp_from_email" name="smtp_from_email" class="form-input" value="<?= e($mailConfig['from_email'] ?? '') ?>">
    </div>
    <div class="col-span-2">
      <label class="form-label" for="smtp_from_name">From Name</label>
      <input type="text" id="smtp_from_name" name="smtp_from_name" class="form-input" value="<?= e($mailConfig['from_name'] ?? '') ?>">
    </div>
  </div>

  <button type="submit" class="btn-gold !px-6 !py-2.5 text-sm mt-6">Save SMTP Connection</button>
</form>
