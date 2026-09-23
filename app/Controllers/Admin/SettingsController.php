<?php

namespace App\Controllers\Admin;

use App\Core\Upload;
use App\Models\Setting;

class SettingsController extends AdminController
{
    private const KEYS = [
        'site_name',
        'cta_subtitle', 'cta_title',
        'metric_alumni', 'metric_countries', 'metric_jobs', 'metric_connections', 'metric_events',
        'metric_scholarships', 'metric_mentors_engaged', 'metric_projects_funded',
        'giving_contact_email',
    ];

    private const TRANSITIONS = ['fade', 'slide', 'slide-vertical', 'zoom', 'flip', 'wipe'];

    public function edit(): void
    {
        $this->view('admin.settings.edit', [
            'title' => 'Site Settings',
            'activeNav' => 'settings',
            'settings' => Setting::all(),
        ]);
    }

    public function update(): void
    {
        $this->requireCsrf();

        $data = [];
        foreach (self::KEYS as $key) {
            $data[$key] = trim((string) $this->input($key, ''));
        }

        $height = (int) $this->input('hero_height', 480);
        $data['hero_height'] = (string) max(300, min(900, $height ?: 480));

        $interval = (int) $this->input('hero_interval', 6);
        $data['hero_interval'] = (string) max(3, min(20, $interval ?: 6));

        $transition = (string) $this->input('hero_transition', 'fade');
        $data['hero_transition'] = in_array($transition, self::TRANSITIONS, true) ? $transition : 'fade';

        foreach (['home_news_count', 'home_jobs_count', 'home_events_count'] as $countKey) {
            $count = (int) $this->input($countKey, 4);
            $data[$countKey] = (string) max(1, min(12, $count ?: 4));
        }

        $data['job_posting_mode'] = $this->input('job_posting_mode') === 'direct' ? 'direct' : 'approval';
        $data['require_login_to_apply'] = $this->input('require_login_to_apply') ? '1' : '0';

        $data['theme_mode'] = $this->input('theme_mode') === 'custom' ? 'custom' : 'unified';

        foreach ([
            'theme_accent_color' => 'theme_accent_color',
            'menu_accent_color' => 'menu_accent_color',
            'theme_button_color' => 'theme_button_color',
            'theme_map_color' => 'theme_map_color',
        ] as $inputKey => $settingKey) {
            $color = trim((string) $this->input($inputKey, '#d49326'));
            $data[$settingKey] = preg_match('/^#[0-9a-fA-F]{6}$/', $color) ? $color : '#d49326';
        }

        $headerHeight = (int) $this->input('header_height', 64);
        $data['header_height'] = (string) max(48, min(120, $headerHeight ?: 64));

        $dropdownOffset = (int) $this->input('dropdown_offset', 4);
        $data['dropdown_offset'] = (string) max(-20, min(40, $dropdownOffset));

        $errors = [];
        $favicon = null;
        try {
            $uploaded = Upload::image($this->file('favicon'), 'branding');
            if ($uploaded) {
                $favicon = upload_url($uploaded);
            }
        } catch (\RuntimeException $e) {
            $errors[] = $e->getMessage();
        }

        $jobsBanner = null;
        try {
            $uploaded = Upload::image($this->file('jobs_banner_image'), 'branding');
            if ($uploaded) {
                $jobsBanner = upload_url($uploaded);
            }
        } catch (\RuntimeException $e) {
            $errors[] = $e->getMessage();
        }

        $jobsExploreImage = null;
        try {
            $uploaded = Upload::image($this->file('jobs_explore_image'), 'branding');
            if ($uploaded) {
                $jobsExploreImage = upload_url($uploaded);
            }
        } catch (\RuntimeException $e) {
            $errors[] = $e->getMessage();
        }

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->redirect('admin/settings');
        }

        if ($favicon) {
            $data['site_favicon'] = $favicon;
        }

        if ($jobsBanner) {
            $data['jobs_banner_image'] = $jobsBanner;
        } else {
            $jobsBannerUrl = trim((string) $this->input('jobs_banner_image_url', ''));
            if ($jobsBannerUrl !== '') {
                $data['jobs_banner_image'] = $jobsBannerUrl;
            }
        }

        if ($jobsExploreImage) {
            $data['jobs_explore_image'] = $jobsExploreImage;
        } else {
            $jobsExploreImageUrl = trim((string) $this->input('jobs_explore_image_url', ''));
            if ($jobsExploreImageUrl !== '') {
                $data['jobs_explore_image'] = $jobsExploreImageUrl;
            }
        }

        Setting::setMany($data);
        $this->flash('success', 'Site settings updated.');
        $this->redirect('admin/settings');
    }
}
