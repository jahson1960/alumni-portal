<?php
/**
 * Shared branded email shell. Expects $subject, $bodyHtml, $settings (Setting::all()) in scope —
 * never rendered directly, only via the render_email() helper in app/Core/helpers.php.
 *
 * Table-based layout with every style inlined: email clients have minimal and wildly
 * inconsistent CSS support (no CSS custom properties, often no <style> blocks at all), so this
 * cannot reuse theme_style()/Tailwind the way the live site does.
 */
$headerBg = $settings['admin_email_header_bg'] ?? $settings['header_bg_desktop'] ?? '#091a2e';
$headerText = $settings['admin_email_header_text'] ?? $settings['header_text_desktop'] ?? '#ffffff';
$siteName = $settings['site_name'] ?? 'Rome Business School Nigeria';
$logoUrl = !empty($settings['site_logo']) ? $settings['site_logo'] : absolute_url('assets/uploads/branding/rbs-logo.png');
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= e($subject) ?></title>
</head>
<body style="margin:0; padding:0; background-color:#f1f5f9; font-family: Arial, Helvetica, sans-serif;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f1f5f9; padding:24px 0;">
    <tr>
      <td align="center">
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="max-width:560px; background-color:#ffffff; border-radius:8px; overflow:hidden;">
          <tr>
            <td style="background-color:<?= e($headerBg) ?>; padding:20px 24px;">
              <table role="presentation" cellpadding="0" cellspacing="0">
                <tr>
                  <td style="vertical-align:middle;"><img src="<?= e($logoUrl) ?>" alt="<?= e($siteName) ?>" width="36" height="36" style="display:block; border-radius:4px;"></td>
                  <td style="vertical-align:middle; padding-left:10px; color:<?= e($headerText) ?>; font-size:15px; font-weight:bold; letter-spacing:0.03em; text-transform:uppercase;"><?= e($siteName) ?></td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding:28px 24px; color:#1f2937; font-size:14px; line-height:1.6;">
              <?= $bodyHtml ?>
            </td>
          </tr>
          <tr>
            <td style="padding:16px 24px; background-color:#f8fafc; color:#94a3b8; font-size:11px; text-align:center;">
              &copy; <?= date('Y') ?> <?= e($siteName) ?>. This is an automated message &mdash; please do not reply.
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
