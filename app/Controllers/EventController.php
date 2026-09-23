<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Event;
use App\Models\EventRsvp;
use App\Models\PageTabVisibility;
use App\Models\SavedEvent;

class EventController extends Controller
{
    private const VIEWS = ['all', 'global', 'reunions', 'executive', 'webinars', 'featured'];

    private const VIEW_CATEGORY_MAP = [
        'global' => 'Global Events',
        'reunions' => 'Reunions',
        'executive' => 'Executive Programmes',
        'webinars' => 'Webinars',
    ];

    public function index(): void
    {
        $this->requireVisibility('events');

        $view = in_array($this->input('view'), self::VIEWS, true) ? $this->input('view') : 'all';
        if (!PageTabVisibility::isVisible('events', $view)) {
            $fallback = PageTabVisibility::firstVisibleTab('events', self::VIEWS);
            if ($fallback === null) {
                http_response_code(404);
                (new ErrorController())->notFound();
                return;
            }
            $view = $fallback;
        }
        $format = in_array($this->input('format'), ['virtual', 'in-person'], true) ? $this->input('format') : '';
        $q = trim((string) $this->input('q', ''));
        $display = $this->input('display') === 'calendar' ? 'calendar' : 'grid';

        $filters = array_filter([
            'q' => $q,
            'category' => self::VIEW_CATEGORY_MAP[$view] ?? '',
            'featured' => $view === 'featured' ? '1' : '',
            'format' => $format,
        ], fn ($v) => $v !== '');

        $viewerId = Auth::id();
        $data = [
            'title' => 'Upcoming Events',
            'activeNav' => 'events',
            'view' => $view,
            'format' => $format,
            'q' => $q,
            'display' => $display,
            'savedEventIds' => $viewerId ? SavedEvent::savedIdsFor((int) $viewerId) : [],
            'attendingEventIds' => $viewerId ? EventRsvp::attendingIdsFor((int) $viewerId) : [],
        ];

        if ($display === 'calendar') {
            $month = (string) $this->input('month', '');
            if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
                $month = date('Y-m');
                if (empty(Event::forMonth($month, $filters))) {
                    $upcoming = Event::search($filters, 1);
                    if ($upcoming) {
                        $month = date('Y-m', strtotime($upcoming[0]['event_date']));
                    }
                }
            }
            $events = Event::forMonth($month, $filters);
            $data += [
                'month' => $month,
                'prevMonth' => date('Y-m', strtotime($month . '-01 -1 month')),
                'nextMonth' => date('Y-m', strtotime($month . '-01 +1 month')),
                'events' => $events,
                'eventsByDay' => $this->groupByDay($events),
            ];
        } else {
            $perPage = max(9, (int) $this->input('per_page', 9));
            $data += [
                'events' => Event::search($filters, $perPage),
                'totalCount' => Event::countSearch($filters),
                'perPage' => $perPage,
            ];
        }

        $previews = [];
        foreach ($data['events'] as $event) {
            $previews[$event['id']] = [
                'attendees' => EventRsvp::attendeesPreview((int) $event['id']),
                'count' => EventRsvp::count((int) $event['id']),
            ];
        }
        $data['attendeePreviews'] = $previews;

