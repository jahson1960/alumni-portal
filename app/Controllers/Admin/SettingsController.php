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
            'availableFonts' => available_fonts(),
        ]);
    }

    public function update(): void
    {
        $this->requireCsrf();

        $data = [];
        foreach (self::KEYS as $key) {
            $data[$key] = trim((string) $this->input($key, ''));
        }

        $heightDesktop = (int) $this->input('hero_height_desktop', 480);
        $data['hero_height_desktop'] = (string) max(300, min(900, $heightDesktop ?: 480));
        $heightMobile = (int) $this->input('hero_height_mobile', 380);
        $data['hero_height_mobile'] = (string) max(300, min(900, $heightMobile ?: 380));

        $interval = (int) $this->input('hero_interval', 6);
        $data['hero_interval'] = (string) max(3, min(20, $interval ?: 6));

        $transition = (string) $this->input('hero_transition', 'fade');
        $data['hero_transition'] = in_array($transition, self::TRANSITIONS, true) ? $transition : 'fade';

        $data['hero_btn_h_align_desktop'] = valid_align($this->input('hero_btn_h_align_desktop'), ['left', 'center', 'right'], 'left');
        $data['hero_btn_h_align_mobile'] = valid_align($this->input('hero_btn_h_align_mobile'), ['left', 'center', 'right'], 'left');
        $data['hero_btn_v_align_desktop'] = valid_align($this->input('hero_btn_v_align_desktop'), ['top', 'center', 'bottom'], 'bottom');
        $data['hero_btn_v_align_mobile'] = valid_align($this->input('hero_btn_v_align_mobile'), ['top', 'center', 'bottom'], 'bottom');

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

        $data['font_family_desktop'] = valid_font_family($this->input('font_family_desktop'));
        $data['font_family_mobile'] = valid_font_family($this->input('font_family_mobile'));
        $data['font_size_desktop'] = (string) max(12, min(20, (int) $this->input('font_size_desktop', 16) ?: 16));
        $data['font_size_mobile'] = (string) max(12, min(20, (int) $this->input('font_size_mobile', 15) ?: 15));

        foreach ([
            'header_bg_desktop' => '#091a2e',
            'header_bg_mobile' => '#ffffff',
            'header_text_desktop' => '#ffffff',
            'header_text_mobile' => '#091a2e',
            'menu_panel_bg_desktop' => '#ffffff',
            'menu_panel_bg_mobile' => '#091a2e',
            'menu_item_text_desktop' => '#091a2e',
            'menu_item_text_mobile' => '#ffffff',
        ] as $key => $default) {
            $data[$key] = valid_hex_color($this->input($key), $default);
        }

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
