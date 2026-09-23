<?php
use App\Core\Auth;
use App\Models\Article;
use App\Models\Benefit;
use App\Models\Campaign;
use App\Models\Company;
use App\Models\Connection;
use App\Models\Event;
use App\Models\Job;
use App\Models\Message;
use App\Models\Notification;
use App\Models\Setting;

$pageTitle = $title ?? 'Rome Business School Nigeria - Alumni Network';
$activeNav = $activeNav ?? '';
$authUser = Auth::user();
$footerSettings = Setting::all();
$pendingConnectionCount = $authUser ? Connection::countIncoming((int) $authUser['id']) : 0;
$unreadMessageCount = $authUser ? Message::unreadCount((int) $authUser['id']) : 0;
$unreadNotificationCount = $authUser ? Notification::unreadCount((int) $authUser['id']) : 0;
$networkPendingTotal = $pendingConnectionCount + $unreadMessageCount;

$networkFamily = ['directory', 'connections', 'feed', 'messages', 'mentorship', 'businesses', 'cohorts', 'map', 'spotlight', 'news', 'resources'];
$isNetworkActive = in_array($activeNav, $networkFamily, true);
$isCareersActive = $activeNav === 'jobs';

$careerFeaturedJob = Job::featuredOpen();
$companiesHiringCount = Company::countHiring();

$upcomingEventsMenu = Event::upcoming(3);

$featuredArticle = Article::featured();

$featuredBenefit = Benefit::featured();

$featuredCampaign = Campaign::featured();
$popularPartners = [
    ['name' => 'Coursera', 'offer' => '15% Off'],
    ['name' => 'Amazon Web Services', 'offer' => 'Up to 20% Off'],
    ['name' => 'Hertz', 'offer' => '15% Off'],
    ['name' => 'Microsoft 365', 'offer' => '20% Off'],
];

