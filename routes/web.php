<?php

use App\Core\Router;

$router = new Router();

$router->get('/', 'HomeController@index');

// Alumni auth
$router->get('/register', 'AuthController@showRegister');
$router->post('/register', 'AuthController@register');
$router->get('/login', 'AuthController@showLogin');
$router->post('/login', 'AuthController@login');
$router->post('/logout', 'AuthController@logout');

// Profile (self-service)
$router->get('/profile/edit', 'ProfileController@edit');
$router->post('/profile/edit', 'ProfileController@update');
$router->post('/profile/education', 'ProfileController@educationStore');
$router->post('/profile/education/{id}', 'ProfileController@educationUpdate');
$router->post('/profile/education/{id}/delete', 'ProfileController@educationDestroy');
$router->post('/profile/experience', 'ProfileController@experienceStore');
$router->post('/profile/experience/{id}', 'ProfileController@experienceUpdate');
$router->post('/profile/experience/{id}/delete', 'ProfileController@experienceDestroy');

// Alumni directory
$router->get('/directory', 'DirectoryController@index');
$router->get('/directory/online-status', 'DirectoryController@onlineStatus');
$router->get('/alumni/{id}', 'DirectoryController@show');
$router->post('/alumni/{id}/save', 'DirectoryController@toggleSave');

// Connections & Follow
$router->get('/connections', 'ConnectionController@index');
$router->get('/connections/poll', 'ConnectionController@poll');
$router->post('/connections/{userId}/request', 'ConnectionController@sendRequest');
$router->post('/connections/{userId}/cancel', 'ConnectionController@cancelRequest');
$router->post('/connections/{connectionId}/accept', 'ConnectionController@accept');
$router->post('/connections/{connectionId}/decline', 'ConnectionController@decline');
$router->post('/connections/{userId}/remove', 'ConnectionController@remove');
$router->post('/connections/{userId}/block', 'ConnectionController@block');
$router->post('/follow/{userId}', 'ConnectionController@follow');
$router->post('/unfollow/{userId}', 'ConnectionController@unfollow');

// Messaging (between accepted connections only)
$router->get('/messages', 'MessageController@index');
$router->get('/messages/{userId}', 'MessageController@show');
$router->post('/messages/{userId}', 'MessageController@store');
$router->get('/messages/{userId}/poll', 'MessageController@poll');

// Community Feed
$router->get('/feed', 'FeedController@index');
$router->get('/feed/drafts', 'FeedController@drafts');
$router->post('/feed/drafts/{id}', 'FeedController@updateDraft');
$router->post('/feed/drafts/{id}/delete', 'FeedController@destroyDraft');
$router->get('/feed/{id}', 'FeedController@show');
$router->post('/feed', 'FeedController@store');
$router->post('/feed/{id}/delete', 'FeedController@destroy');
$router->post('/feed/{id}/like', 'FeedController@like');
$router->post('/feed/{id}/share', 'FeedController@share');
$router->post('/feed/{id}/comment', 'FeedController@comment');
$router->get('/feed/{id}/comments', 'FeedController@commentsFragment');
$router->post('/feed/comments/{commentId}/delete', 'FeedController@deleteComment');

// Jobs
$router->get('/jobs', 'JobController@index');
$router->get('/jobs/search', 'JobController@searchForm');
$router->get('/jobs/saved', 'JobController@saved');
$router->get('/jobs/post', 'JobController@postForm');
$router->post('/jobs/post', 'JobController@postStore');
$router->get('/jobs/mine', 'JobController@mine');
$router->get('/jobs/mine/{id}/edit', 'JobController@editMine');
$router->post('/jobs/mine/{id}', 'JobController@updateMine');
$router->post('/jobs/mine/{id}/delete', 'JobController@deleteMine');

// Job alerts (must be registered before /jobs/{id})
$router->get('/jobs/alerts', 'JobAlertController@index');
$router->get('/jobs/alerts/create', 'JobAlertController@createForm');
$router->post('/jobs/alerts', 'JobAlertController@store');
$router->get('/jobs/alerts/{id}/edit', 'JobAlertController@editForm');
$router->post('/jobs/alerts/{id}', 'JobAlertController@update');
$router->post('/jobs/alerts/{id}/toggle', 'JobAlertController@toggle');
$router->post('/jobs/alerts/{id}/delete', 'JobAlertController@destroy');

