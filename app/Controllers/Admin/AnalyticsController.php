<?php

namespace App\Controllers\Admin;

use App\Models\PageView;

class AnalyticsController extends AdminController
{
    private const RANGES = ['today', '7d', '30d', 'all'];

    public function index(): void
    {
        $range = in_array($this->input('range'), self::RANGES, true) ? $this->input('range') : '7d';
        $since = $this->sinceFor($range);

        $this->view('admin.analytics.index', [
            'title' => 'Analytics',
            'activeNav' => 'analytics',
            'range' => $range,
            'uniqueVisitors' => PageView::countUniqueVisitors($since),
            'pageViews' => PageView::countViews($since),
            'avgTimeOnPage' => PageView::averageDurationSeconds($since),
            'avgTimeOnSite' => PageView::averageSessionSeconds($since),
            'dailyCounts' => PageView::dailyCounts($since),
            'topPages' => PageView::topPages($since, 10),
        ]);
    }

    private function sinceFor(string $range): string
    {
        return match ($range) {
            'today' => date('Y-m-d 00:00:00'),
            '30d' => date('Y-m-d 00:00:00', strtotime('-29 days')),
            'all' => '1970-01-01 00:00:00',
            default => date('Y-m-d 00:00:00', strtotime('-6 days')), // '7d'
        };
    }
}