$cohortBadge = null;
if ($authUser && $authUser['program']) {
    $cohortBadge = $authUser['program'] . ($authUser['graduation_year'] ? " '" . substr((string) $authUser['graduation_year'], -2) : '');
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($pageTitle) ?></title>
  <?php if (!empty($footerSettings['site_favicon'])): ?>
    <link rel="icon" href="<?= e($footerSettings['site_favicon']) ?>">
  <?php endif; ?>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="<?= e(versioned_asset('css/app.css')) ?>">
  <style><?= theme_style(
    $footerSettings,
    (int) ($footerSettings['header_height'] ?? 64),
    (int) ($footerSettings['dropdown_offset'] ?? 4)
  ) ?></style>
  <?php if (!empty($needsRichEditor)): ?>
    <meta name="csrf-token" content="<?= e(\App\Core\Csrf::token()) ?>">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.snow.css">
    <script src="https://cdn.jsdelivr.net/npm/quill@1.3.7/dist/quill.min.js"></script>
    <script>window.EDITOR_UPLOAD_URL = <?= json_encode(url('editor-upload')) ?>;</script>
  <?php endif; ?>
</head>
<body class="pb-16 lg:pb-0 flex flex-col min-h-screen">

  <header class="bg-primary-navy sticky top-0 z-50">
    <div class="max-w-[1400px] mx-auto px-4 md:px-8">
      <div class="flex items-center justify-between h-[var(--header-height)] gap-4">
        <a href="<?= e(url('/')) ?>" class="flex items-center gap-3 flex-shrink-0">
          <img src="<?= e(upload_url('branding/rbs-logo.png')) ?>" alt="Rome Business School Logo" class="w-9 h-9 object-contain flex-shrink-0">
          <div class="hidden sm:block leading-tight">
            <div class="text-white font-extrabold text-sm tracking-wide">ROME BUSINESS SCHOOL</div>
            <div class="text-gold text-[0.62rem] font-bold tracking-[0.15em]">ALUMNI PORTAL</div>
          </div>
        </a>

        <nav class="hidden lg:flex items-stretch gap-1 flex-1 justify-center">
          <a href="<?= e(url('/')) ?>" class="top-pill h-full <?= $activeNav === 'home' ? 'active' : '' ?>">
            <i class="bi bi-house"></i><span>Home</span>
          </a>

          <?php if (\App\Models\PageVisibility::shouldShowInNav('directory', $authUser)): ?>
          <div class="relative group h-full" data-mega-group>
            <button class="top-pill h-full <?= $isNetworkActive ? 'active' : '' ?>">
              <i class="bi bi-people"></i><span>Alumni Network</span>
            </button>

            <div class="fixed left-0 right-0 top-[var(--header-height)] hidden z-[60]" data-mega-panel>
              <div data-mega-caret class="absolute -top-2 -translate-x-1/2 translate-y-[var(--dropdown-offset)] z-[61] w-0 h-0 border-l-[8px] border-l-transparent border-r-[8px] border-r-transparent border-b-[8px] border-b-white" style="left:50%"></div>
              <div data-mega-box class="mx-auto bg-white shadow-2xl rounded-xl border border-slate-100 overflow-hidden w-[min(94vw,1100px)] max-h-[calc(100vh-var(--header-height)-1rem)] flex flex-col translate-y-[var(--dropdown-offset)]">
                <div class="p-6 grid grid-cols-1 md:grid-cols-[220px_repeat(4,1fr)] gap-6 overflow-y-auto min-h-0 mega-scroll">

                  <div class="pr-6 md:border-r border-slate-100">
                    <div class="w-11 h-11 rounded-lg bg-[var(--menu-accent-50)] text-[var(--menu-accent-600)] flex items-center justify-center text-lg mb-3"><i class="fa-solid fa-user-group"></i></div>
                    <h3 class="text-sm font-extrabold text-primary-navy mb-1">Alumni Network</h3>
                    <p class="text-xs text-slate-500 mb-3 leading-relaxed">Connect, collaborate and engage with fellow alumni around the world.</p>
                    <?php if ($authUser): ?>
                      <a href="<?= e(url('profile/edit')) ?>" class="text-xs font-semibold text-gold hover:underline">View my profile &rarr;</a>
                    <?php else: ?>
                      <a href="<?= e(url('register')) ?>" class="text-xs font-semibold text-gold hover:underline">Join the network &rarr;</a>
                    <?php endif; ?>
                  </div>

                  <div>
                    <?= mega_col_heading('Directory') ?>
                    <div class="space-y-1">
                      <?= mega_item(url('directory'), 'fa-solid fa-magnifying-glass', 'Search Alumni', 'Find and connect with fellow alumni') ?>
                      <?= mega_item(url('directory?view=saved'), 'fa-solid fa-bookmark', 'Saved Alumni', 'Profiles you have bookmarked') ?>
                      <?= mega_item(url('directory?view=country'), 'fa-solid fa-earth-africa', 'Alumni Map', 'Explore where alumni are located worldwide') ?>
                      <?= mega_item(url('directory?view=cohort'), 'fa-solid fa-graduation-cap', 'Find by Cohort', 'Browse alumni by program and class year') ?>
                      <?= mega_item(url('businesses'), 'fa-solid fa-store', 'Alumni Businesses', 'Discover businesses owned by alumni') ?>
                    </div>
                  </div>

                  <div>
                    <?= mega_col_heading('Connections') ?>
                    <div class="space-y-1">
                      <?= mega_item(url('connections'), 'fa-solid fa-user-group', 'My Connections', 'People you are connected with') ?>
                      <?= mega_item(url('connections#requests'), 'fa-solid fa-user-plus', 'Connection Requests', 'Manage incoming and sent requests') ?>
                      <?= mega_item(url('connections#suggested'), 'fa-solid fa-star', 'Suggested Connections', 'Alumni you may want to connect with') ?>
                      <?= mega_item(url('connections#following'), 'fa-solid fa-eye', 'Following', 'Alumni you are following') ?>
                    </div>
                  </div>

                  <div>
                    <?= mega_col_heading('Engage') ?>
                    <div class="space-y-1">
                      <?= mega_item(url('feed'), 'fa-solid fa-rss', 'Community Feed', 'Share updates and see what alumni are posting') ?>
                      <?= mega_item(url('messages'), 'fa-solid fa-envelope', 'Messages', 'Chat privately with your connections') ?>
                      <?= mega_item(url('mentorship'), 'fa-solid fa-chalkboard-user', 'Mentorship', 'Find a mentor or become one') ?>
                      <?= mega_item(url('notifications'), 'fa-solid fa-bell', 'Notifications', 'Stay on top of your activity') ?>
                    </div>
                  </div>

                  <div>
                    <?= mega_col_heading('More') ?>
                    <div class="space-y-1">
                      <?= mega_item(url('news'), 'fa-solid fa-newspaper', 'News & Blog', 'Alumni stories and school updates') ?>
                      <?= mega_item(url('spotlight'), 'fa-solid fa-star', 'Alumni Spotlight', 'Celebrating standout alumni achievements') ?>
                      <?php if ($authUser && $authUser['role'] === 'admin'): ?>
                        <?= mega_item(url('admin/dashboard'), 'fa-solid fa-gauge', 'Admin Panel', 'Manage the portal') ?>
                      <?php endif; ?>
                    </div>
                  </div>

                </div>

                <div class="mega-panel-footer flex-shrink-0">
                  <div class="flex items-center gap-3 flex-1 min-w-[220px]">
                    <div class="w-11 h-11 rounded-full bg-[var(--menu-accent-100)] text-[var(--menu-accent-600)] flex items-center justify-center text-lg flex-shrink-0"><i class="fa-solid fa-globe"></i></div>
                    <div>
                      <p class="text-sm font-extrabold text-primary-navy">Grow Your Network. Build Your Future.</p>
                      <p class="text-xs text-slate-500">Connect with thousands of alumni making an impact worldwide.</p>
                    </div>
                  </div>
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-white flex items-center justify-center text-[var(--menu-accent-600)] flex-shrink-0"><i class="fa-solid fa-user-group"></i></div>
                    <div>
                      <p class="text-sm font-extrabold text-primary-navy"><?= e(Setting::get('metric_alumni', '0')) ?></p>
                      <p class="text-[0.65rem] text-slate-500">Active Alumni</p>
                    </div>
                  </div>
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-white flex items-center justify-center text-[var(--menu-accent-600)] flex-shrink-0"><i class="fa-solid fa-earth-africa"></i></div>
                    <div>
                      <p class="text-sm font-extrabold text-primary-navy"><?= e(Setting::get('metric_countries', '0')) ?></p>
                      <p class="text-[0.65rem] text-slate-500">Countries</p>
                    </div>
                  </div>
                  <a href="<?= e(url('directory')) ?>" class="btn !bg-[var(--menu-accent-600)] !text-white hover:!bg-[var(--menu-accent-700)] !px-4 !py-2 text-xs whitespace-nowrap">View Directory &rarr;</a>
                </div>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <?php if (\App\Models\PageVisibility::shouldShowInNav('jobs', $authUser)): ?>
          <div class="relative group h-full" data-mega-group>
            <button class="top-pill h-full <?= $isCareersActive ? 'active' : '' ?>">
              <i class="bi bi-briefcase"></i><span>Careers</span>
            </button>

            <div class="fixed left-0 right-0 top-[var(--header-height)] hidden z-[60]" data-mega-panel>
              <div data-mega-caret class="absolute -top-2 -translate-x-1/2 translate-y-[var(--dropdown-offset)] z-[61] w-0 h-0 border-l-[8px] border-l-transparent border-r-[8px] border-r-transparent border-b-[8px] border-b-white" style="left:50%"></div>
              <div data-mega-box class="mx-auto bg-white shadow-2xl rounded-xl border border-slate-100 overflow-hidden w-[min(94vw,1080px)] max-h-[calc(100vh-var(--header-height)-1rem)] flex flex-col translate-y-[var(--dropdown-offset)]">
                <div class="p-6 grid grid-cols-1 md:grid-cols-[repeat(3,1fr)_300px] gap-8 overflow-y-auto min-h-0 mega-scroll">

                  <div>
                    <?= mega_col_heading('Explore Careers') ?>
                    <div class="space-y-1">
                      <?= mega_item(url('jobs'), 'fa-solid fa-briefcase', 'Job Board', 'Browse all job opportunities') ?>
                      <?= mega_item(url('jobs') . '?tab=search', 'fa-solid fa-magnifying-glass', 'Advanced Search', 'Find jobs that match your skills') ?>
                      <?= mega_item(url('jobs') . '?tab=companies', 'fa-solid fa-building', 'Companies Hiring', 'Explore companies hiring our alumni') ?>
                      <?= mega_item(url('jobs') . '?tab=saved', 'fa-solid fa-bookmark', 'Saved Jobs', 'View and manage your saved jobs') ?>
                      <?= mega_item(url('jobs') . '?tab=alerts', 'fa-regular fa-bell', 'Job Alerts', 'Create alerts and never miss an opportunity') ?>
                    </div>
                  </div>

                  <?php if ($authUser && $authUser['role'] === 'admin'): ?>
                    <div>
                      <?= mega_col_heading('Post & Manage') ?>
                      <div class="space-y-1">
                        <?= mega_item(url('admin/jobs/create'), 'fa-solid fa-square-plus', 'Post a Job', 'Share opportunities with our alumni') ?>
                        <?= mega_item(url('admin/jobs'), 'fa-solid fa-list-check', 'Manage Jobs', 'Edit, close or manage your job posts') ?>
                        <?= mega_item(url('admin/jobs'), 'fa-solid fa-user-group', 'Applications', 'View and manage applications') ?>
                        <?= mega_item(url('directory'), 'fa-solid fa-address-book', 'Talent Search', 'Search our alumni talent pool') ?>
                      </div>
                    </div>
                  <?php elseif ($authUser): ?>
                    <div>
                      <?= mega_col_heading('Post & Manage') ?>
                      <div class="space-y-1">
                        <?= mega_item(url('jobs/post'), 'fa-solid fa-square-plus', 'Post a Job', 'Share opportunities with our alumni') ?>
                        <?= mega_item(url('jobs/mine'), 'fa-solid fa-list-check', 'My Job Posts', 'Edit, close or manage your job posts') ?>
                        <?= mega_item(url('directory'), 'fa-solid fa-address-book', 'Talent Search', 'Search our alumni talent pool') ?>
                        <?= mega_item(url('jobs') . '?tab=companies', 'fa-solid fa-building', 'Companies Hiring', 'Explore companies hiring our alumni') ?>
                      </div>
                    </div>
                  <?php else: ?>
                    <div>
                      <?= mega_col_heading('Network') ?>
                      <div class="space-y-1">
                        <?= mega_item(url('login'), 'fa-solid fa-square-plus', 'Post a Job', 'Share opportunities with our alumni') ?>
                        <?= mega_item(url('jobs') . '?tab=companies', 'fa-solid fa-building', 'Companies Hiring', 'Explore companies hiring our alumni') ?>
                      </div>
                    </div>
                  <?php endif; ?>

                  <div>
                    <?= mega_col_heading('Career Resources') ?>
                    <div class="space-y-1">
                      <?= mega_item(url('resources'), 'fa-solid fa-file-lines', 'Career Resources', 'Guides, tips and templates') ?>
                      <?= mega_item(url('resources') . '?tab=resume-review', 'fa-solid fa-shield-halved', 'Resume Review', 'Get your resume reviewed by experts') ?>
                      <?= mega_item(url('resources') . '?tab=interview-prep', 'fa-regular fa-comment-dots', 'Interview Prep', 'Practice and prepare for interviews') ?>
                      <?= mega_item(url('mentorship'), 'fa-solid fa-chalkboard-user', 'Mentorship', 'Find a mentor or become a mentor') ?>
                      <?= mega_item(url('events') . '?view=webinars', 'fa-solid fa-video', 'Career Webinars', 'Watch webinars and career sessions') ?>
                    </div>
                  </div>

                  <div class="space-y-4">
                    <?php if ($careerFeaturedJob): ?>
                      <div class="mega-side-card">
                        <h5 class="text-[0.65rem] font-extrabold text-slate-500 uppercase tracking-wide mb-3">Featured Opportunity</h5>
                        <?= job_logo_html($careerFeaturedJob, 'w-10 h-10 mb-2') ?>
                        <p class="text-sm font-bold text-primary-navy leading-tight"><?= e($careerFeaturedJob['title']) ?></p>
                        <p class="text-xs text-slate-500 mb-1"><?= e($careerFeaturedJob['company']) ?></p>
                        <p class="text-xs text-slate-400 mb-2"><i class="fa-solid fa-location-dot"></i> <?= e($careerFeaturedJob['location']) ?></p>
                        <span class="badge bg-[var(--menu-accent-100)] text-[var(--menu-accent-700)] mb-3 inline-block"><?= e($careerFeaturedJob['job_type']) ?></span>
                        <a href="<?= e(url('jobs/' . $careerFeaturedJob['id'])) ?>" class="section-link block">View Job Details &rarr;</a>
                      </div>
                    <?php endif; ?>

                    <?php if ($authUser): ?>
                      <div class="mega-side-card">
                        <h5 class="text-[0.65rem] font-extrabold text-slate-500 uppercase tracking-wide mb-2">Looking to Hire?</h5>
                        <p class="text-xs text-slate-500 mb-3 leading-relaxed">Post your job and connect with top business school talent.</p>
                        <a href="<?= e(url($authUser['role'] === 'admin' ? 'admin/jobs/create' : 'jobs/post')) ?>" class="btn !bg-[var(--menu-accent-600)] !text-white hover:!bg-[var(--menu-accent-700)] !mt-0 !w-auto inline-block !px-4 !py-2 text-xs">Post a Job &rarr;</a>
                      </div>
                    <?php endif; ?>
                  </div>

                </div>

                <div class="mega-panel-footer flex-shrink-0">
                  <div class="flex items-center gap-3 flex-1 min-w-[220px]">
                    <div class="w-11 h-11 rounded-full bg-[var(--menu-accent-100)] text-[var(--menu-accent-600)] flex items-center justify-center text-lg flex-shrink-0"><i class="fa-solid fa-user-group"></i></div>
                    <div>
                      <p class="text-sm font-extrabold text-primary-navy">Hire Alumni. Build Leaders.</p>
                      <p class="text-xs text-slate-500">Our alumni are making an impact in top organizations worldwide.</p>
                      <a href="<?= e(url('jobs') . '?tab=companies') ?>" class="section-link">Learn more about hiring alumni &rarr;</a>
                    </div>
                  </div>
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-white flex items-center justify-center text-[var(--menu-accent-600)] flex-shrink-0"><i class="fa-solid fa-user-group"></i></div>
                    <div>
                      <p class="text-sm font-extrabold text-primary-navy"><?= e(Setting::get('metric_alumni', '0')) ?></p>
                      <p class="text-[0.65rem] text-slate-500">Active Alumni</p>
                    </div>
                  </div>
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-white flex items-center justify-center text-[var(--menu-accent-600)] flex-shrink-0"><i class="fa-solid fa-building"></i></div>
                    <div>
                      <p class="text-sm font-extrabold text-primary-navy"><?= (int) $companiesHiringCount ?>+</p>
                      <p class="text-[0.65rem] text-slate-500">Companies Hiring</p>
                    </div>
                  </div>
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-white flex items-center justify-center text-[var(--menu-accent-600)] flex-shrink-0"><i class="fa-solid fa-briefcase"></i></div>
                    <div>
                      <p class="text-sm font-extrabold text-primary-navy"><?= e(Setting::get('metric_jobs', '0')) ?></p>
                      <p class="text-[0.65rem] text-slate-500">Jobs Posted</p>
                    </div>
                  </div>
                  <a href="<?= e(url('jobs')) ?>" class="btn !bg-[var(--menu-accent-600)] !text-white hover:!bg-[var(--menu-accent-700)] !px-4 !py-2 text-xs whitespace-nowrap">Explore All Jobs &rarr;</a>
                </div>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <?php if (\App\Models\PageVisibility::shouldShowInNav('events', $authUser)): ?>
          <div class="relative group h-full" data-mega-group>
            <button class="top-pill h-full <?= $activeNav === 'events' ? 'active' : '' ?>">
              <i class="bi bi-calendar3"></i><span>Events</span>
            </button>

            <div class="fixed left-0 right-0 top-[var(--header-height)] hidden z-[60]" data-mega-panel>
              <div data-mega-caret class="absolute -top-2 -translate-x-1/2 translate-y-[var(--dropdown-offset)] z-[61] w-0 h-0 border-l-[8px] border-l-transparent border-r-[8px] border-r-transparent border-b-[8px] border-b-white" style="left:50%"></div>
              <div data-mega-box class="mx-auto bg-white shadow-2xl rounded-xl border border-slate-100 overflow-hidden w-[min(94vw,1080px)] max-h-[calc(100vh-var(--header-height)-1rem)] flex flex-col translate-y-[var(--dropdown-offset)]">
                <div class="p-6 grid grid-cols-1 md:grid-cols-[repeat(3,1fr)_300px] gap-8 overflow-y-auto min-h-0 mega-scroll">

                  <div>
                    <?= mega_col_heading('Explore Events') ?>
                    <div class="space-y-1">
                      <?= mega_item(url('events'), 'fa-solid fa-calendar-days', 'All Events', 'Browse upcoming events') ?>
                      <?= mega_item(url('events') . '?view=global', 'fa-solid fa-earth-africa', 'Global Events', 'Explore events worldwide') ?>
                      <?= mega_item(url('events') . '?view=reunions', 'fa-solid fa-user-group', 'Reunions', 'Class reunions & get-togethers') ?>
                      <?= mega_item(url('events') . '?view=executive', 'fa-solid fa-graduation-cap', 'Executive Programmes', 'Seminars & masterclasses') ?>
                      <?= mega_item(url('events') . '?view=webinars', 'fa-solid fa-display', 'Webinars', 'Live & on-demand sessions') ?>
                      <?= mega_item(url('events') . '?view=featured', 'fa-regular fa-star', 'Featured Events', 'Handpicked events for you') ?>
                    </div>
                  </div>

                  <div>
                    <?= mega_col_heading('My Events') ?>
                    <div class="space-y-1">
                      <?= mega_item(url('events/mine'), 'fa-solid fa-ticket', 'My Registrations', 'Events you have registered for') ?>
                      <?= mega_item(url('events/mine'), 'fa-regular fa-calendar-check', 'My Tickets', 'View and manage tickets') ?>
                      <?= mega_item(url('events/mine') . '?tab=saved', 'fa-regular fa-heart', 'Saved Events', 'Events you have saved') ?>
                      <?= mega_item(url('events/mine') . '?tab=history', 'fa-solid fa-clock-rotate-left', 'Event History', 'Events you have attended') ?>
                      <?= mega_item(url('notifications'), 'fa-regular fa-bell', 'My Alerts', 'Notifications & reminders') ?>
                    </div>
                  </div>

                  <?php if ($authUser && $authUser['role'] === 'admin'): ?>
                    <div>
                      <?= mega_col_heading('Host & Manage') ?>
                      <div class="space-y-1">
                        <?= mega_item(url('admin/events/create'), 'fa-solid fa-square-plus', 'Create Event', 'Organize an alumni event') ?>
                        <?= mega_item(url('admin/events'), 'fa-regular fa-calendar', 'Manage Events', 'Edit, update or cancel your events') ?>
                        <?= mega_item(url('admin/events'), 'fa-solid fa-user-group', 'Attendee List', 'View and manage attendees') ?>
                        <?= mega_item(url('admin/events'), 'fa-solid fa-bullhorn', 'Promote Event', 'Share with the alumni community') ?>
                        <?= mega_item(url('admin/events'), 'fa-solid fa-chart-column', 'Event Reports', 'Track engagement & attendance') ?>
                      </div>
                    </div>
                  <?php else: ?>
                    <div>
                      <?= mega_col_heading('Get Involved') ?>
                      <div class="space-y-1">
                        <?= mega_item(url('mentorship/become'), 'fa-solid fa-chalkboard-user', 'Become a Mentor', 'Give back through the mentorship program') ?>
                        <?= mega_item(url('give'), 'fa-solid fa-hand-holding-heart', 'Support Alumni Events', 'Help fund future alumni gatherings') ?>
                        <?= mega_item(url('knowledge'), 'fa-solid fa-book-open', 'Knowledge Hub', 'Explore alumni insights and research') ?>
                      </div>
                    </div>
                  <?php endif; ?>

                  <div class="mega-side-card">
                    <div class="flex items-center justify-between mb-3">
                      <h5 class="text-[0.65rem] font-extrabold text-slate-500 uppercase tracking-wide">Upcoming Events</h5>
                      <a href="<?= e(url('events')) ?>" class="text-[0.65rem] font-semibold text-gold hover:underline">View all &rarr;</a>
                    </div>
                    <div class="space-y-3 mb-3">
                      <?php foreach ($upcomingEventsMenu as $ev): ?>
                        <a href="<?= e(url('events/' . $ev['id'])) ?>" class="flex items-start gap-2.5">
                          <div class="bg-primary-navy text-white rounded p-1 text-center min-w-[36px] h-[36px] flex flex-col justify-center flex-shrink-0">
                            <span class="text-[0.5rem] font-bold uppercase leading-none"><?= e(format_date($ev['event_date'], 'M')) ?></span>
                            <span class="text-xs font-extrabold leading-none"><?= e(format_date($ev['event_date'], 'd')) ?></span>
                          </div>
                          <div class="min-w-0">
                            <p class="text-xs font-bold text-primary-navy leading-tight truncate"><?= e($ev['title']) ?></p>
                            <p class="text-[0.65rem] text-slate-500 truncate"><i class="fa-solid fa-location-dot"></i> <?= e($ev['location']) ?></p>
                            <?php if ($ev['event_time']): ?><p class="text-[0.65rem] text-slate-400"><i class="fa-regular fa-clock"></i> <?= e($ev['event_time']) ?></p><?php endif; ?>
                          </div>
                        </a>
                      <?php endforeach; ?>
                      <?php if (empty($upcomingEventsMenu)): ?>
                        <p class="text-xs text-slate-400">No upcoming events scheduled.</p>
                      <?php endif; ?>
                    </div>
                    <a href="<?= e(url('events') . '?display=calendar') ?>" class="btn !bg-[var(--menu-accent-600)] !text-white hover:!bg-[var(--menu-accent-700)] !mt-0 block text-center !px-4 !py-2 text-xs"><i class="fa-regular fa-calendar"></i> View Event Calendar</a>
                  </div>

                </div>

                <div class="mega-panel-footer flex-shrink-0">
                  <div class="flex items-center gap-3 flex-1 min-w-[220px]">
                    <div class="w-11 h-11 rounded-full bg-[var(--menu-accent-100)] text-[var(--menu-accent-600)] flex items-center justify-center text-lg flex-shrink-0"><i class="fa-solid fa-user-group"></i></div>
                    <div>
                      <p class="text-sm font-extrabold text-primary-navy">Connect. Learn. Grow.</p>
                      <p class="text-xs text-slate-500">Join alumni events to expand your network and gain valuable insights.</p>
                    </div>
                  </div>
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-white flex items-center justify-center text-[var(--menu-accent-600)] flex-shrink-0"><i class="fa-solid fa-calendar-days"></i></div>
                    <div>
                      <p class="text-sm font-extrabold text-primary-navy"><?= e(Setting::get('metric_events', '0')) ?></p>
                      <p class="text-[0.65rem] text-slate-500">Events per Year</p>
                    </div>
                  </div>
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-white flex items-center justify-center text-[var(--menu-accent-600)] flex-shrink-0"><i class="fa-solid fa-user-group"></i></div>
                    <div>
                      <p class="text-sm font-extrabold text-primary-navy"><?= e(Setting::get('metric_alumni', '0')) ?></p>
                      <p class="text-[0.65rem] text-slate-500">Active Alumni</p>
                    </div>
                  </div>
                  <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-full bg-white flex items-center justify-center text-[var(--menu-accent-600)] flex-shrink-0"><i class="fa-solid fa-earth-africa"></i></div>
                    <div>
                      <p class="text-sm font-extrabold text-primary-navy"><?= e(Setting::get('metric_countries', '0')) ?></p>
                      <p class="text-[0.65rem] text-slate-500">Countries</p>
                    </div>
                  </div>
                  <a href="<?= e(url('events')) ?>" class="btn !bg-[var(--menu-accent-600)] !text-white hover:!bg-[var(--menu-accent-700)] !px-4 !py-2 text-xs whitespace-nowrap">Explore All Events &rarr;</a>
                </div>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <?php if (\App\Models\PageVisibility::shouldShowInNav('knowledge', $authUser)): ?>
          <div class="relative group h-full" data-mega-group>
            <button class="top-pill h-full <?= $activeNav === 'knowledge' ? 'active' : '' ?>">
              <i class="bi bi-book"></i><span>Knowledge</span>
            </button>

            <div class="fixed left-0 right-0 top-[var(--header-height)] hidden z-[60]" data-mega-panel>
              <div data-mega-caret class="absolute -top-2 -translate-x-1/2 translate-y-[var(--dropdown-offset)] z-[61] w-0 h-0 border-l-[8px] border-l-transparent border-r-[8px] border-r-transparent border-b-[8px] border-b-white" style="left:50%"></div>
              <div data-mega-box class="mx-auto bg-white shadow-2xl rounded-xl border border-slate-100 overflow-hidden w-[min(94vw,1080px)] max-h-[calc(100vh-var(--header-height)-1rem)] flex flex-col translate-y-[var(--dropdown-offset)]">
                <div class="p-6 grid grid-cols-1 md:grid-cols-[repeat(3,1fr)_300px] gap-8 overflow-y-auto min-h-0 mega-scroll">

                  <div>
                    <?= mega_col_heading('Explore Knowledge') ?>
                    <div class="space-y-1">
                      <?= mega_item(url('knowledge'), 'fa-solid fa-book-open', 'All Articles', 'Browse all insights and articles') ?>
                      <?= mega_item(url('knowledge?category=research'), 'fa-regular fa-file-lines', 'Research & Publications', 'Explore research, papers and publications') ?>
                      <?= mega_item(url('knowledge'), 'fa-solid fa-circle-play', 'Videos & Podcasts', 'Watch and listen to expert sessions') ?>
                      <?= mega_item(url('knowledge?category=case_study'), 'fa-solid fa-book', 'Case Studies', 'Real-world business case studies') ?>
                      <?= mega_item(url('knowledge?category=article'), 'fa-regular fa-lightbulb', 'Thought Leadership', 'Insights from industry leaders') ?>
                      <?= mega_item(url('knowledge'), 'fa-solid fa-tags', 'Topics & Categories', 'Explore content by topics that matter') ?>
                    </div>
                  </div>

                  <div>
                    <?= mega_col_heading('Contribute & Engage') ?>
                    <div class="space-y-1">
                      <?= mega_item(url($authUser ? 'knowledge/submit' : 'login'), 'fa-solid fa-pen-to-square', 'Write an Article', 'Share your knowledge with the community') ?>
                      <?= mega_item(url($authUser ? 'knowledge/submit' : 'login'), 'fa-solid fa-arrow-up-from-bracket', 'Submit Research', 'Publish your research or case study') ?>
                      <?= mega_item(url('feed'), 'fa-regular fa-comment-dots', 'Start a Discussion', 'Ask questions and share ideas') ?>
                      <?= mega_item(url('feed'), 'fa-solid fa-chart-simple', 'Create a Poll', 'Get opinions from the community') ?>
                      <?= mega_item(url('knowledge'), 'fa-solid fa-award', 'Featured Contributors', 'Top contributors and thought leaders') ?>
                    </div>
                  </div>

                  <div>
                    <?= mega_col_heading('Learn & Grow') ?>
                    <div class="space-y-1">
                      <?= mega_item(url('knowledge'), 'fa-solid fa-graduation-cap', 'Executive Summaries', 'Key insights in short, actionable reads') ?>
                      <?= mega_item(url('knowledge'), 'fa-regular fa-bookmark', 'Recommended for You', 'Personalized content recommendations') ?>
                      <?= mega_item(url('knowledge'), 'fa-solid fa-arrow-trend-up', 'Trending Now', 'Most popular content right now') ?>
                      <?= mega_item(url('knowledge'), 'fa-regular fa-clock', 'Recently Added', 'Latest articles and resources') ?>
                      <?= mega_item(url('resources'), 'fa-solid fa-download', 'Resource Library', 'Templates, guides and downloadables') ?>
                      <?php if ($authUser): ?>
                        <?= mega_item(url('knowledge/submit'), 'fa-regular fa-bookmark', 'My Collections', 'Save and organize content you love') ?>
                      <?php endif; ?>
                    </div>
                  </div>

                  <div class="space-y-4">
                    <div class="mega-side-card">
                      <div class="flex items-center justify-between mb-3">
                        <h5 class="text-[0.65rem] font-extrabold text-slate-500 uppercase tracking-wide">Featured Article</h5>
                        <a href="<?= e(url('knowledge')) ?>" class="text-[0.65rem] font-semibold text-gold hover:underline">View all</a>
                      </div>
                      <?php if ($featuredArticle): ?>
                        <a href="<?= e(url('knowledge/' . $featuredArticle['id'])) ?>" class="block">
                          <div class="w-full h-24 rounded-lg mb-2 bg-gradient-to-br from-primary-navy to-[var(--menu-accent-900)] flex items-center justify-center">
                            <i class="fa-regular fa-lightbulb text-2xl text-gold"></i>
                          </div>
                          <span class="badge bg-[var(--menu-accent-100)] text-[var(--menu-accent-700)] mb-2 inline-block"><?= e(strtoupper(article_category_label($featuredArticle['category']))) ?></span>
                          <p class="text-sm font-bold text-primary-navy leading-tight mb-1"><?= e($featuredArticle['title']) ?></p>
                          <p class="text-xs text-slate-500"><?= e($featuredArticle['author_name']) ?></p>
                          <p class="text-[0.65rem] text-slate-400 mb-2"><?= e(format_date($featuredArticle['published_at'])) ?> &bull; <?= reading_time_minutes($featuredArticle['body']) ?> min read</p>
                          <span class="section-link">Read Article &rarr;</span>
                        </a>
                      <?php else: ?>
                        <p class="text-xs text-slate-400">No articles published yet.</p>
                      <?php endif; ?>
                    </div>

                    <div class="mega-side-card">
                      <h5 class="text-[0.65rem] font-extrabold text-slate-500 uppercase tracking-wide mb-3">Trending Topics</h5>
                      <div class="flex flex-wrap gap-1.5">
                        <?php foreach (['Leadership','Strategy','Innovation','Finance','Entrepreneurship','AI & Tech','Marketing','Sustainability','Operations'] as $topic): ?>
                          <a href="<?= e(url('knowledge?q=' . urlencode($topic))) ?>" class="badge bg-[var(--menu-accent-50)] text-[var(--menu-accent-700)] hover:bg-[var(--menu-accent-100)]"><?= e($topic) ?></a>
                        <?php endforeach; ?>
                      </div>
                    </div>
                  </div>

                </div>

                <div class="mega-panel-footer flex-shrink-0">
                  <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full bg-[var(--menu-accent-100)] text-[var(--menu-accent-600)] flex items-center justify-center text-lg flex-shrink-0"><i class="fa-solid fa-star"></i></div>
                    <div>
                      <p class="text-sm font-extrabold text-primary-navy">Share your expertise. Inspire the next generation of business leaders.</p>
                      <p class="text-xs text-slate-500">Your knowledge can make a lasting impact.</p>
                    </div>
                  </div>
                  <a href="<?= e(url($authUser ? 'knowledge/submit' : 'login')) ?>" class="btn !bg-[var(--menu-accent-600)] !text-white hover:!bg-[var(--menu-accent-700)] !px-5 !py-2.5 text-xs whitespace-nowrap">Contribute Now &rarr;</a>
                </div>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <?php if (\App\Models\PageVisibility::shouldShowInNav('benefits', $authUser)): ?>
          <div class="relative group h-full" data-mega-group>
            <button class="top-pill h-full <?= $activeNav === 'benefits' ? 'active' : '' ?>">
              <i class="bi bi-gift"></i><span>Benefits</span>
            </button>

            <div class="fixed left-0 right-0 top-[var(--header-height)] hidden z-[60]" data-mega-panel>
              <div data-mega-caret class="absolute -top-2 -translate-x-1/2 translate-y-[var(--dropdown-offset)] z-[61] w-0 h-0 border-l-[8px] border-l-transparent border-r-[8px] border-r-transparent border-b-[8px] border-b-white" style="left:50%"></div>
              <div data-mega-box class="mx-auto bg-white shadow-2xl rounded-xl border border-slate-100 overflow-hidden w-[min(94vw,1080px)] max-h-[calc(100vh-var(--header-height)-1rem)] flex flex-col translate-y-[var(--dropdown-offset)]">
                <div class="p-6 grid grid-cols-1 md:grid-cols-[repeat(3,1fr)_300px] gap-8 overflow-y-auto min-h-0 mega-scroll">

                  <div>
                    <?= mega_col_heading('Explore Benefits') ?>
                    <div class="space-y-1">
                      <?= mega_item(url('benefits'), 'fa-solid fa-gem', 'All Benefits', 'Browse all exclusive alumni benefits') ?>
                      <?= mega_item(url('benefits'), 'fa-solid fa-percent', 'Discounts & Offers', 'Enjoy savings from our partner organizations') ?>
                      <?= mega_item(url('benefits'), 'fa-solid fa-book-open-reader', 'Executive Education', 'Access discounted programs and courses') ?>
                      <?= mega_item(url('benefits'), 'fa-solid fa-building-columns', 'Library & Resources', 'Access books, journals and research databases') ?>
                      <?= mega_item(url('benefits'), 'fa-regular fa-id-card', 'Professional Services', 'Legal, financial and consulting support') ?>
                      <?= mega_item(url('benefits'), 'fa-solid fa-plane', 'Travel & Lifestyle', 'Travel perks and lifestyle discounts') ?>
                    </div>
                    <a href="<?= e(url('benefits')) ?>" class="mt-3 inline-flex items-center gap-2 text-xs font-semibold text-[var(--menu-accent-600)] border border-[var(--menu-accent-200)] rounded-lg px-3 py-2 hover:bg-[var(--menu-accent-50)]"><i class="fa-solid fa-table-cells"></i> View All Benefits &rarr;</a>
                  </div>

                  <div>
                    <?= mega_col_heading('Learning & Growth') ?>
                    <div class="space-y-1">
                      <?= mega_item(url('benefits'), 'fa-solid fa-graduation-cap', 'Continuing Education', 'Lifelong learning opportunities and certifications') ?>
                      <?= mega_item(url('events') . '?view=webinars', 'fa-solid fa-display', 'Webinars & Workshops', 'Free and discounted events and workshops') ?>
                      <?= mega_item(url('mentorship'), 'fa-regular fa-user', 'Mentorship Programs', 'Give and receive guidance from experienced alumni') ?>
                      <?= mega_item(url('resources'), 'fa-solid fa-arrow-trend-up', 'Career Development', 'Tools and resources to accelerate your career') ?>
                      <?= mega_item(url('events'), 'fa-regular fa-file-lines', 'Case Competitions', 'Participate in exclusive alumni competitions') ?>
                    </div>
                  </div>

                  <div>
                    <?= mega_col_heading('Connections & Access') ?>
                    <div class="space-y-1">
                      <?= mega_item(url('events'), 'fa-solid fa-user-group', 'Networking Events', 'Exclusive invites to alumni networking events') ?>
                      <?= mega_item(url('map'), 'fa-solid fa-earth-africa', 'Global Alumni Access', 'Connect with alumni worldwide') ?>
                      <?= mega_item(url('directory'), 'fa-regular fa-star', 'Alumni Directory Access', 'Full access to the alumni directory') ?>
                      <?= mega_item(url('knowledge'), 'fa-solid fa-microphone', 'Speaking Opportunities', 'Share your expertise and inspire others') ?>
                      <?= mega_item(url('businesses'), 'fa-solid fa-handshake', 'Business Referrals', 'Connect and grow through trusted referrals') ?>
                    </div>
                  </div>

                  <div class="space-y-4">
                    <?php if ($featuredBenefit): ?>
                      <div class="rounded-lg overflow-hidden bg-gradient-to-br from-primary-navy to-[var(--menu-accent-900)] text-white">
                        <div class="flex items-center justify-between px-4 pt-3">
                          <h5 class="text-[0.65rem] font-extrabold text-[var(--menu-accent-200)] uppercase tracking-wide">Featured Benefit</h5>
                          <a href="<?= e(url('benefits')) ?>" class="text-[0.65rem] font-semibold text-[var(--menu-accent-200)] hover:text-white">View all</a>
                        </div>
                        <div class="p-4 pt-2">
                          <div class="w-10 h-10 rounded-lg bg-white/10 flex items-center justify-center mb-3"><i class="fa-solid fa-gift text-gold"></i></div>
                          <p class="text-base font-extrabold leading-tight mb-1"><?= e($featuredBenefit['title']) ?></p>
                          <p class="text-xs text-[var(--menu-accent-100)] mb-3 leading-relaxed"><?= e($featuredBenefit['description']) ?></p>
                          <a href="<?= e(url('benefits')) ?>" class="btn bg-white !text-primary-navy hover:bg-slate-100 !px-4 !py-2 text-xs inline-block">Explore Benefit &rarr;</a>
                        </div>
                      </div>
                    <?php endif; ?>

                    <div class="mega-side-card">
                      <div class="flex items-center justify-between mb-3">
                        <h5 class="text-[0.65rem] font-extrabold text-slate-500 uppercase tracking-wide">Popular Partners</h5>
                        <a href="<?= e(url('benefits')) ?>" class="text-[0.65rem] font-semibold text-gold hover:underline">View all</a>
                      </div>
                      <div class="space-y-2.5">
                        <?php foreach ($popularPartners as $partner): ?>
                          <a href="<?= e(url('benefits')) ?>" class="flex items-center justify-between gap-2">
                            <span class="text-xs font-bold text-primary-navy"><?= e($partner['name']) ?></span>
                            <span class="text-[0.65rem] font-semibold text-[var(--menu-accent-600)] flex items-center gap-1"><?= e($partner['offer']) ?> <i class="fa-solid fa-arrow-right text-[0.6rem]"></i></span>
                          </a>
                        <?php endforeach; ?>
                      </div>
                    </div>

                    <div class="mega-side-card">
                      <h5 class="text-[0.65rem] font-extrabold text-slate-500 uppercase tracking-wide mb-2">Need Help?</h5>
                      <div class="flex items-start gap-2.5">
                        <div class="w-9 h-9 rounded-full bg-[var(--menu-accent-100)] text-[var(--menu-accent-600)] flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-headset"></i></div>
                        <div>
                          <p class="text-xs text-slate-500 leading-relaxed">Our team is here to help you make the most of your alumni benefits.</p>
                          <a href="<?= e(url('benefits')) ?>" class="text-xs font-semibold text-[var(--menu-accent-600)] hover:underline">Contact Alumni Support &rarr;</a>
                        </div>
                      </div>
                    </div>
                  </div>

                </div>

                <div class="mega-panel-footer flex-shrink-0">
                  <div class="flex items-center gap-3">
                    <div class="w-11 h-11 rounded-full bg-[var(--menu-accent-100)] text-[var(--menu-accent-600)] flex items-center justify-center text-lg flex-shrink-0"><i class="fa-regular fa-star"></i></div>
                    <div>
                      <p class="text-sm font-extrabold text-primary-navy">Your alumni status comes with exclusive privileges.</p>
                      <p class="text-xs text-slate-500">Explore, connect and grow with benefits designed just for you.</p>
                    </div>
                  </div>
                  <a href="<?= e(url('benefits')) ?>" class="btn !bg-[var(--menu-accent-600)] !text-white hover:!bg-[var(--menu-accent-700)] !px-5 !py-2.5 text-xs whitespace-nowrap">Explore All Benefits &rarr;</a>
                </div>
              </div>
            </div>
          </div>
          <?php endif; ?>

          <?php if (\App\Models\PageVisibility::shouldShowInNav('give', $authUser)): ?>
            <div class="relative h-full">
              <a href="<?= e(url('give')) ?>" class="top-pill h-full hover:!text-gold hover:!bg-white/10 <?= $activeNav === 'give' ? 'active' : '' ?>">
                <i class="bi bi-heart"></i><span>Give Back</span>
              </a>

              <?php if (false): // Give Back mega menu suspended — flip to true to re-enable ?>
              <div class="fixed left-0 right-0 top-[var(--header-height)] hidden z-[60]" data-mega-panel>
              <div data-mega-caret class="absolute -top-2 -translate-x-1/2 translate-y-[var(--dropdown-offset)] z-[61] w-0 h-0 border-l-[8px] border-l-transparent border-r-[8px] border-r-transparent border-b-[8px] border-b-white" style="left:50%"></div>
                <div data-mega-box class="mx-auto bg-white shadow-2xl rounded-xl border border-slate-100 overflow-hidden w-[min(94vw,1080px)] max-h-[calc(100vh-var(--header-height)-1rem)] flex flex-col translate-y-[var(--dropdown-offset)]">
                  <div class="p-6 grid grid-cols-1 md:grid-cols-[repeat(3,1fr)_300px] gap-8 overflow-y-auto min-h-0 mega-scroll">

                    <div>
                      <?= mega_col_heading('Support the Future') ?>
                      <div class="space-y-1">
                        <?= mega_item(url('give/causes/make-a-donation'), 'fa-solid fa-heart', 'Make a Donation', 'Contribute to initiatives that transform lives') ?>
                        <?= mega_item(url('give/causes/scholarships'), 'fa-solid fa-graduation-cap', 'Scholarships', 'Support students through scholarships and bursaries') ?>
                        <?= mega_item(url('give/causes/academic-excellence'), 'fa-solid fa-building-columns', 'Academic Excellence', 'Fund faculty, research and academic programs') ?>
                        <?= mega_item(url('give/causes/innovation-research'), 'fa-regular fa-lightbulb', 'Innovation & Research', 'Drive innovation and research that matters') ?>
                        <?= mega_item(url('give/causes/student-development'), 'fa-solid fa-seedling', 'Student Development', 'Empower student clubs, projects and competitions') ?>
                        <?= mega_item(url('give/causes/facilities-infrastructure'), 'fa-solid fa-trophy', 'Facilities & Infrastructure', 'Help build world-class facilities') ?>
                      </div>
                      <a href="<?= e(url('give')) ?>" class="mt-3 inline-flex items-center gap-2 text-xs font-semibold text-[var(--menu-accent-600)] border border-[var(--menu-accent-200)] rounded-lg px-3 py-2 hover:bg-[var(--menu-accent-50)]"><i class="fa-solid fa-table-cells"></i> View All Causes &rarr;</a>
                    </div>

                    <div>
                      <?= mega_col_heading('Get Involved') ?>
                      <div class="space-y-1">
                        <?= mega_item(url('give/causes/volunteer'), 'fa-regular fa-hand', 'Volunteer', 'Share your time and expertise') ?>
                        <?= mega_item(url('mentorship/become'), 'fa-solid fa-user-group', 'Mentor Students', 'Guide and inspire the next generation') ?>
                        <?= mega_item(url('give/causes/guest-speaker'), 'fa-solid fa-microphone', 'Guest Speaker', 'Share your insights and experiences') ?>
                        <?= mega_item(url('give/causes/industry-connect'), 'fa-solid fa-handshake', 'Industry Connect', 'Create opportunities for students') ?>
                        <?= mega_item(url($authUser ? 'jobs/post' : 'login'), 'fa-solid fa-briefcase', 'Internship Opportunities', 'Offer internships and hands-on experience') ?>
                        <?= mega_item(url('give/causes/pro-bono-support'), 'fa-solid fa-scale-balanced', 'Pro Bono Support', 'Provide professional expertise for good') ?>
                      </div>
                    </div>

                    <div>
                      <?= mega_col_heading('Create Impact') ?>
                      <div class="space-y-1">
                        <?= mega_item(url('give/causes/start-a-fund'), 'fa-solid fa-hand-holding-dollar', 'Start a Fund', 'Establish a fund for a cause you care about') ?>
                        <?= mega_item(url('give/causes/corporate-partnerships'), 'fa-solid fa-building', 'Corporate Partnerships', 'Partner with the school to drive impact') ?>
                        <?= mega_item(url('give/causes/endow-a-chair'), 'fa-solid fa-chair', 'Endow a Chair', 'Support academic chairs and professorships') ?>
                        <?= mega_item(url('give/causes/leave-a-legacy'), 'fa-solid fa-leaf', 'Leave a Legacy', 'Include the Business School in your estate plans') ?>
                        <?= mega_item(url('give/causes/giving-societies'), 'fa-solid fa-people-group', 'Giving Societies', 'Join leadership giving societies') ?>
                        <?= mega_item(url('give/causes/impact-reports'), 'fa-solid fa-chart-column', 'Impact Reports', 'See how your support makes a difference') ?>
                      </div>
                    </div>

                    <div class="space-y-4">
                      <?php if ($featuredCampaign): $pct = $featuredCampaign['goal_amount'] > 0 ? min(100, round($featuredCampaign['raised_amount'] / $featuredCampaign['goal_amount'] * 100)) : 0; ?>
                        <div class="rounded-lg overflow-hidden bg-gradient-to-br from-primary-navy to-[var(--menu-accent-900)] text-white">
                          <div class="flex items-center justify-between px-4 pt-3">
                            <h5 class="text-[0.65rem] font-extrabold text-[var(--menu-accent-200)] uppercase tracking-wide">Featured Campaign</h5>
                            <a href="<?= e(url('give')) ?>" class="text-[0.65rem] font-semibold text-[var(--menu-accent-200)] hover:text-white">View all</a>
                          </div>
                          <?php if (!empty($featuredCampaign['image'])): ?>
                            <img src="<?= e($featuredCampaign['image']) ?>" alt="" class="w-full h-24 object-cover mt-2">
                          <?php else: ?>
                            <div class="w-full h-24 mt-2 flex items-center justify-center bg-white/10"><i class="fa-solid fa-hand-holding-heart text-2xl text-gold"></i></div>
                          <?php endif; ?>
                          <div class="p-4">
                            <p class="text-base font-extrabold leading-tight mb-1"><?= e($featuredCampaign['title']) ?></p>
                            <p class="text-xs text-[var(--menu-accent-100)] mb-3 leading-relaxed"><?= e($featuredCampaign['description']) ?></p>
                            <div class="w-full bg-white/20 rounded-full h-1.5 mb-1.5">
                              <div class="bg-gold h-1.5 rounded-full" style="width: <?= $pct ?>%"></div>
                            </div>
                            <p class="text-[0.65rem] text-[var(--menu-accent-100)] mb-3">&#8358;<?= number_format((float) $featuredCampaign['raised_amount']) ?> raised of &#8358;<?= number_format((float) $featuredCampaign['goal_amount']) ?> &bull; <?= $pct ?>%</p>
                            <a href="<?= e(url('give')) ?>" class="btn bg-white !text-primary-navy hover:bg-slate-100 !px-4 !py-2 text-xs inline-block"><i class="fa-solid fa-heart"></i> Donate Now</a>
                          </div>
                        </div>
                      <?php endif; ?>

                      <div class="mega-side-card">
                        <div class="flex items-center justify-between mb-3">
                          <h5 class="text-[0.65rem] font-extrabold text-slate-500 uppercase tracking-wide">Recent Impact</h5>
                          <a href="<?= e(url('give')) ?>" class="text-[0.65rem] font-semibold text-gold hover:underline">View all</a>
                        </div>
                        <div class="space-y-2.5">
                          <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-[var(--menu-accent-100)] text-[var(--menu-accent-600)] flex items-center justify-center flex-shrink-0 text-xs"><i class="fa-solid fa-graduation-cap"></i></div>
                            <p class="text-xs text-slate-600 flex-1"><span class="font-bold text-primary-navy"><?= e($footerSettings['metric_scholarships'] ?? '0') ?></span> Scholarships Awarded</p>
                            <span class="text-[0.6rem] text-slate-400 whitespace-nowrap">This Year</span>
                          </div>
                          <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-[var(--menu-accent-100)] text-[var(--menu-accent-600)] flex items-center justify-center flex-shrink-0 text-xs"><i class="fa-solid fa-user-group"></i></div>
                            <p class="text-xs text-slate-600 flex-1"><span class="font-bold text-primary-navy"><?= e($footerSettings['metric_mentors_engaged'] ?? '0') ?></span> Mentors Engaged</p>
                            <span class="text-[0.6rem] text-slate-400 whitespace-nowrap">This Year</span>
                          </div>
                          <div class="flex items-center gap-2.5">
                            <div class="w-8 h-8 rounded-full bg-[var(--menu-accent-100)] text-[var(--menu-accent-600)] flex items-center justify-center flex-shrink-0 text-xs"><i class="fa-solid fa-building-columns"></i></div>
                            <p class="text-xs text-slate-600 flex-1"><span class="font-bold text-primary-navy"><?= e($footerSettings['metric_projects_funded'] ?? '0') ?></span> Projects Funded</p>
                            <span class="text-[0.6rem] text-slate-400 whitespace-nowrap">This Year</span>
                          </div>
                        </div>
                      </div>

                      <div class="mega-side-card">
                        <div class="flex items-start gap-2.5">
                          <div class="w-9 h-9 rounded-full bg-[var(--menu-accent-100)] text-[var(--menu-accent-600)] flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-hand-holding-heart"></i></div>
                          <div class="flex-1">
                            <p class="text-xs font-extrabold text-primary-navy uppercase tracking-wide mb-1">Make a Difference Today</p>
                            <p class="text-xs text-slate-500 mb-3 leading-relaxed">Every contribution, big or small, creates a lasting impact.</p>
                            <a href="<?= e(url('give')) ?>" class="btn !bg-[var(--menu-accent-600)] !text-white hover:!bg-[var(--menu-accent-700)] !mt-0 !w-auto inline-block !px-4 !py-2 text-xs">Give Now &rarr;</a>
                          </div>
                        </div>
                      </div>
                    </div>

                  </div>

                  <div class="mega-panel-footer flex-shrink-0">
                    <div class="flex items-center gap-3">
                      <div class="w-11 h-11 rounded-full bg-[var(--menu-accent-100)] text-[var(--menu-accent-600)] flex items-center justify-center text-lg flex-shrink-0"><i class="fa-solid fa-hand-holding-heart"></i></div>
                      <div>
                        <p class="text-sm font-extrabold text-primary-navy">Together, we build a stronger community.</p>
                        <p class="text-xs text-slate-500">Your support empowers students, advances knowledge and shapes a better future for all.</p>
                      </div>
                    </div>
                    <a href="<?= e(url('give')) ?>" class="btn !bg-[var(--menu-accent-600)] !text-white hover:!bg-[var(--menu-accent-700)] !px-5 !py-2.5 text-xs whitespace-nowrap">Explore Ways to Give &rarr;</a>
                  </div>
                </div>
              </div>
              <?php endif; ?>
            </div>
          <?php endif; ?>
        </nav>

        <div class="flex items-center gap-4 flex-shrink-0">
          <?php if ($authUser): ?>
            <a href="<?= e(url('notifications')) ?>" class="relative text-slate-300 hover:text-white" id="notification-bell">
              <i class="fa-regular fa-bell text-lg"></i>
              <span id="notification-badge" class="absolute -top-1.5 -right-2 bg-gold text-white text-[0.6rem] font-bold w-4 h-4 rounded-full flex items-center justify-center <?= $unreadNotificationCount > 0 ? '' : 'hidden' ?>"><?= min(9, $unreadNotificationCount) ?><?= $unreadNotificationCount > 9 ? '+' : '' ?></span>
            </a>
            <div class="relative group">
              <button class="flex items-center gap-2.5">
                <?= avatar_html($authUser, 'w-9 h-9 border-2 border-white/20') ?>
                <span class="hidden md:block text-left leading-tight">
                  <span class="block text-white text-xs font-bold truncate max-w-[110px]"><?= e($authUser['name']) ?></span>
                  <?php if ($cohortBadge): ?><span class="block text-gold text-[0.65rem] font-semibold"><?= e($cohortBadge) ?></span><?php endif; ?>
                </span>
                <i class="fa-solid fa-angle-down text-[0.65rem] text-slate-400"></i>
              </button>
              <div class="absolute right-0 top-full pt-2 hidden group-hover:block z-40">
                <div class="bg-white min-w-[180px] shadow-lg rounded-md border border-slate-200 py-2">
                  <div class="px-4 py-1.5 text-xs font-semibold text-primary-navy truncate"><?= e($authUser['name']) ?></div>
                  <a href="<?= e(url('profile/edit')) ?>" class="block px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 hover:text-gold">Edit Profile</a>
                  <?php if ($authUser['role'] === 'admin'): ?>
                    <a href="<?= e(url('admin/dashboard')) ?>" class="block px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 hover:text-gold">Admin Panel</a>
                  <?php endif; ?>
                  <?php if (in_array($authUser['role'], ['admin', 'editor'], true)): ?>
                    <a href="<?= e(url('admin/register-alumni')) ?>" class="block px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 hover:text-gold">Register Alumni</a>
                  <?php endif; ?>
                  <form method="POST" action="<?= e(url('logout')) ?>">
                    <?= csrf_field() ?>
                    <button type="submit" class="w-full text-left px-4 py-2 text-xs text-slate-700 hover:bg-slate-50 hover:text-gold">Logout</button>
                  </form>
                </div>
              </div>
            </div>
          <?php else: ?>
            <a href="<?= e(url('login')) ?>" class="text-xs font-semibold text-slate-200 hover:text-gold uppercase hidden sm:inline">Login</a>
            <a href="<?= e(url('register')) ?>" class="btn-gold !px-4 !py-2 text-xs hidden sm:inline-block">Join Now</a>
          <?php endif; ?>
          <button type="button" id="mobile-menu-open" class="lg:hidden text-white text-xl cursor-pointer">
            <i class="fa-solid fa-bars"></i>
          </button>
        </div>
      </div>
    </div>

    <?php
    /** One accordion header row + its 2-col grid of sub-items, for the redesigned mobile menu. $pageKey ties it to a Page Visibility row (admin's "Show in Menu" toggle); pass '' for bundles with no single governing page. */
    $mobileAccordion = function (string $key, string $pageKey, string $icon, string $title, string $description, array $items) use ($authUser) {
        if (!$items || !\App\Models\PageVisibility::shouldShowInNav($pageKey, $authUser)) {
            return;
        }
        $items = array_filter($items, function ($item) use ($authUser) {
            $itemPageKey = url_page_key($item['href']);
            if (!\App\Models\PageVisibility::shouldShowInNav($itemPageKey, $authUser)) {
                return false;
            }
            $tabKey = url_tab_key($item['href']);
            if ($tabKey !== null && !\App\Models\PageTabVisibility::isVisible($itemPageKey, $tabKey)) {
                return false;
            }
            return \App\Models\LinkVisibility::isVisible(link_visibility_key($itemPageKey, $item['label']));
        });
        if (!$items) {
            return;
        }
        ?>
        <details class="group bg-[var(--menu-accent-50)] rounded-xl">
          <summary class="flex items-center gap-3 p-4 cursor-pointer list-none marker:hidden [&::-webkit-details-marker]:hidden">
            <div class="w-11 h-11 rounded-lg bg-[var(--menu-accent-100)] text-gold flex items-center justify-center flex-shrink-0 text-lg"><i class="<?= e($icon) ?>"></i></div>
            <div class="flex-1 min-w-0">
              <p class="text-sm font-extrabold text-primary-navy"><?= e($title) ?></p>
              <p class="text-xs text-slate-500 mt-0.5"><?= e($description) ?></p>
            </div>
            <i class="fa-solid fa-chevron-down text-gold text-sm flex-shrink-0 transition-transform group-open:rotate-180"></i>
          </summary>
          <div class="grid grid-cols-2 gap-3 px-4 pb-4 pt-1">
            <?php foreach ($items as $item): ?>
              <a href="<?= e($item['href']) ?>" class="rounded-lg p-2">
                <div class="w-9 h-9 rounded-lg bg-white text-gold flex items-center justify-center mb-2"><i class="<?= e($item['icon']) ?>"></i></div>
                <p class="text-sm font-bold text-primary-navy leading-tight"><?= e($item['label']) ?></p>
                <p class="text-xs text-slate-500 mt-0.5 leading-snug"><?= e($item['desc']) ?></p>
              </a>
            <?php endforeach; ?>
          </div>
        </details>
        <?php
    };
    ?>
    <div id="mobile-menu-overlay" class="hidden lg:!hidden fixed inset-0 z-[100] bg-white flex-col">
      <div class="bg-primary-navy text-white px-4 py-3.5 flex items-center justify-between flex-shrink-0">
        <a href="<?= e(url('/')) ?>" class="flex items-center gap-2.5 min-w-0">
          <i class="fa-solid fa-building-columns text-gold text-lg flex-shrink-0"></i>
          <span class="min-w-0">
            <span class="block text-xs font-extrabold tracking-wide truncate">ROME BUSINESS SCHOOL</span>
            <span class="block text-[0.65rem] text-gold font-bold tracking-wide">ALUMNI PORTAL</span>
          </span>
        </a>
        <button type="button" id="mobile-menu-close" class="text-white text-xl cursor-pointer flex-shrink-0 px-1"><i class="fa-solid fa-xmark"></i></button>
      </div>

      <div class="flex-1 overflow-y-auto bg-slate-50 px-4 py-4 space-y-2.5">
        <a href="<?= e(url('/')) ?>" class="flex items-center gap-3 bg-white rounded-xl p-4 border border-slate-100">
          <div class="w-11 h-11 rounded-lg bg-[var(--menu-accent-100)] text-gold flex items-center justify-center flex-shrink-0 text-lg"><i class="fa-solid fa-house"></i></div>
          <p class="text-sm font-extrabold text-primary-navy">Home</p>
        </a>
        <?php if (!$authUser): ?>
          <a href="<?= e(url('login')) ?>" class="flex items-center gap-3 bg-white rounded-xl p-4 border border-slate-100">
            <div class="w-11 h-11 rounded-lg bg-[var(--menu-accent-100)] text-gold flex items-center justify-center flex-shrink-0 text-lg"><i class="fa-solid fa-right-to-bracket"></i></div>
            <p class="text-sm font-extrabold text-primary-navy">Login</p>
          </a>
          <a href="<?= e(url('register')) ?>" class="flex items-center gap-3 bg-white rounded-xl p-4 border border-slate-100">
            <div class="w-11 h-11 rounded-lg bg-[var(--menu-accent-100)] text-gold flex items-center justify-center flex-shrink-0 text-lg"><i class="fa-solid fa-user-plus"></i></div>
            <p class="text-sm font-extrabold text-primary-navy">Join Now</p>
          </a>
        <?php endif; ?>

        <?php $mobileAccordion('network', 'directory', 'fa-solid fa-users', 'Alumni Network', 'Connect, collaborate and engage with fellow alumni around the world.', array_filter([
          ['href' => url('directory'), 'icon' => 'fa-solid fa-magnifying-glass', 'label' => 'Search Alumni', 'desc' => 'Find and connect with fellow alumni'],
          $authUser ? ['href' => url('directory?view=saved'), 'icon' => 'fa-solid fa-bookmark', 'label' => 'Saved Alumni', 'desc' => 'Browse your saved fellow alumni'] : null,
          ['href' => url('directory?view=country'), 'icon' => 'fa-solid fa-location-dot', 'label' => 'Alumni Map', 'desc' => 'Explore where alumni are located worldwide'],
          ['href' => url('directory?view=cohort'), 'icon' => 'fa-solid fa-graduation-cap', 'label' => 'Find by Cohort', 'desc' => 'Browse alumni by programme or class year'],
          ['href' => url('businesses'), 'icon' => 'fa-solid fa-store', 'label' => 'Alumni Businesses', 'desc' => 'Discover businesses run by alumni'],
        ])); ?>

        <?php if ($authUser): $mobileAccordion('connections', 'connections', 'fa-solid fa-user-group', 'Connections', 'Grow your professional network.', [
          ['href' => url('connections'), 'icon' => 'fa-solid fa-user-group', 'label' => 'My Connections', 'desc' => 'Keep in touch with your connections'],
          ['href' => url('connections#requests'), 'icon' => 'fa-solid fa-user-plus', 'label' => 'Connection Requests', 'desc' => 'Manage incoming and outgoing requests'],
          ['href' => url('connections#suggested'), 'icon' => 'fa-solid fa-star', 'label' => 'Suggested Connections', 'desc' => 'Alumni you may want to connect with'],
          ['href' => url('connections#following'), 'icon' => 'fa-regular fa-eye', 'label' => 'Following', 'desc' => 'Alumni you are following'],
          ['href' => url('feed'), 'icon' => 'fa-solid fa-rss', 'label' => 'Community Feed', 'desc' => 'Posts and updates from alumni'],
          ['href' => url('messages'), 'icon' => 'fa-solid fa-envelope', 'label' => 'Messages', 'desc' => 'Your private conversations'],
          ['href' => url('mentorship'), 'icon' => 'fa-solid fa-chalkboard-user', 'label' => 'Mentorship', 'desc' => 'Find or become a mentor'],
          ['href' => url('notifications'), 'icon' => 'fa-solid fa-bell', 'label' => 'Notifications', 'desc' => 'Your latest alerts and updates'],
        ]); endif; ?>

        <?php
          $isAdmin = $authUser && $authUser['role'] === 'admin';
          $mobileAccordion('careers', 'jobs', 'fa-solid fa-briefcase', 'Careers', 'Jobs, resources and opportunities.', array_filter([
            ['href' => url('jobs'), 'icon' => 'fa-solid fa-briefcase', 'label' => 'Job Board', 'desc' => 'Browse open roles from alumni and partners'],
            ['href' => url('jobs') . '?tab=search', 'icon' => 'fa-solid fa-magnifying-glass', 'label' => 'Advanced Search', 'desc' => 'Filter jobs by type, location and more'],
            ['href' => url('jobs') . '?tab=companies', 'icon' => 'fa-solid fa-building', 'label' => 'Companies Hiring', 'desc' => 'See which companies are hiring'],
            $authUser ? ['href' => url('jobs') . '?tab=saved', 'icon' => 'fa-solid fa-bookmark', 'label' => 'Saved Jobs', 'desc' => "Jobs you've bookmarked"] : null,
            $authUser ? ['href' => url('jobs') . '?tab=alerts', 'icon' => 'fa-regular fa-bell', 'label' => 'Job Alerts', 'desc' => 'Get notified about new matching jobs'] : null,
            ['href' => url('resources'), 'icon' => 'fa-solid fa-file-lines', 'label' => 'Career Resources', 'desc' => 'Guides, templates and career tools'],
            $isAdmin ? ['href' => url('admin/jobs/create'), 'icon' => 'fa-solid fa-square-plus', 'label' => 'Post a Job', 'desc' => 'Add a new job listing'] : ($authUser ? ['href' => url('jobs/post'), 'icon' => 'fa-solid fa-square-plus', 'label' => 'Post a Job', 'desc' => 'Share an opportunity with alumni'] : null),
            $isAdmin ? ['href' => url('admin/jobs'), 'icon' => 'fa-solid fa-list-check', 'label' => 'Manage Jobs', 'desc' => 'Review and manage job listings'] : ($authUser ? ['href' => url('jobs/mine'), 'icon' => 'fa-solid fa-list-check', 'label' => 'My Job Posts', 'desc' => "Jobs you've posted"] : null),
          ]));

          $mobileAccordion('events', 'events', 'fa-solid fa-calendar-days', 'Events', 'Upcoming events and your registrations.', array_filter([
            ['href' => url('events'), 'icon' => 'fa-solid fa-calendar-days', 'label' => 'All Events', 'desc' => 'Browse upcoming alumni events'],
            $authUser ? ['href' => url('events/mine'), 'icon' => 'fa-solid fa-ticket', 'label' => 'My Events', 'desc' => 'Your registrations, saved events and history'] : null,
            $isAdmin ? ['href' => url('admin/events/create'), 'icon' => 'fa-solid fa-square-plus', 'label' => 'Create Event', 'desc' => 'Add a new alumni event'] : null,
            $isAdmin ? ['href' => url('admin/events'), 'icon' => 'fa-solid fa-list-check', 'label' => 'Manage Events', 'desc' => 'Review and manage all events'] : null,
          ]));

          $mobileAccordion('knowledge', 'knowledge', 'fa-solid fa-book-open', 'Knowledge', 'Articles, research and insights.', array_filter([
            ['href' => url('knowledge'), 'icon' => 'fa-solid fa-book-open', 'label' => 'All Articles', 'desc' => 'Browse the knowledge hub'],
            ['href' => url('knowledge?category=research'), 'icon' => 'fa-regular fa-file-lines', 'label' => 'Research & Publications', 'desc' => 'Alumni research and publications'],
            ['href' => url('resources'), 'icon' => 'fa-solid fa-download', 'label' => 'Resource Library', 'desc' => 'Downloadable guides and templates'],
            $authUser ? ['href' => url('knowledge/submit'), 'icon' => 'fa-solid fa-pen-to-square', 'label' => 'Write an Article', 'desc' => 'Share your expertise'] : null,
          ]));

          $mobileAccordion('more', '', 'fa-solid fa-ellipsis', 'More', 'News & Blog, Alumni Spotlight, Give Back.', array_filter([
            ['href' => url('news'), 'icon' => 'fa-solid fa-newspaper', 'label' => 'News & Blog', 'desc' => 'Latest news from the school'],
            ['href' => url('spotlight'), 'icon' => 'fa-solid fa-star', 'label' => 'Alumni Spotlight', 'desc' => 'Celebrating alumni achievements'],
            ['href' => url('give'), 'icon' => 'fa-solid fa-hand-holding-heart', 'label' => 'Give Back', 'desc' => 'Support scholarships and alumni causes'],
          ]));
        ?>

        <?php if ($authUser): ?>
          <a href="<?= e(url('profile/edit')) ?>" class="flex items-center gap-3 bg-white rounded-xl p-4 border border-slate-100">
            <div class="w-11 h-11 rounded-lg bg-[var(--menu-accent-100)] text-gold flex items-center justify-center flex-shrink-0 text-lg"><i class="fa-solid fa-user"></i></div>
            <p class="text-sm font-extrabold text-primary-navy">Edit Profile</p>
          </a>
          <?php if ($isAdmin): ?>
            <a href="<?= e(url('admin/dashboard')) ?>" class="flex items-center gap-3 bg-white rounded-xl p-4 border border-slate-100">
              <div class="w-11 h-11 rounded-lg bg-[var(--menu-accent-100)] text-gold flex items-center justify-center flex-shrink-0 text-lg"><i class="fa-solid fa-gauge"></i></div>
              <p class="text-sm font-extrabold text-primary-navy">Admin Panel</p>
            </a>
          <?php endif; ?>
          <?php if ($isAdmin || $authUser['role'] === 'editor'): ?>
            <a href="<?= e(url('admin/register-alumni')) ?>" class="flex items-center gap-3 bg-white rounded-xl p-4 border border-slate-100">
              <div class="w-11 h-11 rounded-lg bg-[var(--menu-accent-100)] text-gold flex items-center justify-center flex-shrink-0 text-lg"><i class="fa-solid fa-user-plus"></i></div>
              <p class="text-sm font-extrabold text-primary-navy">Register Alumni</p>
            </a>
          <?php endif; ?>
          <form method="POST" action="<?= e(url('logout')) ?>">
            <?= csrf_field() ?>
            <button type="submit" class="w-full flex items-center gap-3 bg-white rounded-xl p-4 border border-slate-100 text-left">
              <div class="w-11 h-11 rounded-lg bg-slate-100 text-slate-500 flex items-center justify-center flex-shrink-0 text-lg"><i class="fa-solid fa-right-from-bracket"></i></div>
              <p class="text-sm font-extrabold text-primary-navy">Logout</p>
            </button>
          </form>
        <?php endif; ?>
      </div>

      <div class="flex-shrink-0 bg-[var(--menu-accent-50)] border-t border-[var(--menu-accent-100)] px-4 py-4 flex items-center justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
          <div class="w-10 h-10 rounded-full bg-[var(--menu-accent-100)] text-gold flex items-center justify-center flex-shrink-0"><i class="fa-solid fa-user-group"></i></div>
          <div class="min-w-0">
            <p class="text-xs font-extrabold text-primary-navy leading-tight">Grow Your Network. Build Your Future.</p>
            <p class="text-[0.68rem] text-slate-500 mt-0.5">Connect with alumni making an impact worldwide.</p>
          </div>
        </div>
        <a href="<?= e(url('directory')) ?>" class="btn-gold !px-4 !py-2.5 text-xs flex items-center gap-1.5 flex-shrink-0 whitespace-nowrap">View Directory <i class="fa-solid fa-arrow-right"></i></a>
      </div>
    </div>

    <script>
      (function () {
        var overlay = document.getElementById('mobile-menu-overlay');
        var openBtns = [document.getElementById('mobile-menu-open'), document.getElementById('mobile-menu-open-bottom')];
        var closeBtn = document.getElementById('mobile-menu-close');
        if (!overlay) return;

        function openMenu() {
          overlay.classList.remove('hidden');
          overlay.classList.add('flex');
          document.body.style.overflow = 'hidden';
        }
        function closeMenu() {
          overlay.classList.add('hidden');
          overlay.classList.remove('flex');
          document.body.style.overflow = '';
          overlay.querySelectorAll('details[open]').forEach(function (d) { d.removeAttribute('open'); });
        }

        openBtns.forEach(function (btn) {
          if (btn) btn.addEventListener('click', openMenu);
        });
        if (closeBtn) closeBtn.addEventListener('click', closeMenu);

        document.addEventListener('keydown', function (e) {
          if (e.key === 'Escape' && !overlay.classList.contains('hidden')) closeMenu();
        });

        window.addEventListener('resize', function () {
          if (window.innerWidth >= 1024) closeMenu();
        });
      })();
    </script>
  </header>

  <?php require dirname(__DIR__) . '/partials/flash.php'; ?>

  <main class="flex-1">
    <?= $content ?>
  </main>

  <footer class="bg-primary-navy text-white mt-12 py-6">
    <div class="max-w-[1280px] mx-auto px-4 md:px-8 grid grid-cols-2 md:grid-cols-4 gap-6">
      <div class="flex items-center gap-3 md:border-r md:border-white/10">
        <div class="w-11 h-11 rounded-full bg-white/10 flex items-center justify-center text-lg flex-shrink-0"><i class="fa-solid fa-users"></i></div>
        <div>
          <h3 class="text-lg font-extrabold"><?= e($footerSettings['metric_alumni'] ?? '') ?></h3>
          <p class="text-[0.7rem] text-slate-400">Alumni Worldwide</p>
        </div>
      </div>
      <div class="flex items-center gap-3 md:border-r md:border-white/10">
        <div class="w-11 h-11 rounded-full bg-white/10 flex items-center justify-center text-lg flex-shrink-0"><i class="fa-solid fa-building-columns"></i></div>
        <div>
          <h3 class="text-lg font-extrabold"><?= e($footerSettings['metric_countries'] ?? '') ?></h3>
          <p class="text-[0.7rem] text-slate-400">Countries Represented</p>
        </div>
      </div>
      <div class="flex items-center gap-3 md:border-r md:border-white/10">
        <div class="w-11 h-11 rounded-full bg-white/10 flex items-center justify-center text-lg flex-shrink-0"><i class="fa-solid fa-briefcase"></i></div>
        <div>
          <h3 class="text-lg font-extrabold"><?= e($footerSettings['metric_jobs'] ?? '') ?></h3>
          <p class="text-[0.7rem] text-slate-400">Active Job Opportunities</p>
        </div>
      </div>
      <div class="flex items-center gap-3">
        <div class="w-11 h-11 rounded-full bg-white/10 flex items-center justify-center text-lg flex-shrink-0"><i class="fa-solid fa-link"></i></div>
        <div>
          <h3 class="text-lg font-extrabold"><?= e($footerSettings['metric_connections'] ?? '') ?></h3>
          <p class="text-[0.7rem] text-slate-400">Alumni Connections Made</p>
        </div>
      </div>
    </div>
  </footer>

  <nav class="lg:hidden fixed bottom-0 left-0 right-0 h-[60px] bg-white border-t border-slate-200 flex justify-around items-center z-50">
    <a href="<?= e(url('/')) ?>" class="flex flex-col items-center gap-0.5 text-[0.65rem] font-medium <?= $activeNav === 'home' ? 'text-gold font-bold' : 'text-slate-500' ?>"><i class="fa-solid fa-house text-lg"></i>Home</a>
    <a href="<?= e(url('jobs')) ?>" class="flex flex-col items-center gap-0.5 text-[0.65rem] font-medium <?= $activeNav === 'jobs' ? 'text-gold font-bold' : 'text-slate-500' ?>"><i class="fa-solid fa-briefcase text-lg"></i>Jobs</a>
    <a href="<?= e(url('directory')) ?>" class="flex flex-col items-center gap-0.5 text-[0.65rem] font-medium <?= $activeNav === 'directory' ? 'text-gold font-bold' : 'text-slate-500' ?>"><i class="fa-solid fa-user-group text-lg"></i>Network</a>
    <a href="<?= e(url('events')) ?>" class="flex flex-col items-center gap-0.5 text-[0.65rem] font-medium <?= $activeNav === 'events' ? 'text-gold font-bold' : 'text-slate-500' ?>"><i class="fa-solid fa-calendar-days text-lg"></i>Events</a>
    <button type="button" id="mobile-menu-open-bottom" class="flex flex-col items-center gap-0.5 text-[0.65rem] font-medium text-slate-500 cursor-pointer"><i class="fa-solid fa-bars text-lg"></i>More</button>
  </nav>

  <div id="confirm-modal" class="hidden fixed inset-0 z-[100] items-center justify-center bg-slate-900/60 px-4">
    <div class="bg-white rounded-xl shadow-2xl max-w-sm w-full p-6 text-center">
      <div class="w-12 h-12 rounded-full bg-red-50 text-red-500 flex items-center justify-center text-xl mx-auto mb-4"><i class="fa-solid fa-trash-can"></i></div>
      <p id="confirm-modal-message" class="text-sm text-slate-700 mb-6">Are you sure?</p>
      <div class="flex gap-3 justify-center">
        <button type="button" id="confirm-modal-cancel" class="btn bg-slate-100 text-slate-600 hover:bg-slate-200 !px-5 !py-2 text-xs">Cancel</button>
        <button type="button" id="confirm-modal-ok" class="btn bg-red-500 text-white hover:bg-red-600 !px-5 !py-2 text-xs">Delete</button>
      </div>
    </div>
  </div>

  <script>
    (function () {
      var modal = document.getElementById('confirm-modal');
      var message = document.getElementById('confirm-modal-message');
      var okBtn = document.getElementById('confirm-modal-ok');
      var cancelBtn = document.getElementById('confirm-modal-cancel');
      var pendingForm = null;

      function closeModal() {
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        pendingForm = null;
      }

      document.addEventListener('submit', function (e) {
        var form = e.target;
        if (!(form instanceof HTMLFormElement) || !form.hasAttribute('data-confirm')) return;
        if (form.dataset.confirmed === '1') return;
        e.preventDefault();
        pendingForm = form;
        message.textContent = form.getAttribute('data-confirm') || 'Are you sure?';
        modal.classList.remove('hidden');
        modal.classList.add('flex');
      });

      okBtn.addEventListener('click', function () {
        if (pendingForm) {
          pendingForm.dataset.confirmed = '1';
          pendingForm.requestSubmit();
        }
        closeModal();
      });

      cancelBtn.addEventListener('click', closeModal);
      modal.addEventListener('click', function (e) {
        if (e.target === modal) closeModal();
      });
    })();
  </script>

  <script>
    (function () {
      // Grace period (ms) before a hovered dropdown closes after the mouse leaves it.
      // Lower = snappier close, higher = more forgiving of slow mouse movement.
      var CLOSE_DELAY = 120;

      document.querySelectorAll('[data-mega-group]').forEach(function (group) {
        var panel = group.querySelector('[data-mega-panel]');
        var trigger = group.querySelector('.top-pill');
        var box = panel ? panel.querySelector('[data-mega-box]') : null;
        var caret = panel ? panel.querySelector('[data-mega-caret]') : null;
        if (!panel || !box) return;

        var closeTimer = null;

        function positionCaret() {
          if (!caret || !trigger) return;
          var rect = trigger.getBoundingClientRect();
          caret.style.left = (rect.left + rect.width / 2) + 'px';
        }

        function open() {
          clearTimeout(closeTimer);
          positionCaret();
          panel.classList.remove('hidden');
        }

        function scheduleClose() {
          clearTimeout(closeTimer);
          closeTimer = setTimeout(function () {
            panel.classList.add('hidden');
          }, CLOSE_DELAY);
        }

        // Listen on the trigger pill and the visible panel box only (not the
        // full-width transparent wrapper) so leaving the menu in any direction
        // — including sideways, past the edge of the panel — closes it.
        trigger.addEventListener('mouseenter', open);
        trigger.addEventListener('mouseleave', scheduleClose);
        box.addEventListener('mouseenter', open);
        box.addEventListener('mouseleave', scheduleClose);

        // Close immediately on link click. A link to a hash on the current page (e.g.
        // /connections -> /connections#requests) doesn't unload the page, so nothing else
        // would ever close the panel in that case.
        box.addEventListener('click', function (e) {
          if (e.target.closest('a')) {
            clearTimeout(closeTimer);
            panel.classList.add('hidden');
          }
        });
      });
    })();
  </script>

  <?php if (!empty($needsRichEditor)): ?>
    <script src="<?= e(asset('js/admin.js')) ?>"></script>
  <?php endif; ?>

  <?php if ($authUser): ?>
    <script>
      (function () {
        var pollUrl = <?= json_encode(url('notifications/poll')) ?>;
        var badge = document.getElementById('notification-badge');
        if (!badge) return;

        function poll() {
          fetch(pollUrl)
            .then(function (res) { return res.json(); })
            .then(function (data) {
              var count = data.unread || 0;
              if (count > 0) {
                badge.textContent = count > 9 ? '9+' : count;
                badge.classList.remove('hidden');
              } else {
                badge.classList.add('hidden');
              }
            })
            .catch(function () {});
        }

        setInterval(poll, 20000);
      })();
    </script>
  <?php endif; ?>

</body>
</html>