$router->post('/jobs/{id}/save', 'JobController@toggleSave');
$router->post('/jobs/{id}/apply', 'JobController@apply');
$router->get('/jobs/{id}', 'JobController@show');

// Companies
$router->get('/companies', 'CompanyController@index');
$router->get('/companies/{id}', 'CompanyController@show');

// Editor image upload (any logged-in user, used by the Knowledge submission rich editor)
$router->post('/editor-upload', 'EditorUploadController@image');

// Notifications
$router->get('/notifications', 'NotificationController@index');
$router->post('/notifications/mark-all-read', 'NotificationController@markAllRead');
$router->get('/notifications/poll', 'NotificationController@poll');

// Benefits
$router->get('/benefits', 'BenefitController@index');

// Giving
$router->get('/give', 'GivingController@index');
$router->post('/give/campaigns/{id}/confirm', 'GivingController@confirm');
$router->get('/give/causes/{slug}', 'GivingController@cause');

// Alumni Spotlight
$router->get('/spotlight', 'SpotlightController@index');

// Alumni Map
$router->get('/map', 'MapController@index');

// Knowledge & Publications
$router->get('/knowledge', 'KnowledgeController@index');
$router->get('/knowledge/submit', 'KnowledgeController@submitForm');
$router->post('/knowledge/submit', 'KnowledgeController@store');
$router->get('/knowledge/{id}', 'KnowledgeController@show');

// Cohorts
$router->get('/cohorts', 'CohortController@index');
$router->get('/cohorts/{program}/{year}', 'CohortController@show');

// Business Directory
$router->get('/businesses', 'BusinessController@index');
$router->get('/businesses/mine', 'BusinessController@mine');
$router->get('/businesses/create', 'BusinessController@create');
$router->post('/businesses', 'BusinessController@store');
$router->get('/businesses/{id}/edit', 'BusinessController@edit');
$router->post('/businesses/{id}', 'BusinessController@update');
$router->post('/businesses/{id}/delete', 'BusinessController@destroy');
$router->post('/businesses/{id}/save', 'BusinessController@toggleSave');
$router->get('/businesses/{id}', 'BusinessController@show');

// Mentorship
$router->get('/mentorship', 'MentorshipController@index');
$router->get('/mentorship/become', 'MentorshipController@becomeForm');
$router->post('/mentorship/become', 'MentorshipController@becomeStore');
$router->get('/mentorship/requests', 'MentorshipController@requests');
$router->post('/mentorship/{mentorId}/request', 'MentorshipController@request');
$router->post('/mentorship/{id}/accept', 'MentorshipController@accept');
$router->post('/mentorship/{id}/decline', 'MentorshipController@decline');

// Events
$router->get('/events', 'EventController@index');
$router->get('/events/mine', 'EventController@mine');
$router->get('/events/{id}', 'EventController@show');
$router->post('/events/{id}/rsvp', 'EventController@rsvp');
$router->post('/events/{id}/save', 'EventController@toggleSave');
$router->get('/events/{id}/ics', 'EventController@ics');

// News
$router->get('/news', 'NewsController@index');
$router->post('/news/{id}/save', 'NewsController@toggleSave');
$router->get('/news/{slug}', 'NewsController@show');

// Resources
$router->get('/resources', 'ResourceController@index');
$router->post('/resources/{id}/save', 'ResourceController@toggleSave');
$router->post('/resources/resume-review', 'ResumeReviewController@store');
$router->post('/resources/resume-review/{id}/claim', 'ResumeReviewController@claim');
$router->post('/resources/resume-review/{id}/feedback', 'ResumeReviewController@feedback');
$router->get('/resources/{id}', 'ResourceController@show');

// Admin auth
$router->get('/admin/login', 'Admin\\AdminAuthController@showLogin');
$router->post('/admin/login', 'Admin\\AdminAuthController@login');
$router->post('/admin/logout', 'Admin\\AdminAuthController@logout');

// Admin dashboard
$router->get('/admin', 'Admin\\DashboardController@index');
$router->get('/admin/dashboard', 'Admin\\DashboardController@index');

