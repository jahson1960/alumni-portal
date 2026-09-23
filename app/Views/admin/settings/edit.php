<?php require dirname(dirname(__DIR__)) . '/partials/errors.php'; ?>

<form method="POST" action="<?= e(url('admin/settings')) ?>" enctype="multipart/form-data" class="space-y-8 max-w-3xl">
  <?= csrf_field() ?>

  <div class="card p-6">
    <h3 class="section-title text-xs mb-4">General</h3>
    <div class="space-y-4">
      <div>
        <label class="form-label" for="site_name">Site Name</label>
        <input type="text" id="site_name" name="site_name" class="form-input" value="<?= e($settings['site_name'] ?? '') ?>">
      </div>
      <div>
        <label class="form-label" for="giving_contact_email">Giving / Donations Contact Email</label>
        <input type="email" id="giving_contact_email" name="giving_contact_email" class="form-input" value="<?= e($settings['giving_contact_email'] ?? '') ?>">
      </div>
      <div>
        <label class="form-label">Favicon</label>
        <?php if (!empty($settings['site_favicon'])): ?>
          <img src="<?= e($settings['site_favicon']) ?>" alt="" class="w-8 h-8 object-contain rounded border border-slate-200 mb-2 bg-white p-1">
        <?php endif; ?>
        <input type="file" name="favicon" accept=".png,.jpg,.jpeg,.webp" class="text-sm block">
        <p class="text-xs text-slate-400 mt-1">Small square image (e.g. 32&times;32 or 64&times;64 PNG) shown in the browser tab.</p>
      </div>
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
        <div>
          <label class="form-label" for="header_height">Navigation Bar Height (px)</label>
          <input type="number" id="header_height" name="header_height" min="48" max="120" step="1" class="form-input" value="<?= e($settings['header_height'] ?? '64') ?>">
          <p class="text-xs text-slate-400 mt-1">Height of the top navigation bar. Default 64px.</p>
        </div>
        <div>
          <label class="form-label" for="dropdown_offset">Dropdown Menu Vertical Offset (px)</label>
          <input type="number" id="dropdown_offset" name="dropdown_offset" min="-20" max="40" step="1" class="form-input" value="<?= e($settings['dropdown_offset'] ?? '4') ?>">
          <p class="text-xs text-slate-400 mt-1">Gap between the nav bar and its dropdown menus. Use a negative value to pull dropdowns upward, closer to the bar.</p>
        </div>
      </div>
    </div>
  </div>

  <?php $themeMode = ($settings['theme_mode'] ?? 'unified') === 'custom' ? 'custom' : 'unified'; ?>
  <div class="card p-6">
    <h3 class="section-title text-xs mb-1">Theme Colors</h3>
    <p class="text-xs text-slate-400 mb-4">Controls the brand accent color used for the navigation menu, buttons &amp; links, and the alumni map.</p>

    <div class="space-y-2 mb-5">
      <label class="flex items-start gap-2">
        <input type="radio" name="theme_mode" value="unified" id="theme_mode_unified" <?= $themeMode === 'unified' ? 'checked' : '' ?> class="mt-1 text-gold focus:ring-gold" data-theme-mode-toggle>
        <span>
          <span class="block text-sm font-semibold text-primary-navy">One color for everything (recommended)</span>
          <span class="block text-xs text-slate-500">A single accent color drives the menu, buttons, links and the map.</span>
        </span>
      </label>
      <label class="flex items-start gap-2">
        <input type="radio" name="theme_mode" value="custom" id="theme_mode_custom" <?= $themeMode === 'custom' ? 'checked' : '' ?> class="mt-1 text-gold focus:ring-gold" data-theme-mode-toggle>
        <span>
          <span class="block text-sm font-semibold text-primary-navy">Customize each area separately</span>
          <span class="block text-xs text-slate-500">Pick a different color for the menu, buttons &amp; links, and the map.</span>
        </span>
      </label>
    </div>

    <div id="theme-unified-fields" class="<?= $themeMode === 'custom' ? 'hidden' : '' ?>">
      <label class="form-label" for="theme_accent_color">Accent Color</label>
      <div class="flex items-center gap-3">
        <input type="color" id="theme_accent_color" name="theme_accent_color" class="w-14 h-10 rounded border border-slate-300 cursor-pointer" value="<?= e($settings['theme_accent_color'] ?? '#d49326') ?>">
        <span class="text-xs text-slate-400">Used everywhere: menu badges &amp; hovers, buttons, links, active states and the alumni map.</span>
      </div>
    </div>

    <div id="theme-custom-fields" class="space-y-4 <?= $themeMode === 'custom' ? '' : 'hidden' ?>">
      <div>
        <label class="form-label" for="theme_button_color">Buttons &amp; Links Color</label>
        <div class="flex items-center gap-3">
          <input type="color" id="theme_button_color" name="theme_button_color" class="w-14 h-10 rounded border border-slate-300 cursor-pointer" value="<?= e($settings['theme_button_color'] ?? '#d49326') ?>">
          <span class="text-xs text-slate-400">Buttons, CTAs, badges, active-page underlines and hover highlights.</span>
        </div>
      </div>
      <div>
        <label class="form-label" for="menu_accent_color">Navigation Menu Accent Color</label>
        <div class="flex items-center gap-3">
          <input type="color" id="menu_accent_color" name="menu_accent_color" class="w-14 h-10 rounded border border-slate-300 cursor-pointer" value="<?= e($settings['menu_accent_color'] ?? '#d49326') ?>">
          <span class="text-xs text-slate-400">Icon badges and hover states across the mega-menus.</span>
        </div>
      </div>
      <div>
        <label class="form-label" for="theme_map_color">Alumni Map Color</label>
        <div class="flex items-center gap-3">
          <input type="color" id="theme_map_color" name="theme_map_color" class="w-14 h-10 rounded border border-slate-300 cursor-pointer" value="<?= e($settings['theme_map_color'] ?? '#d49326') ?>">
          <span class="text-xs text-slate-400">The "By Country" directory world map.</span>
        </div>
      </div>
    </div>

    <script>
      (function () {
        var unifiedFields = document.getElementById('theme-unified-fields');
        var customFields = document.getElementById('theme-custom-fields');
        document.querySelectorAll('[data-theme-mode-toggle]').forEach(function (radio) {
          radio.addEventListener('change', function () {
            var isCustom = document.getElementById('theme_mode_custom').checked;
            unifiedFields.classList.toggle('hidden', isCustom);
            customFields.classList.toggle('hidden', !isCustom);
          });
        });
      })();
    </script>
  </div>

  <div class="card p-6">
    <h3 class="section-title text-xs mb-1">Alumni Job Postings</h3>
    <p class="text-xs text-slate-400 mb-4">Controls what happens when an alumnus submits a job through "Post a Job" on the public site.</p>
    <div class="space-y-2">
      <label class="flex items-start gap-2">
        <input type="radio" name="job_posting_mode" value="approval" <?= ($settings['job_posting_mode'] ?? 'approval') === 'approval' ? 'checked' : '' ?> class="mt-1 text-gold focus:ring-gold">
        <span>
          <span class="block text-sm font-semibold text-primary-navy">Require admin approval (recommended)</span>
          <span class="block text-xs text-slate-500">Alumni-submitted jobs are held for review and only go live once an admin approves them.</span>
        </span>
      </label>
      <label class="flex items-start gap-2">
        <input type="radio" name="job_posting_mode" value="direct" <?= ($settings['job_posting_mode'] ?? '') === 'direct' ? 'checked' : '' ?> class="mt-1 text-gold focus:ring-gold">
        <span>
          <span class="block text-sm font-semibold text-primary-navy">Publish immediately</span>
          <span class="block text-xs text-slate-500">Alumni-submitted jobs go live right away without review.</span>
        </span>
      </label>
    </div>
    <div class="mt-4 pt-4 border-t border-slate-100">
      <label class="flex items-start gap-2">
        <input type="checkbox" name="require_login_to_apply" value="1" <?= ($settings['require_login_to_apply'] ?? '0') === '1' ? 'checked' : '' ?> class="mt-1 rounded border-slate-300 text-gold focus:ring-gold">
        <span>
          <span class="block text-sm font-semibold text-primary-navy">Require login to apply for jobs</span>
          <span class="block text-xs text-slate-500">Even when the Job Board is public, visitors must sign in before clicking Apply on a listing.</span>
        </span>
      </label>
    </div>
    <div class="mt-4 pt-4 border-t border-slate-100">
      <label class="form-label">Job Board Banner Image</label>
      <p class="text-xs text-slate-400 mb-2">Background photo behind the "Job Board" hero on the Careers page. Upload a file or paste an image URL — uploading a file takes priority.</p>
      <?php if (!empty($settings['jobs_banner_image'])): ?>
        <img src="<?= e($settings['jobs_banner_image']) ?>" alt="" class="w-full max-w-xs h-24 object-cover rounded border border-slate-200 mb-2">
      <?php endif; ?>
      <input type="file" name="jobs_banner_image" accept=".jpg,.jpeg,.png,.webp" class="text-sm block mb-2">
      <input type="url" name="jobs_banner_image_url" placeholder="...or paste an image URL" class="form-input">
    </div>
    <div class="mt-4 pt-4 border-t border-slate-100">
      <label class="form-label">"Explore Career Opportunities" Sidebar Image</label>
      <p class="text-xs text-slate-400 mb-2">Background photo for the "Explore Career Opportunities" card in the Job Board sidebar. Upload a file or paste an image URL — uploading a file takes priority.</p>
      <?php if (!empty($settings['jobs_explore_image'])): ?>
        <img src="<?= e($settings['jobs_explore_image']) ?>" alt="" class="w-full max-w-xs h-24 object-cover rounded border border-slate-200 mb-2">
      <?php endif; ?>
      <input type="file" name="jobs_explore_image" accept=".jpg,.jpeg,.png,.webp" class="text-sm block mb-2">
      <input type="url" name="jobs_explore_image_url" placeholder="...or paste an image URL" class="form-input">
    </div>
  </div>

  <div class="card p-6">
    <h3 class="section-title text-xs mb-1">Homepage Display Counts</h3>
    <p class="text-xs text-slate-400 mb-4">How many items to show in each homepage section.</p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="form-label" for="home_news_count">News Items</label>
        <input type="number" id="home_news_count" name="home_news_count" min="1" max="12" class="form-input" value="<?= e($settings['home_news_count'] ?? '4') ?>">
      </div>
      <div>
        <label class="form-label" for="home_jobs_count">Job Listings</label>
        <input type="number" id="home_jobs_count" name="home_jobs_count" min="1" max="12" class="form-input" value="<?= e($settings['home_jobs_count'] ?? '3') ?>">
      </div>
      <div>
        <label class="form-label" for="home_events_count">Upcoming Events</label>
        <input type="number" id="home_events_count" name="home_events_count" min="1" max="12" class="form-input" value="<?= e($settings['home_events_count'] ?? '3') ?>">
      </div>
    </div>
  </div>

  <div class="card p-6">
    <div class="flex items-center justify-between mb-1">
      <h3 class="section-title text-xs">Hero Section Behavior</h3>
      <a href="<?= e(url('admin/hero-slides')) ?>" class="text-xs font-semibold text-gold hover:underline">Manage Hero Slides &rarr;</a>
    </div>
    <p class="text-xs text-slate-400 mb-4">Controls the size and slideshow motion of the homepage hero. Slide content, per-slide buttons, and alignment are managed on the Hero Slides page.</p>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
      <div>
        <label class="form-label" for="hero_height">Height (px)</label>
        <input type="number" id="hero_height" name="hero_height" min="300" max="900" step="10" class="form-input" value="<?= e($settings['hero_height'] ?? '480') ?>">
        <p class="text-xs text-slate-400 mt-1">Between 300 and 900.</p>
      </div>
      <div>
        <label class="form-label" for="hero_interval">Time Between Slides (seconds)</label>
        <input type="number" id="hero_interval" name="hero_interval" min="3" max="20" class="form-input" value="<?= e($settings['hero_interval'] ?? '6') ?>">
        <p class="text-xs text-slate-400 mt-1">Between 3 and 20.</p>
      </div>
      <div>
        <label class="form-label" for="hero_transition">Transition Style</label>
        <select id="hero_transition" name="hero_transition" class="form-input">
          <?php
          $transitionLabels = [
              'fade' => 'Fade',
              'slide' => 'Slide (horizontal)',
              'slide-vertical' => 'Slide (vertical)',
              'zoom' => 'Zoom (Ken Burns)',
              'flip' => 'Flip',
              'wipe' => 'Wipe',
          ];
          $currentTransition = $settings['hero_transition'] ?? 'fade';
          ?>
          <?php foreach ($transitionLabels as $value => $label): ?>
            <option value="<?= e($value) ?>" <?= $currentTransition === $value ? 'selected' : '' ?>><?= e($label) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>
  </div>

  <div class="card p-6">
    <h3 class="section-title text-xs mb-1">Mentorship CTA Banner</h3>
    <p class="text-xs text-slate-400 mb-4">Shown at the bottom of the "Upcoming Events" column on the homepage.</p>
    <div class="space-y-4">
      <div>
        <label class="form-label" for="cta_title">CTA Heading</label>
        <input type="text" id="cta_title" name="cta_title" class="form-input" placeholder="e.g. Become a Mentor" value="<?= e($settings['cta_title'] ?? '') ?>">
      </div>
      <div>
        <label class="form-label" for="cta_subtitle">CTA Description</label>
        <input type="text" id="cta_subtitle" name="cta_subtitle" class="form-input" placeholder="e.g. Share your experience. Inspire the next generation." value="<?= e($settings['cta_subtitle'] ?? '') ?>">
      </div>
    </div>
  </div>

  <div class="card p-6">
    <h3 class="section-title text-xs mb-4">Footer Metrics Bar</h3>
    <div class="grid grid-cols-2 gap-4">
      <div>
        <label class="form-label" for="metric_alumni">Alumni Worldwide</label>
        <input type="text" id="metric_alumni" name="metric_alumni" class="form-input" value="<?= e($settings['metric_alumni'] ?? '') ?>">
      </div>
      <div>
        <label class="form-label" for="metric_countries">Countries Represented</label>
        <input type="text" id="metric_countries" name="metric_countries" class="form-input" value="<?= e($settings['metric_countries'] ?? '') ?>">
      </div>
      <div>
        <label class="form-label" for="metric_jobs">Active Job Opportunities</label>
        <input type="text" id="metric_jobs" name="metric_jobs" class="form-input" value="<?= e($settings['metric_jobs'] ?? '') ?>">
      </div>
      <div>
        <label class="form-label" for="metric_connections">Alumni Connections Made</label>
        <input type="text" id="metric_connections" name="metric_connections" class="form-input" value="<?= e($settings['metric_connections'] ?? '') ?>">
      </div>
      <div>
        <label class="form-label" for="metric_events">Events per Year</label>
        <input type="text" id="metric_events" name="metric_events" class="form-input" value="<?= e($settings['metric_events'] ?? '') ?>">
      </div>
    </div>
  </div>

  <div class="card p-6">
    <h3 class="section-title text-xs mb-1">Give Back — Recent Impact</h3>
    <p class="text-xs text-slate-400 mb-4">Shown in the "Recent Impact" card on the Give Back menu.</p>
    <div class="grid grid-cols-3 gap-4">
      <div>
        <label class="form-label" for="metric_scholarships">Scholarships Awarded</label>
        <input type="text" id="metric_scholarships" name="metric_scholarships" class="form-input" value="<?= e($settings['metric_scholarships'] ?? '') ?>">
      </div>
      <div>
        <label class="form-label" for="metric_mentors_engaged">Mentors Engaged</label>
        <input type="text" id="metric_mentors_engaged" name="metric_mentors_engaged" class="form-input" value="<?= e($settings['metric_mentors_engaged'] ?? '') ?>">
      </div>
      <div>
        <label class="form-label" for="metric_projects_funded">Projects Funded</label>
        <input type="text" id="metric_projects_funded" name="metric_projects_funded" class="form-input" value="<?= e($settings['metric_projects_funded'] ?? '') ?>">
      </div>
    </div>
  </div>

  <button type="submit" class="btn-gold">Save Settings</button>
</form>