        $this->view('events.index', $data);
    }

    public function toggleSave(string $id): void
    {
        $this->requireCsrf();
        if (!Auth::check()) {
            $this->redirect('login');
        }
        SavedEvent::toggle((int) Auth::id(), (int) $id);
        $this->redirectBack('events');
    }

    /** @return array<int,array> day-of-month => events on that day, for the calendar view. */
    private function groupByDay(array $events): array
    {
        $byDay = [];
        foreach ($events as $event) {
            $day = (int) date('j', strtotime($event['event_date']));
            $byDay[$day][] = $event;
        }
        return $byDay;
    }

    private const MINE_TABS = ['registrations', 'saved', 'history'];

    public function mine(): void
    {
        if (!Auth::check()) {
            $this->redirect('login');
        }

        $userId = (int) Auth::id();
        $tab = in_array($this->input('tab'), self::MINE_TABS, true) ? $this->input('tab') : 'registrations';
        if (!PageTabVisibility::isVisible('events_mine', $tab)) {
            $fallback = PageTabVisibility::firstVisibleTab('events_mine', self::MINE_TABS);
            if ($fallback === null) {
                http_response_code(404);
                (new ErrorController())->notFound();
                return;
            }
            $tab = $fallback;
        }
        $today = date('Y-m-d');

        $registered = EventRsvp::myEvents($userId);
        $pastRegistered = array_values(array_filter($registered, fn ($e) => $e['event_date'] < $today));
        $savedEventIds = SavedEvent::savedIdsFor($userId);

        $data = [
            'title' => 'My Events',
            'activeNav' => 'events',
            'tab' => $tab,
            'savedEventIds' => $savedEventIds,
            'attendingEventIds' => EventRsvp::attendingIdsFor($userId),
            'registrationsCount' => count($registered),
            'savedCount' => count($savedEventIds),
            'historyCount' => count($pastRegistered),
        ];

        if ($tab === 'saved') {
            $data['events'] = Event::savedByUser($userId);
        } elseif ($tab === 'history') {
            $data['events'] = $pastRegistered;
        } else {
            $data['upcoming'] = array_values(array_filter($registered, fn ($e) => $e['event_date'] >= $today));
            $data['past'] = $pastRegistered;
        }

        $this->view('events.mine', $data);
    }

    public function show(string $id): void
    {
        $event = Event::find((int) $id);
        if (!$event) {
            http_response_code(404);
            (new ErrorController())->notFound();
            return;
        }

        $this->view('events.show', [
            'title' => $event['title'],
            'activeNav' => 'events',
            'event' => $event,
            'attendees' => EventRsvp::attendees((int) $id),
            'isAttending' => Auth::check() ? EventRsvp::isAttending((int) $id, (int) Auth::id()) : false,
        ]);
    }

    public function rsvp(string $id): void
    {
        $this->requireCsrf();
        if (!Auth::check()) {
            $this->redirect('login');
        }

        EventRsvp::toggle((int) $id, (int) Auth::id());
        $this->redirectBack("events/{$id}");
    }

    public function ics(string $id): void
    {
        $event = Event::find((int) $id);
        if (!$event) {
            http_response_code(404);
            (new ErrorController())->notFound();
            return;
        }

        [$dtStart, $dtEnd, $allDay] = self::icsDateTime($event['event_date'], $event['event_time']);
        $uid = 'event-' . $event['id'] . '@' . ($_SERVER['HTTP_HOST'] ?? 'alumni-portal.local');

        $lines = [
            'BEGIN:VCALENDAR',
            'VERSION:2.0',
            'PRODID:-//Rome Business School Nigeria//Alumni Portal//EN',
            'BEGIN:VEVENT',
            'UID:' . $uid,
            'DTSTAMP:' . gmdate('Ymd\THis\Z'),
            $allDay ? 'DTSTART;VALUE=DATE:' . $dtStart : 'DTSTART:' . $dtStart,
            $allDay ? 'DTEND;VALUE=DATE:' . $dtEnd : 'DTEND:' . $dtEnd,
            'SUMMARY:' . self::icsEscape($event['title']),
            'DESCRIPTION:' . self::icsEscape((string) $event['description']),
            'LOCATION:' . self::icsEscape($event['is_virtual'] ? 'Virtual' : (string) $event['location']),
            'END:VEVENT',
            'END:VCALENDAR',
        ];

        $filename = preg_replace('/[^a-z0-9]+/i', '-', $event['title']) . '.ics';
        header('Content-Type: text/calendar; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        echo implode("\r\n", $lines);
    }

    /** @return array{0:string,1:string,2:bool} [dtStart, dtEnd, isAllDay] */
    private static function icsDateTime(string $eventDate, ?string $eventTime): array
    {
        if ($eventTime && preg_match('/(\d{1,2}):(\d{2})\s*(AM|PM)/i', $eventTime, $m)) {
            $hour = ((int) $m[1]) % 12 + (strtoupper($m[3]) === 'PM' ? 12 : 0);
            $start = new \DateTime($eventDate . ' ' . sprintf('%02d:%02d', $hour, (int) $m[2]));
            $end = (clone $start)->modify('+2 hours');
            return [$start->format('Ymd\THis'), $end->format('Ymd\THis'), false];
        }
        $start = new \DateTime($eventDate);
        $end = (clone $start)->modify('+1 day');
        return [$start->format('Ymd'), $end->format('Ymd'), true];
    }

    private static function icsEscape(string $text): string
    {
        return str_replace(["\\", ",", ";", "\n"], ["\\\\", "\\,", "\\;", "\\n"], $text);
    }
}