// Admin settings (hero / CTA / metrics)
$router->get('/admin/settings', 'Admin\\SettingsController@edit');
$router->post('/admin/settings', 'Admin\\SettingsController@update');
$router->get('/admin/page-visibility', 'Admin\\PageVisibilityController@edit');
$router->post('/admin/page-visibility', 'Admin\\PageVisibilityController@update');

// Admin: news
$router->get('/admin/news', 'Admin\\NewsController@index');
$router->get('/admin/news/create', 'Admin\\NewsController@create');
$router->post('/admin/news', 'Admin\\NewsController@store');
$router->get('/admin/news/{id}/edit', 'Admin\\NewsController@edit');
$router->post('/admin/news/{id}', 'Admin\\NewsController@update');
$router->post('/admin/news/{id}/delete', 'Admin\\NewsController@destroy');

// Admin: jobs
$router->get('/admin/jobs', 'Admin\\JobController@index');
$router->get('/admin/jobs/pending', 'Admin\\JobController@pending');
$router->get('/admin/jobs/create', 'Admin\\JobController@create');
$router->post('/admin/jobs', 'Admin\\JobController@store');
$router->get('/admin/jobs/{id}/edit', 'Admin\\JobController@edit');
$router->post('/admin/jobs/{id}', 'Admin\\JobController@update');
$router->post('/admin/jobs/{id}/delete', 'Admin\\JobController@destroy');
$router->post('/admin/jobs/{id}/approve', 'Admin\\JobController@approve');
$router->post('/admin/jobs/{id}/reject', 'Admin\\JobController@reject');

// Admin: events
$router->get('/admin/events', 'Admin\\EventController@index');
$router->get('/admin/events/create', 'Admin\\EventController@create');
$router->post('/admin/events', 'Admin\\EventController@store');
$router->get('/admin/events/{id}/edit', 'Admin\\EventController@edit');
$router->post('/admin/events/{id}', 'Admin\\EventController@update');
$router->post('/admin/events/{id}/delete', 'Admin\\EventController@destroy');

// Admin: resources
$router->get('/admin/resources', 'Admin\\ResourceController@index');
$router->get('/admin/resources/create', 'Admin\\ResourceController@create');
$router->post('/admin/resources', 'Admin\\ResourceController@store');
$router->get('/admin/resources/{id}/edit', 'Admin\\ResourceController@edit');
$router->post('/admin/resources/{id}', 'Admin\\ResourceController@update');
$router->post('/admin/resources/{id}/delete', 'Admin\\ResourceController@destroy');

// Admin: benefits
$router->get('/admin/benefits', 'Admin\\BenefitController@index');
$router->get('/admin/benefits/create', 'Admin\\BenefitController@create');
$router->post('/admin/benefits', 'Admin\\BenefitController@store');
$router->get('/admin/benefits/{id}/edit', 'Admin\\BenefitController@edit');
$router->post('/admin/benefits/{id}', 'Admin\\BenefitController@update');
$router->post('/admin/benefits/{id}/delete', 'Admin\\BenefitController@destroy');

// Admin: giving campaigns
$router->get('/admin/campaigns', 'Admin\\CampaignController@index');
$router->get('/admin/campaigns/create', 'Admin\\CampaignController@create');
$router->post('/admin/campaigns', 'Admin\\CampaignController@store');
$router->get('/admin/campaigns/{id}/edit', 'Admin\\CampaignController@edit');
$router->post('/admin/campaigns/{id}', 'Admin\\CampaignController@update');
$router->post('/admin/campaigns/{id}/delete', 'Admin\\CampaignController@destroy');

$router->get('/admin/donation-methods', 'Admin\\DonationMethodController@index');
$router->get('/admin/donation-methods/create', 'Admin\\DonationMethodController@create');
$router->post('/admin/donation-methods', 'Admin\\DonationMethodController@store');
$router->get('/admin/donation-methods/{id}/edit', 'Admin\\DonationMethodController@edit');
$router->post('/admin/donation-methods/{id}', 'Admin\\DonationMethodController@update');
$router->post('/admin/donation-methods/{id}/delete', 'Admin\\DonationMethodController@destroy');

$router->get('/admin/donation-confirmations', 'Admin\\DonationConfirmationController@index');

