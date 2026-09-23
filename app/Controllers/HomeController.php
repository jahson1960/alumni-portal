<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Event;
use App\Models\HeroSlide;
use App\Models\Job;
use App\Models\NewsPost;
use App\Models\Setting;

class HomeController extends Controller
{
    public function index(): void
    {
        $settings = Setting::all();

        $newsCount = max(1, (int) ($settings['home_news_count'] ?? 4));
        $jobsCount = max(1, (int) ($settings['home_jobs_count'] ?? 3));
        $eventsCount = max(1, (int) ($settings['home_events_count'] ?? 3));

        $news = NewsPost::published($newsCount);
        $featuredNews = array_shift($news);

        $this->view('home.index', [
            'title' => Setting::get('site_name', 'Rome Business School Nigeria - Alumni Network'),
            'activeNav' => 'home',
            'settings' => $settings,
            'heroSlides' => HeroSlide::enabledOrdered(),
            'featuredNews' => $featuredNews,
            'newsList' => $news,
            'jobs' => Job::open($jobsCount),
            'events' => Event::upcoming($eventsCount),
        ]);
    }
}
