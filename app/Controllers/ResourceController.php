<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\PageTabVisibility;
use App\Models\Resource;
use App\Models\ResumeReview;
use App\Models\SavedResource;

class ResourceController extends Controller
{
    private const TABS = ['career', 'resume-review', 'interview-prep'];

    public function index(): void
    {
        $this->requireVisibility('resources');

        $tab = in_array($this->input('tab'), self::TABS, true) ? $this->input('tab') : 'career';
        if (!PageTabVisibility::isVisible('resources', $tab)) {
            $fallback = PageTabVisibility::firstVisibleTab('resources', self::TABS);
            if ($fallback === null) {
                http_response_code(404);
                (new ErrorController())->notFound();
                return;
            }
            $tab = $fallback;
        }
        if ($tab === 'resume-review' && !Auth::check()) {
            $this->redirect('login');
        }

        $data = ['title' => 'Career Resources', 'activeNav' => 'resources', 'tab' => $tab];
        $data += match ($tab) {
            'resume-review' => $this->resumeReviewTabData(),
            'interview-prep' => $this->resourceListTabData(Resource::INTERVIEW_CATEGORIES),
            default => $this->resourceListTabData(Resource::CAREER_CATEGORIES),
        };

        $this->view('resources.index', $data);
    }

    public function show(string $id): void
    {
        $resource = Resource::find((int) $id);
        if (!$resource) {
            http_response_code(404);
            (new ErrorController())->notFound();
            return;
        }

        $viewerId = Auth::id();
        [$backUrl, $backLabel] = $this->smartBack(url('resources'), 'Back to Resources');

        $this->view('resources.show', [
            'title' => $resource['title'],
            'activeNav' => 'resources',
            'resource' => $resource,
            'isSaved' => $viewerId ? SavedResource::isSaved((int) $viewerId, (int) $resource['id']) : false,
            'backUrl' => $backUrl,
            'backLabel' => $backLabel,
        ]);
    }

    public function toggleSave(string $id): void
    {
        $this->requireCsrf();
        if (!Auth::check()) {
            $this->redirect('login');
        }
        SavedResource::toggle((int) Auth::id(), (int) $id);
        $this->redirectBack('resources');
    }

    /** Shared by the Career Resources and Interview Prep tabs — same filters, different category scope. */
    private function resourceListTabData(array $categoryScope): array
    {
        $filters = [
            'q' => trim((string) $this->input('q', '')),
            'category' => (string) $this->input('category', ''),
            'type' => (string) $this->input('type', ''),
            'featured' => $this->input('featured') === '1' ? '1' : '',
        ];
        $filters = array_filter($filters, fn ($v) => $v !== '');

        $viewerId = Auth::id();

        return [
            'resources' => Resource::search($filters, $categoryScope),
            'featured' => empty($filters) ? Resource::featured(3, $categoryScope) : [],
            'categoryCounts' => Resource::countsByCategory($categoryScope),
            'totalCount' => Resource::countScope($categoryScope),
            'filters' => $filters,
            'savedIds' => $viewerId ? SavedResource::savedIdsFor((int) $viewerId) : [],
        ];
    }

    private function resumeReviewTabData(): array
    {
        $user = Auth::user();
        $viewerId = (int) Auth::id();

        $data = [
            'myReviews' => ResumeReview::forRequester($viewerId),
            'resumeFileUrl' => !empty($user['resume_file']) ? upload_url($user['resume_file']) : null,
            'resumeFileExt' => !empty($user['resume_file']) ? strtoupper((string) pathinfo($user['resume_file'], PATHINFO_EXTENSION)) : null,
            'resumeLinkUrl' => $user['resume_url'] ?? null,
        ];

        if (!empty($user['is_mentor'])) {
            $data['pendingQueue'] = ResumeReview::pendingUnclaimed();
            $data['myClaims'] = ResumeReview::claimedBy($viewerId);
        }

        return $data;
    }
}
