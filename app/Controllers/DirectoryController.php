<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Connection;
use App\Models\Follow;
use App\Models\SavedAlumni;
use App\Models\User;

class DirectoryController extends Controller
{
    private const FILTER_KEYS = ['q', 'skills', 'program', 'graduation_year', 'industry', 'country', 'city', 'company'];
    private const SORTS = ['recent', 'name_asc', 'name_desc'];
    private const GROUP_SORTS = ['most', 'fewest', 'name_asc'];
    private const PER_PAGE_OPTIONS = [12, 24, 48];
    private const VIEWS = ['all', 'programme', 'country', 'cohort', 'saved'];

    public function index(): void
    {
        $this->requireVisibility('directory');

        $filters = [];
        foreach (self::FILTER_KEYS as $key) {
            $value = trim((string) $this->input($key, ''));
            if ($value !== '') {
                $filters[$key] = $value;
            }
        }

        $view = in_array($this->input('view'), self::VIEWS, true) ? $this->input('view') : 'all';
        $perPageInput = (int) $this->input('per_page', 12);
        $perPage = in_array($perPageInput, self::PER_PAGE_OPTIONS, true) ? $perPageInput : 12;
        $page = max(1, (int) $this->input('page', 1));

        $viewerLoggedIn = Auth::check();
        $total = User::countAlumniDirectory($filters, $viewerLoggedIn);
        $savedIds = $viewerLoggedIn ? SavedAlumni::savedIdsFor((int) Auth::id()) : [];

        $groups = [];
        $mapCounts = [];

        // A "View Alumni" link drilled in from a grouped tab (Programme/Country/Cohort) keeps that
        // tab's own filter set instead of switching to the "all" view, so the origin tab stays
        // highlighted and the page shows the filtered individual list rather than jumping to "All Alumni".
        $isDrilledIn = match ($view) {
            'programme' => isset($filters['program']),
            'country' => isset($filters['country']),
            'cohort' => isset($filters['program']) && isset($filters['graduation_year']),
            default => false,
        };

        if ($view === 'saved') {
            $sort = in_array($this->input('sort'), self::SORTS, true) ? $this->input('sort') : 'name_asc';
            $savedTotal = User::countAlumniDirectory($filters, $viewerLoggedIn, $savedIds);
            $totalPages = max(1, (int) ceil($savedTotal / $perPage));
            $page = min($page, $totalPages);
            $alumni = User::alumniDirectory($filters, $viewerLoggedIn, $sort, $perPage, ($page - 1) * $perPage, $savedIds);
        } elseif ($view === 'all' || $isDrilledIn) {
            $sort = in_array($this->input('sort'), self::SORTS, true) ? $this->input('sort') : 'recent';
            $totalPages = max(1, (int) ceil($total / $perPage));
            $page = min($page, $totalPages);
            $alumni = User::alumniDirectory($filters, $viewerLoggedIn, $sort, $perPage, ($page - 1) * $perPage);
        } else {
            $sort = in_array($this->input('sort'), self::GROUP_SORTS, true) ? $this->input('sort') : 'most';
            $groupField = $view === 'programme' ? 'program' : ($view === 'country' ? 'country' : null);

            $counts = [];
            $cohortMeta = [];
            foreach (User::alumniDirectory($filters, $viewerLoggedIn) as $person) {
                if ($view === 'cohort') {
                    $program = trim((string) ($person['program'] ?? ''));
                    $year = trim((string) ($person['graduation_year'] ?? ''));
                    if ($program === '' || $year === '') {
                        continue;
                    }
                    $key = $program . '|' . $year;
                    $cohortMeta[$key] = ['program' => $program, 'year' => $year];
                } else {
                    $key = trim((string) ($person[$groupField] ?? ''));
                    if ($key === '') {
                        continue;
                    }
                }
                $counts[$key] = ($counts[$key] ?? 0) + 1;
            }

            if ($view === 'country') {
                foreach ($counts as $countryName => $count) {
                    $iso = country_iso2($countryName);
                    if ($iso !== null) {
                        $mapCounts[$iso] = $count;
                    }
                }
            }

            foreach ($counts as $key => $count) {
                if ($view === 'cohort') {
                    $groups[] = [
                        'label' => $cohortMeta[$key]['program'] . ' — ' . $cohortMeta[$key]['year'],
                        'count' => $count,
                        'program' => $cohortMeta[$key]['program'],
                        'year' => $cohortMeta[$key]['year'],
                    ];
                } else {
                    $groups[] = ['label' => $key, 'count' => $count];
                }
            }

            usort($groups, function ($a, $b) use ($sort) {
                return match ($sort) {
                    'fewest' => $a['count'] <=> $b['count'],
                    'name_asc' => strcasecmp($a['label'], $b['label']),
                    default => $b['count'] <=> $a['count'],
                };
            });

            $totalGroups = count($groups);
            $totalPages = max(1, (int) ceil($totalGroups / $perPage));
            $page = min($page, $totalPages);
            $groups = array_slice($groups, ($page - 1) * $perPage, $perPage);
            $alumni = [];
        }

        $this->view('directory.index', [
            'title' => 'Alumni Directory',
            'activeNav' => 'directory',
            'alumni' => $alumni,
            'groups' => $groups,
            'mapCounts' => $mapCounts,
            'total' => $total,
            'page' => $page,
            'totalPages' => $totalPages,
            'perPage' => $perPage,
            'sort' => $sort,
            'view' => $view,
            'isDrilledIn' => $isDrilledIn,
            'savedIds' => $savedIds,
            'filters' => $filters,
            'programOptions' => User::distinctFilterValues('program'),
            'yearOptions' => User::distinctFilterValues('graduation_year'),
            'industryOptions' => User::distinctFilterValues('industry'),
            'countryOptions' => User::distinctFilterValues('country'),
            'cityOptions' => User::distinctFilterValues('city'),
            'companyOptions' => User::distinctFilterValues('company'),
        ]);
    }

