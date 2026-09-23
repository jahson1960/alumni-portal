<?php
$completion = profile_completion($user, $education, $experience);
$circumference = 2 * M_PI * 36;
$offset = $circumference * (1 - $completion['percent'] / 100);

$tabLabels = [
    'profile_information' => ['label' => 'Profile Information', 'icon' => 'fa-regular fa-id-badge'],
    'education' => ['label' => 'Education', 'icon' => 'fa-solid fa-graduation-cap'],
    'experience' => ['label' => 'Experience', 'icon' => 'fa-solid fa-briefcase'],
    'skills' => ['label' => 'Skills & Expertise', 'icon' => 'fa-regular fa-star'],
    'interests' => ['label' => 'Interests', 'icon' => 'fa-regular fa-heart'],
    'contact' => ['label' => 'Contact & Location', 'icon' => 'fa-solid fa-location-dot'],
    'links' => ['label' => 'Links & Socials', 'icon' => 'fa-solid fa-link'],
    'privacy' => ['label' => 'Privacy Settings', 'icon' => 'fa-solid fa-shield-halved'],
];

$industryOptions = \App\Models\User::distinctFilterValues('industry');
?>
<div class="max-w-[1400px] mx-auto px-4 md:px-8 py-8">
  <div class="flex flex-wrap items-start justify-between gap-4 mb-6">
    <div>
      <h1 class="text-xl font-extrabold text-primary-navy">Edit Profile</h1>
      <p class="text-sm text-slate-500">Keep your profile updated. Your information helps alumni connect with you.</p>
    </div>
    <div class="flex items-center gap-2">
      <a href="<?= e(url('alumni/' . $user['id'])) ?>" class="btn bg-slate-100 text-slate-600 hover:bg-slate-200 !px-4 !py-2 text-xs"><i class="fa-regular fa-eye"></i> Preview Profile</a>
      <button type="submit" form="profile-form" class="btn !bg-primary-navy !text-white hover:!bg-slate-800 !px-4 !py-2 text-xs"><i class="fa-regular fa-floppy-disk"></i> Save Changes</button>
      <div class="relative group">
        <button type="button" class="btn bg-slate-100 text-slate-600 hover:bg-slate-200 !px-3 !py-2 text-xs"><i class="fa-solid fa-ellipsis-vertical"></i></button>
        <div class="absolute right-0 top-full pt-1 hidden group-hover:block z-30">
          <div class="bg-white min-w-[190px] shadow-lg rounded-md border border-slate-200 py-2">
            <a href="<?= e(url('alumni/' . $user['id'])) ?>" class="block px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 hover:text-gold">View Public Profile</a>
            <a href="#privacy" class="jump-tab block px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 hover:text-gold" data-tab="privacy">Privacy Settings</a>
            <a href="mailto:<?= e(\App\Models\Setting::get('giving_contact_email', 'support@rbsn.example.com')) ?>" class="block px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 hover:text-gold">Contact Support</a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <?php require dirname(__DIR__) . '/partials/errors.php'; ?>

  <div class="grid grid-cols-1 lg:grid-cols-[220px_1fr_300px] gap-6 items-start">

    <!-- Left: tab nav -->
    <div>
      <nav class="card p-2 space-y-1">
        <?php foreach ($tabLabels as $key => $meta): ?>
          <button type="button" class="profile-tab-link w-full flex items-center gap-2.5 px-3 py-2.5 rounded-lg text-sm font-semibold text-slate-600 hover:bg-slate-50 <?= $key === 'profile_information' ? '!bg-[var(--menu-accent-50)] !text-[var(--menu-accent-600)]' : '' ?>" data-tab="<?= e($key) ?>">
            <i class="<?= e($meta['icon']) ?> w-4 text-center"></i> <?= e($meta['label']) ?>
          </button>
        <?php endforeach; ?>
      </nav>

      <div class="mega-side-card mt-4">
        <div class="flex items-start gap-2.5">
          <div class="w-9 h-9 rounded-full bg-[var(--menu-accent-100)] text-[var(--menu-accent-600)] flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-headset"></i></div>
          <div>
            <p class="text-sm font-bold text-primary-navy mb-1">Need Help?</p>
            <p class="text-xs text-slate-500 mb-3 leading-relaxed">If you need help updating your profile, our support team is here for you.</p>
            <a href="mailto:<?= e(\App\Models\Setting::get('giving_contact_email', 'support@rbsn.example.com')) ?>" class="btn bg-white border border-slate-200 !text-slate-700 hover:bg-slate-50 !px-3 !py-2 text-xs w-full text-center block">Contact Support</a>
          </div>
        </div>
      </div>
    </div>

    <!-- Center: tab content -->
    <div>
      <form method="POST" id="profile-form" action="<?= e(url('profile/edit')) ?>" enctype="multipart/form-data">
        <?= csrf_field() ?>

        <div class="profile-tab-panel card p-6" data-tab="profile_information">
          <h3 class="text-base font-bold text-primary-navy mb-1">Profile Information</h3>
          <p class="text-xs text-slate-500 mb-5">Basic information about you</p>

          <div class="mb-5">
            <label class="form-label">Profile Photo</label>
            <div class="flex items-center gap-4">
              <div class="relative flex-shrink-0">
                <?= avatar_html($user, 'w-20 h-20') ?>
              </div>
              <label for="avatar" class="flex-1 border-2 border-dashed border-slate-200 rounded-lg py-5 text-center cursor-pointer hover:border-[var(--menu-accent-600)] transition-colors block">
                <i class="fa-solid fa-cloud-arrow-up text-[var(--menu-accent-600)] text-lg block mb-1"></i>
                <span class="text-sm font-semibold text-primary-navy">Upload New Photo</span>
                <span class="block text-xs text-slate-400 mt-0.5">JPG, PNG or WEBP. Max 2MB</span>
                <input type="file" id="avatar" name="avatar" accept=".jpg,.jpeg,.png,.webp" class="hidden">
              </label>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
              <label class="form-label" for="name">Full Name</label>
              <input type="text" id="name" name="name" class="form-input" value="<?= e($user['name']) ?>" required>
            </div>
            <div>
              <label class="form-label" for="preferred_name">Preferred Name <span class="text-slate-400 font-normal">(Optional)</span></label>
              <input type="text" id="preferred_name" name="preferred_name" class="form-input" value="<?= e($user['preferred_name'] ?? '') ?>">
            </div>
          </div>

          <div class="mb-4">
            <label class="form-label" for="headline">Current Position</label>
            <input type="text" id="headline" name="headline" class="form-input" placeholder="e.g. Founder & CEO" value="<?= e($user['headline']) ?>">
          </div>

          <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
              <label class="form-label" for="company">Company / Organization</label>
              <input type="text" id="company" name="company" class="form-input" value="<?= e($user['company']) ?>">
            </div>
            <div>
              <label class="form-label" for="industry">Industry</label>
              <input type="text" id="industry" name="industry" list="industry-options" class="form-input" placeholder="e.g. Technology & Fintech" value="<?= e($user['industry']) ?>">
              <datalist id="industry-options">
                <?php foreach ($industryOptions as $opt): ?><option value="<?= e($opt) ?>"><?php endforeach; ?>
              </datalist>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
              <label class="form-label" for="company_size">Company Size <span class="text-slate-400 font-normal">(Optional)</span></label>
              <select id="company_size" name="company_size" class="form-input">
                <option value="">Select&hellip;</option>
                <?php foreach (['1 – 10 employees', '11 – 50 employees', '51 – 200 employees', '201 – 500 employees', '501 – 1,000 employees', '1,000+ employees'] as $opt): ?>
                  <option value="<?= e($opt) ?>" <?= ($user['company_size'] ?? '') === $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
            <div>
              <label class="form-label" for="years_in_role">Years in Current Role</label>
              <select id="years_in_role" name="years_in_role" class="form-input">
                <option value="">Select&hellip;</option>
                <?php foreach (['Less than 1 year', '1 – 2 years', '3 – 5 years', '6 – 10 years', '10+ years'] as $opt): ?>
                  <option value="<?= e($opt) ?>" <?= ($user['years_in_role'] ?? '') === $opt ? 'selected' : '' ?>><?= e($opt) ?></option>
                <?php endforeach; ?>
              </select>
            </div>
          </div>

          <div>
            <label class="form-label" for="bio">Bio</label>
            <div data-rich-editor="bio"></div>
            <textarea id="bio" name="bio" class="hidden" maxlength="2000"><?= e($user['bio']) ?></textarea>
            <p class="text-xs text-slate-400 mt-1 text-right"><span id="bio-char-count">0</span> / 500</p>
          </div>
        </div>

        <div class="profile-tab-panel hidden card p-6" data-tab="education">
          <h3 class="text-base font-bold text-primary-navy mb-1">Education</h3>
          <p class="text-xs text-slate-500 mb-5">Your programmes and academic history</p>

          <h4 class="text-xs font-extrabold text-primary-navy uppercase tracking-wide mb-3">Your RBSN Programme</h4>
          <div class="grid grid-cols-2 gap-4 mb-2">
            <div>
              <label class="form-label" for="program">Programme</label>
              <input type="text" id="program" name="program" class="form-input" placeholder="e.g. MBA, DBA, Executive MBA" value="<?= e($user['program']) ?>">
            </div>
            <div>
              <label class="form-label" for="graduation_year">Graduation Year</label>
              <input type="number" id="graduation_year" name="graduation_year" class="form-input" min="1990" max="2100" value="<?= e($user['graduation_year']) ?>">
            </div>
          </div>
        </div>

        <div class="profile-tab-panel hidden card p-6" data-tab="skills">
          <h3 class="text-base font-bold text-primary-navy mb-1">Skills &amp; Expertise</h3>
          <p class="text-xs text-slate-500 mb-5">Separate multiple entries with commas.</p>
          <div class="mb-4">
            <label class="form-label" for="skills">Skills</label>
            <input type="text" id="skills" name="skills" class="form-input" placeholder="e.g. Product Strategy, Leadership" value="<?= e($user['skills']) ?>">
          </div>
          <div>
            <label class="form-label" for="expertise_areas">Areas of Expertise</label>
            <input type="text" id="expertise_areas" name="expertise_areas" class="form-input" placeholder="e.g. Entrepreneurship, Corporate Strategy" value="<?= e($user['expertise_areas']) ?>">
          </div>
        </div>

        <div class="profile-tab-panel hidden card p-6" data-tab="interests">
          <h3 class="text-base font-bold text-primary-navy mb-1">Interests</h3>
          <p class="text-xs text-slate-500 mb-5">Separate multiple entries with commas.</p>
          <div class="mb-4">
            <label class="form-label" for="business_interests">Business Interests</label>
            <input type="text" id="business_interests" name="business_interests" class="form-input" placeholder="e.g. Fintech, Manufacturing" value="<?= e($user['business_interests']) ?>">
          </div>
          <div>
            <label class="form-label" for="personal_interests">Personal Interests</label>
            <input type="text" id="personal_interests" name="personal_interests" class="form-input" placeholder="e.g. Travel, Reading, Golf" value="<?= e($user['personal_interests'] ?? '') ?>">
          </div>
        </div>

        <div class="profile-tab-panel hidden card p-6" data-tab="contact">
          <h3 class="text-base font-bold text-primary-navy mb-1">Contact &amp; Location</h3>
          <p class="text-xs text-slate-500 mb-5">How and where alumni can reach you</p>
          <div class="mb-4">
            <label class="form-label" for="phone">Phone</label>
            <input type="text" id="phone" name="phone" class="form-input" value="<?= e($user['phone']) ?>">
          </div>
          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="form-label" for="city">City</label>
              <input type="text" id="city" name="city" class="form-input" value="<?= e($user['city']) ?>">
            </div>
            <div>
              <label class="form-label" for="country">Country</label>
              <input type="text" id="country" name="country" class="form-input" value="<?= e($user['country']) ?>">
            </div>
          </div>
        </div>

        <div class="profile-tab-panel hidden card p-6" data-tab="links">
          <h3 class="text-base font-bold text-primary-navy mb-1">Links &amp; Socials</h3>
          <p class="text-xs text-slate-500 mb-5">Where alumni can find you online</p>
          <div class="mb-4">
            <label class="form-label" for="linkedin_url">LinkedIn URL</label>
            <input type="url" id="linkedin_url" name="linkedin_url" class="form-input" value="<?= e($user['linkedin_url']) ?>">
          </div>
          <div class="mb-4">
            <label class="form-label" for="personal_website">Personal / Company Website</label>
            <input type="url" id="personal_website" name="personal_website" class="form-input" value="<?= e($user['personal_website']) ?>">
          </div>
          <div class="mb-4">
            <label class="form-label" for="twitter_url">Twitter / X URL</label>
            <input type="url" id="twitter_url" name="twitter_url" class="form-input" value="<?= e($user['twitter_url'] ?? '') ?>">
          </div>
          <div id="resume">
            <label class="form-label">Resume / CV</label>
            <?php if (!empty($user['resume_file'])): ?>
              <p class="text-xs text-slate-500 mb-2"><i class="fa-regular fa-file-lines"></i> Current file: <a href="<?= e(upload_url($user['resume_file'])) ?>" target="_blank" rel="noopener noreferrer" class="text-gold hover:underline">View</a></p>
            <?php endif; ?>
            <label class="form-label !text-[0.7rem] !text-slate-400" for="resume_file">Upload a file</label>
            <input type="file" id="resume_file" name="resume_file" accept=".pdf,.doc,.docx" class="form-input">
            <p class="text-xs text-slate-400 mt-1 mb-4">PDF, DOC, or DOCX, up to 5MB. Uploading replaces your current file.</p>
            <label class="form-label" for="resume_url">Resume Link (optional)</label>
            <input type="url" id="resume_url" name="resume_url" placeholder="https://drive.google.com/..." class="form-input" value="<?= e($user['resume_url'] ?? '') ?>">
            <p class="text-xs text-slate-400 mt-1">You can provide a file, a link, or both. Employers browsing the Job Board can find it on your profile.</p>
          </div>
        </div>

        <div class="profile-tab-panel hidden card p-6" data-tab="privacy">
          <h3 class="text-base font-bold text-primary-navy mb-1">Privacy Settings</h3>
          <p class="text-xs text-slate-500 mb-5">Control who can see your information</p>

          <div class="mb-4">
            <label class="form-label" for="profile_visibility">Who can see my profile</label>
            <select id="profile_visibility" name="profile_visibility" class="form-input max-w-xs">
              <option value="public" <?= $user['profile_visibility'] === 'public' ? 'selected' : '' ?>>Everyone (Public)</option>
              <option value="alumni" <?= $user['profile_visibility'] === 'alumni' ? 'selected' : '' ?>>Logged-in Alumni Only</option>
              <option value="private" <?= $user['profile_visibility'] === 'private' ? 'selected' : '' ?>>Only Me (Hidden from Directory)</option>
            </select>
          </div>

          <div class="space-y-2 mb-6">
            <label class="flex items-center gap-2 text-sm text-slate-700">
              <input type="checkbox" name="show_email" value="1" <?= !empty($user['show_email']) ? 'checked' : '' ?> class="rounded border-slate-300 text-gold focus:ring-gold">
              Show my email address on my public profile
            </label>
            <label class="flex items-center gap-2 text-sm text-slate-700">
              <input type="checkbox" name="show_phone" value="1" <?= !empty($user['show_phone']) ? 'checked' : '' ?> class="rounded border-slate-300 text-gold focus:ring-gold">
              Show my phone number on my public profile
            </label>
          </div>

          <h4 class="text-xs font-extrabold text-primary-navy uppercase tracking-wide mb-3 border-t border-slate-100 pt-4">Mentorship</h4>
          <label class="flex items-center gap-2 text-sm text-slate-700 mb-3">
            <input type="checkbox" name="is_mentor" value="1" <?= !empty($user['is_mentor']) ? 'checked' : '' ?> class="rounded border-slate-300 text-gold focus:ring-gold">
            Available as a Mentor
          </label>
          <div>
            <label class="form-label" for="mentorship_areas">Mentorship Areas</label>
            <input type="text" id="mentorship_areas" name="mentorship_areas" class="form-input" placeholder="e.g. Entrepreneurship, Corporate Strategy" value="<?= e($user['mentorship_areas']) ?>">
          </div>
        </div>
      </form>

      <!-- Experience tab: standalone forms, outside #profile-form -->
      <div class="profile-tab-panel hidden card p-6" data-tab="experience">
        <div class="flex items-center justify-between mb-1">
          <h3 class="text-base font-bold text-primary-navy">Experience</h3>
          <label for="add-experience-toggle" class="btn-gold !px-3 !py-1.5 text-xs cursor-pointer">+ Add Experience</label>
        </div>
        <p class="text-xs text-slate-500 mb-5">Your work history beyond your current role</p>

        <input type="checkbox" id="add-experience-toggle" class="hidden peer">
        <div class="hidden peer-checked:block mb-5 border border-slate-200 rounded-lg p-4">
          <h4 class="text-xs font-extrabold text-primary-navy uppercase tracking-wide mb-3">Add Experience</h4>
          <form method="POST" action="<?= e(url('profile/experience')) ?>" class="space-y-3">
            <?= csrf_field() ?>
            <div class="grid grid-cols-2 gap-3">
              <input type="text" name="title" class="form-input" placeholder="Job Title" required>
              <input type="text" name="company" class="form-input" placeholder="Company" required>
            </div>
            <input type="text" name="location" class="form-input" placeholder="Location (optional)">
            <div class="grid grid-cols-2 gap-3">
              <div>
                <label class="form-label">Start Date</label>
                <input type="date" name="start_date" class="form-input">
              </div>
              <div>
                <label class="form-label">End Date</label>
                <input type="date" name="end_date" class="form-input">
              </div>
            </div>
            <label class="flex items-center gap-2 text-xs text-slate-600">
              <input type="checkbox" name="is_current" value="1" class="rounded border-slate-300 text-gold focus:ring-gold"> I currently work here
            </label>
            <textarea name="description" rows="2" class="form-textarea text-sm" placeholder="Description (optional)"></textarea>
            <button type="submit" class="btn-gold !px-4 !py-2 text-xs">Save Experience</button>
          </form>
        </div>

        <div class="space-y-3">
          <?php foreach ($experience as $exp): ?>
            <?php $editId = 'edit-exp-' . $exp['id']; ?>
            <input type="checkbox" id="<?= e($editId) ?>" class="hidden peer">
            <div class="peer-checked:hidden card p-4 flex items-start justify-between gap-3">
              <div class="min-w-0">
                <p class="text-sm font-bold text-primary-navy"><?= e($exp['title']) ?><?= $exp['is_current'] ? ' <span class="badge-green">Current</span>' : '' ?></p>
                <p class="text-xs text-slate-500"><?= e($exp['company']) ?><?= $exp['location'] ? ' &bull; ' . e($exp['location']) : '' ?></p>
                <p class="text-[0.68rem] text-slate-400 mt-0.5">
                  <?= $exp['start_date'] ? e(format_date($exp['start_date'], 'M Y')) : '' ?> &ndash; <?= $exp['is_current'] ? 'Present' : ($exp['end_date'] ? e(format_date($exp['end_date'], 'M Y')) : '') ?>
                </p>
                <?php if ($exp['description']): ?><p class="text-xs text-slate-600 mt-2"><?= e($exp['description']) ?></p><?php endif; ?>
              </div>
              <div class="flex items-center gap-3 flex-shrink-0">
                <label for="<?= e($editId) ?>" class="text-xs text-sky-600 hover:underline cursor-pointer">Edit</label>
                <form method="POST" action="<?= e(url('profile/experience/' . $exp['id'] . '/delete')) ?>" data-confirm="Remove this experience entry?">
                  <?= csrf_field() ?>
                  <button type="submit" class="text-xs text-red-500 hover:underline">Delete</button>
                </form>
              </div>
            </div>
            <div class="hidden peer-checked:block border border-slate-200 rounded-lg p-4">
              <form method="POST" action="<?= e(url('profile/experience/' . $exp['id'])) ?>" class="space-y-3">
                <?= csrf_field() ?>
                <div class="grid grid-cols-2 gap-3">
                  <input type="text" name="title" class="form-input" value="<?= e($exp['title']) ?>" required>
                  <input type="text" name="company" class="form-input" value="<?= e($exp['company']) ?>" required>
                </div>
                <input type="text" name="location" class="form-input" value="<?= e($exp['location'] ?? '') ?>" placeholder="Location (optional)">
                <div class="grid grid-cols-2 gap-3">
                  <div>
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-input" value="<?= e($exp['start_date'] ?? '') ?>">
                  </div>
                  <div>
                    <label class="form-label">End Date</label>
                    <input type="date" name="end_date" class="form-input" value="<?= e($exp['end_date'] ?? '') ?>">
                  </div>
                </div>
                <label class="flex items-center gap-2 text-xs text-slate-600">
                  <input type="checkbox" name="is_current" value="1" <?= $exp['is_current'] ? 'checked' : '' ?> class="rounded border-slate-300 text-gold focus:ring-gold"> I currently work here
                </label>
                <textarea name="description" rows="2" class="form-textarea text-sm"><?= e($exp['description'] ?? '') ?></textarea>
                <div class="flex items-center gap-3">
                  <button type="submit" class="btn-gold !px-4 !py-2 text-xs">Save Changes</button>
                  <label for="<?= e($editId) ?>" class="text-xs text-slate-500 hover:underline cursor-pointer">Cancel</label>
                </div>
              </form>
            </div>
          <?php endforeach; ?>
          <?php if (empty($experience)): ?>
            <p class="text-sm text-slate-400">No additional experience added yet.</p>
          <?php endif; ?>
        </div>
      </div>

      <!-- Education additional entries: standalone forms, outside #profile-form -->
      <div class="profile-tab-panel hidden" data-tab="education_extra">
        <div class="card p-6 mt-4">
          <div class="flex items-center justify-between mb-4">
            <h4 class="text-xs font-extrabold text-primary-navy uppercase tracking-wide">Additional Education</h4>
            <label for="add-education-toggle" class="btn-gold !px-3 !py-1.5 text-xs cursor-pointer">+ Add Education</label>
          </div>

          <input type="checkbox" id="add-education-toggle" class="hidden peer">
          <div class="hidden peer-checked:block mb-5 border border-slate-200 rounded-lg p-4">
            <h4 class="text-xs font-extrabold text-primary-navy uppercase tracking-wide mb-3">Add Education</h4>
            <form method="POST" action="<?= e(url('profile/education')) ?>" class="space-y-3">
              <?= csrf_field() ?>
              <input type="text" name="school" class="form-input" placeholder="School / Institution" required>
              <div class="grid grid-cols-2 gap-3">
                <input type="text" name="degree" class="form-input" placeholder="Degree (e.g. BSc)">
                <input type="text" name="field_of_study" class="form-input" placeholder="Field of Study">
              </div>
              <div class="grid grid-cols-2 gap-3">
                <input type="number" name="start_year" class="form-input" placeholder="Start Year" min="1950" max="2100">
                <input type="number" name="end_year" class="form-input" placeholder="End Year" min="1950" max="2100">
              </div>
              <button type="submit" class="btn-gold !px-4 !py-2 text-xs">Save Education</button>
            </form>
          </div>

          <div class="space-y-3">
            <?php foreach ($education as $edu): ?>
              <?php $editId = 'edit-edu-' . $edu['id']; ?>
              <input type="checkbox" id="<?= e($editId) ?>" class="hidden peer">
              <div class="peer-checked:hidden card p-4 flex items-start justify-between gap-3">
                <div class="min-w-0">
                  <p class="text-sm font-bold text-primary-navy"><?= e($edu['school']) ?></p>
                  <p class="text-xs text-slate-500"><?= e(trim(($edu['degree'] ?? '') . (($edu['degree'] && $edu['field_of_study']) ? ', ' : '') . ($edu['field_of_study'] ?? ''))) ?></p>
                  <p class="text-[0.68rem] text-slate-400 mt-0.5"><?= e($edu['start_year'] ?? '') ?><?= $edu['start_year'] || $edu['end_year'] ? ' – ' : '' ?><?= e($edu['end_year'] ?? '') ?></p>
                </div>
                <div class="flex items-center gap-3 flex-shrink-0">
                  <label for="<?= e($editId) ?>" class="text-xs text-sky-600 hover:underline cursor-pointer">Edit</label>
                  <form method="POST" action="<?= e(url('profile/education/' . $edu['id'] . '/delete')) ?>" data-confirm="Remove this education entry?">
                    <?= csrf_field() ?>
                    <button type="submit" class="text-xs text-red-500 hover:underline">Delete</button>
                  </form>
                </div>
              </div>
              <div class="hidden peer-checked:block border border-slate-200 rounded-lg p-4">
                <form method="POST" action="<?= e(url('profile/education/' . $edu['id'])) ?>" class="space-y-3">
                  <?= csrf_field() ?>
                  <input type="text" name="school" class="form-input" value="<?= e($edu['school']) ?>" required>
                  <div class="grid grid-cols-2 gap-3">
                    <input type="text" name="degree" class="form-input" value="<?= e($edu['degree'] ?? '') ?>" placeholder="Degree">
                    <input type="text" name="field_of_study" class="form-input" value="<?= e($edu['field_of_study'] ?? '') ?>" placeholder="Field of Study">
                  </div>
                  <div class="grid grid-cols-2 gap-3">
                    <input type="number" name="start_year" class="form-input" value="<?= e($edu['start_year'] ?? '') ?>" placeholder="Start Year" min="1950" max="2100">
                    <input type="number" name="end_year" class="form-input" value="<?= e($edu['end_year'] ?? '') ?>" placeholder="End Year" min="1950" max="2100">
                  </div>
                  <div class="flex items-center gap-3">
                    <button type="submit" class="btn-gold !px-4 !py-2 text-xs">Save Changes</button>
                    <label for="<?= e($editId) ?>" class="text-xs text-slate-500 hover:underline cursor-pointer">Cancel</label>
                  </div>
                </form>
              </div>
            <?php endforeach; ?>
            <?php if (empty($education)): ?>
              <p class="text-sm text-slate-400">No additional education added yet.</p>
            <?php endif; ?>
          </div>
        </div>
      </div>
    </div>

    <!-- Right: widgets -->
    <div class="space-y-4">
      <div class="card p-5">
        <h4 class="text-sm font-bold text-primary-navy mb-4">Profile Completion</h4>
        <div class="flex items-center gap-4 mb-4">
          <svg width="80" height="80" viewBox="0 0 80 80" class="flex-shrink-0 -rotate-90">
            <circle cx="40" cy="40" r="36" fill="none" stroke="#e2e8f0" stroke-width="7"></circle>
            <circle cx="40" cy="40" r="36" fill="none" stroke="<?= $completion['percent'] >= 80 ? '#22c55e' : 'var(--menu-accent-600)' ?>" stroke-width="7" stroke-linecap="round"
              stroke-dasharray="<?= round($circumference, 2) ?>" stroke-dashoffset="<?= round($offset, 2) ?>"></circle>
            <text x="40" y="40" transform="rotate(90 40 40)" text-anchor="middle" dominant-baseline="central" class="fill-primary-navy" style="font-size:16px;font-weight:800;"><?= $completion['percent'] ?>%</text>
          </svg>
          <p class="text-xs text-slate-500 leading-relaxed"><?= $completion['percent'] >= 100 ? 'Your profile is complete!' : 'Almost there! Complete your profile to get better visibility in the alumni network.' ?></p>
        </div>
        <div class="w-full bg-slate-100 rounded-full h-1.5 mb-4">
          <div class="bg-emerald-500 h-1.5 rounded-full" style="width: <?= $completion['percent'] ?>%"></div>
        </div>
        <ul class="space-y-2">
          <?php foreach ($tabLabels as $key => $meta): ?>
            <li>
              <a href="#<?= e($key) ?>" class="jump-tab flex items-center gap-2 text-xs text-slate-600 hover:text-primary-navy" data-tab="<?= e($key) ?>">
                <?php if (!empty($completion['sections'][$key])): ?>
                  <i class="fa-solid fa-circle-check text-emerald-500"></i>
                <?php else: ?>
                  <i class="fa-solid fa-triangle-exclamation text-amber-500"></i>
                <?php endif; ?>
                <?= e($meta['label']) ?>
              </a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="mega-side-card">
        <div class="flex items-start gap-2.5">
          <div class="w-9 h-9 rounded-full bg-[var(--menu-accent-100)] text-[var(--menu-accent-600)] flex items-center justify-center flex-shrink-0"><i class="fa-regular fa-eye"></i></div>
          <div>
            <p class="text-sm font-bold text-primary-navy mb-1">Profile Visibility</p>
            <p class="text-xs text-slate-500 mb-2 leading-relaxed">Visible to: <strong class="text-primary-navy">
              <?= $user['profile_visibility'] === 'public' ? 'Everyone' : ($user['profile_visibility'] === 'alumni' ? 'Alumni, Students, Faculty and Staff' : 'Only you') ?>
            </strong></p>
            <a href="#privacy" class="jump-tab text-xs font-semibold text-[var(--menu-accent-600)] hover:underline" data-tab="privacy">Manage Privacy Settings &rarr;</a>
          </div>
        </div>
      </div>

      <div class="card p-5 bg-amber-50 border-amber-100">
        <div class="flex items-start gap-2.5">
          <div class="w-9 h-9 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center flex-shrink-0"><i class="fa-regular fa-lightbulb"></i></div>
          <div>
            <p class="text-sm font-bold text-primary-navy mb-1">Tips</p>
            <p class="text-xs text-slate-600 leading-relaxed">A complete profile helps others trust and connect with you. Add your skills, experience and interests for better opportunities.</p>
          </div>
        </div>
      </div>
    </div>

  </div>
