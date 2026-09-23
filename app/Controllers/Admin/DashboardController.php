<?php

namespace App\Controllers\Admin;

use App\Models\Event;
use App\Models\Job;
use App\Models\NewsPost;
use App\Models\Resource;
use App\Models\User;

class DashboardController extends AdminController
{
    public function index(): void
    {
        $this->view('admin.dashboard.index', [
            'title' => 'Dashboard',
            'activeNav' => 'dashboard',
            'counts' => [
                'alumni' => User::countAlumni(),
                'jobs' => Job::countOpen(),
                'news' => count(NewsPost::all()),
                'events' => count(Event::all()),
                'resources' => count(Resource::all()),
            ],
            'recentAlumni' => array_slice(User::allAlumniAdmin(), 0, 5),
        ]);
    }
}