    /** JSON polling endpoint: {id: bool} online status for a batch of user ids, used to refresh online dots live. */
    public function onlineStatus(): void
    {
        $ids = array_values(array_unique(array_filter(
            array_map('intval', explode(',', (string) $this->input('ids', '')))
        )));
        $ids = array_slice($ids, 0, 100);

        $lastActive = User::lastActiveByIds($ids);
        $result = [];
        foreach ($ids as $id) {
            $result[$id] = User::isOnline($lastActive[$id] ?? null);
        }

        header('Content-Type: application/json');
        echo json_encode($result);
    }

    public function toggleSave(string $id): void
    {
        $this->requireCsrf();
        if (!Auth::check()) {
            $this->redirect('login');
        }

        $saved = SavedAlumni::toggle((int) Auth::id(), (int) $id);

        if (($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest') {
            header('Content-Type: application/json');
            echo json_encode(['saved' => $saved]);
            return;
        }

        $this->redirectBack('directory');
    }

    public function show(string $id): void
    {
        $isOwnerOrAdmin = Auth::isAdmin() || Auth::id() === (int) $id;
        $profile = User::publicProfile((int) $id, $isOwnerOrAdmin, Auth::check());
        if (!$profile) {
            http_response_code(404);
            (new ErrorController())->notFound();
            return;
        }

        $viewerId = Auth::id();
        $connectionStatus = null;
        $isFollowing = false;
        if ($viewerId && !$isOwnerOrAdmin) {
            $connectionStatus = Connection::statusBetween((int) $viewerId, (int) $id);
            $isFollowing = Follow::isFollowing((int) $viewerId, (int) $id);
        }

        [$backUrl, $backLabel] = $this->smartBack(url('directory'), 'Back to Directory');

        $this->view('directory.show', [
            'title' => $profile['name'] . ' - Alumni Profile',
            'activeNav' => 'directory',
            'profile' => $profile,
            'isOwnerOrAdmin' => $isOwnerOrAdmin,
            'connectionStatus' => $connectionStatus,
            'isFollowing' => $isFollowing,
            'viewerId' => $viewerId,
            'backUrl' => $backUrl,
            'backLabel' => $backLabel,
        ]);
    }
}