</div>

<script>
  (function () {
    var panels = document.querySelectorAll('.profile-tab-panel');
    var navLinks = document.querySelectorAll('.profile-tab-link');

    function activate(tab) {
      panels.forEach(function (p) {
        var show = p.dataset.tab === tab || (tab === 'education' && p.dataset.tab === 'education_extra');
        p.classList.toggle('hidden', !show);
      });
      navLinks.forEach(function (b) {
        b.classList.toggle('!bg-[var(--menu-accent-50)]', b.dataset.tab === tab);
        b.classList.toggle('!text-[var(--menu-accent-600)]', b.dataset.tab === tab);
      });
    }

    navLinks.forEach(function (btn) {
      btn.addEventListener('click', function () {
        activate(btn.dataset.tab);
        history.replaceState(null, '', '#' + btn.dataset.tab);
      });
    });

    document.querySelectorAll('.jump-tab').forEach(function (link) {
      link.addEventListener('click', function (e) {
        e.preventDefault();
        activate(link.dataset.tab);
        history.replaceState(null, '', '#' + link.dataset.tab);
        window.scrollTo({ top: 0, behavior: 'smooth' });
      });
    });

    var initialTab = (location.hash || '').replace('#', '');
    if (initialTab) {
      if (document.querySelector('.profile-tab-panel[data-tab="' + initialTab + '"], .profile-tab-link[data-tab="' + initialTab + '"]')) {
        activate(initialTab);
      } else {
        var targetEl = document.getElementById(initialTab);
        var targetPanel = targetEl ? targetEl.closest('.profile-tab-panel') : null;
        if (targetPanel) {
          activate(targetPanel.dataset.tab);
          setTimeout(function () { targetEl.scrollIntoView({ behavior: 'smooth', block: 'start' }); }, 50);
        }
      }
    }
  })();

  window.addEventListener('load', function () {
    var quill = window.richEditors && window.richEditors['bio'];
    var counter = document.getElementById('bio-char-count');
    if (!quill || !counter) return;
    function update() {
      counter.textContent = quill.getText().trim().length;
    }
    quill.on('text-change', update);
    update();
  });
</script>