// Admin: giving causes
$router->get('/admin/giving-causes', 'Admin\\GivingCauseController@index');
$router->get('/admin/giving-causes/create', 'Admin\\GivingCauseController@create');
$router->post('/admin/giving-causes', 'Admin\\GivingCauseController@store');
$router->get('/admin/giving-causes/{id}/edit', 'Admin\\GivingCauseController@edit');
$router->post('/admin/giving-causes/{id}', 'Admin\\GivingCauseController@update');
$router->post('/admin/giving-causes/{id}/delete', 'Admin\\GivingCauseController@destroy');

// Admin: article review (Knowledge/Publications)
$router->get('/admin/articles', 'Admin\\ArticleController@index');
$router->post('/admin/articles/{id}/publish', 'Admin\\ArticleController@publish');
$router->post('/admin/articles/{id}/delete', 'Admin\\ArticleController@destroy');

// Admin: hero slides
$router->get('/admin/hero-slides', 'Admin\\HeroSlideController@index');
$router->get('/admin/hero-slides/create', 'Admin\\HeroSlideController@create');
$router->post('/admin/hero-slides', 'Admin\\HeroSlideController@store');
$router->get('/admin/hero-slides/{id}/edit', 'Admin\\HeroSlideController@edit');
$router->post('/admin/hero-slides/{id}', 'Admin\\HeroSlideController@update');
$router->post('/admin/hero-slides/{id}/delete', 'Admin\\HeroSlideController@destroy');
$router->post('/admin/hero-slides/{id}/toggle', 'Admin\\HeroSlideController@toggle');

// Admin: categories
$router->get('/admin/categories', 'Admin\\CategoryController@index');
$router->post('/admin/categories', 'Admin\\CategoryController@store');
$router->get('/admin/categories/{id}/edit', 'Admin\\CategoryController@edit');
$router->post('/admin/categories/{id}', 'Admin\\CategoryController@update');
$router->post('/admin/categories/{id}/delete', 'Admin\\CategoryController@destroy');

// Admin: companies
$router->get('/admin/companies', 'Admin\\CompanyController@index');
$router->get('/admin/companies/create', 'Admin\\CompanyController@create');
$router->post('/admin/companies', 'Admin\\CompanyController@store');
$router->get('/admin/companies/{id}/edit', 'Admin\\CompanyController@edit');
$router->post('/admin/companies/{id}', 'Admin\\CompanyController@update');
$router->post('/admin/companies/{id}/delete', 'Admin\\CompanyController@destroy');

// Admin: rich text editor image uploads (AJAX)
$router->post('/admin/upload/editor-image', 'Admin\\UploadController@editorImage');

// Admin: alumni management
$router->get('/admin/alumni', 'Admin\\AlumniController@index');
$router->get('/admin/alumni/{id}', 'Admin\\AlumniController@show');
$router->post('/admin/alumni/{id}/suspend', 'Admin\\AlumniController@suspend');
$router->post('/admin/alumni/{id}/activate', 'Admin\\AlumniController@activate');
$router->post('/admin/alumni/{id}/delete', 'Admin\\AlumniController@destroy');
$router->post('/admin/alumni/{id}/spotlight', 'Admin\\AlumniController@spotlight');
$router->post('/admin/alumni/{id}/unspotlight', 'Admin\\AlumniController@unspotlight');
$router->post('/admin/alumni/{id}/role', 'Admin\\AlumniController@setRole');

$router->get('/admin/alumni-roster', 'Admin\\AlumniRosterController@index');
$router->get('/admin/alumni-roster/create', 'Admin\\AlumniRosterController@create');
$router->post('/admin/alumni-roster', 'Admin\\AlumniRosterController@store');
$router->get('/admin/alumni-roster/{id}/edit', 'Admin\\AlumniRosterController@edit');
$router->post('/admin/alumni-roster/{id}', 'Admin\\AlumniRosterController@update');
$router->post('/admin/alumni-roster/{id}/delete', 'Admin\\AlumniRosterController@destroy');

// Register Alumni — the one admin-area capability editors are granted, alongside admins.
$router->get('/admin/register-alumni', 'Admin\\AlumniRegistrationController@create');
$router->post('/admin/register-alumni', 'Admin\\AlumniRegistrationController@store');
$router->get('/admin/register-alumni/success', 'Admin\\AlumniRegistrationController@success');

return $router;
