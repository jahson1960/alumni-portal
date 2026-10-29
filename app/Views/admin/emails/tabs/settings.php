<?php require dirname(__DIR__, 3) . '/partials/errors.php'; ?>

<form method="POST" action="<?= e(url('admin/emails/settings')) ?>" class="space-y-6 max-w-3xl">
  <?= csrf_field() ?>

  <div class="card p-6">
    <h3 class="section-title text-xs mb-1">Email Theme</h3>
    <p class="text-xs text-slate-400 mb-4">Branding used in the header and buttons of every outbound email (account credentials, password resets, notifications). Defaults to match the site's own header colors — only change these if you want emails to look different from the live site.</p>
    <div class="grid grid-cols-3 gap-4">
      <div>
        <label class="form-label" for="admin_email_header_bg">Header Background</label>
        <input type="color" id="admin_email_header_bg" name="admin_email_header_bg" class="w-full h-10 rounded border border-slate-300 cursor-pointer" value="<?= e($settings['admin_email_header_bg'] ?? $settings['header_bg_desktop'] ?? '#091a2e') ?>">
      </div>
      <div>
        <label class="form-label" for="admin_email_header_text">Header Text</label>
        <input type="color" id="admin_email_header_text" name="admin_email_header_text" class="w-full h-10 rounded border border-slate-300 cursor-pointer" value="<?= e($settings['admin_email_header_text'] ?? $settings['header_text_desktop'] ?? '#ffffff') ?>">
      </div>
      <div>
        <label class="form-label" for="admin_email_button_color">Button Color</label>
        <input type="color" id="admin_email_button_color" name="admin_email_button_color" class="w-full h-10 rounded border border-slate-300 cursor-pointer" value="<?= e($settings['admin_email_button_color'] ?? $settings['theme_button_color'] ?? '#d49326') ?>">
      </div>
    </div>
    <p class="text-xs text-slate-400 mt-3">The logo shown in emails is the same as the Site Logo set on the Site Settings page.</p>
  </div>

  <div class="card p-6">
    <h3 class="section-title text-xs mb-1">Email Delivery Rate</h3>
    <p class="text-xs text-slate-400 mb-4">Controls how fast queued emails (broadcasts, notifications, account credentials) actually go out. Sending too many at once can trip a mail provider's spam filters, so the background worker sends a limited batch, waits, then sends the next batch.</p>
    <div class="grid grid-cols-2 gap-4 max-w-sm">
      <div>
        <label class="form-label" for="email_batch_size">Emails per batch</label>
        <input type="number" id="email_batch_size" name="email_batch_size" min="1" max="100" step="1" class="form-input" value="<?= e($settings['email_batch_size'] ?? '20') ?>">
      </div>
      <div>
        <label class="form-label" for="email_batch_interval_minutes">Minutes between batches</label>
        <input type="number" id="email_batch_interval_minutes" name="email_batch_interval_minutes" min="0" max="120" step="1" class="form-input" value="<?= e($settings['email_batch_interval_minutes'] ?? '2') ?>">
      </div>
    </div>
    <p class="text-xs text-slate-400 mt-3">Example: 5 per batch with a 2-minute gap sends 20 emails as four batches, about 6 minutes apart start to finish.</p>

    <div class="mt-4 pt-4 border-t border-slate-100">
      <label class="form-label" for="broadcast_bcc_email">Broadcast Monitoring BCC</label>
      <p class="text-xs text-slate-400 mb-2">If set, this address is BCC'd on every broadcast email sent to applicants, so a staff member can monitor activity. Leave blank for no BCC.</p>
      <input type="email" id="broadcast_bcc_email" name="broadcast_bcc_email" class="form-input max-w-sm" placeholder="staff@rbsnapps.com" value="<?= e($settings['broadcast_bcc_email'] ?? '') ?>">
    </div>
  </div>

  <button type="submit" class="btn-gold !px-6 !py-2.5 text-sm">Save Email Settings</button>
</form>
