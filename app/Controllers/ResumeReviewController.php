<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Upload;
use App\Models\Notification;
use App\Models\ResumeReview;

class ResumeReviewController extends Controller
{
    public function __construct()
    {
        if (!Auth::check() || !Auth::user()) {
            Auth::logout();
            header('Location: ' . url('login'));
            exit;
        }
    }

    public function store(): void
    {
        $this->requireCsrf();
        $user = Auth::user();

        $hasFile = !empty($user['resume_file']);
        $hasLink = !empty($user['resume_url']);
        if (!$hasFile && !$hasLink) {
            $this->flash('error', 'Add a resume or CV to your profile before requesting a review.');
            $this->redirect('profile/edit#resume');
        }

        $includeFile = $hasFile && (bool) $this->input('include_file');
        $includeLink = $hasLink && (bool) $this->input('include_link');
        if (!$includeFile && !$includeLink) {
            $_SESSION['_errors'] = ['Select at least one of your CV file or resume link to submit.'];
            $this->redirect('resources?tab=resume-review');
        }

        $fileUrl = null;
        if ($includeFile) {
            try {
                $swappedFile = Upload::document($this->file('resume_review_file'), 'resumes');
            } catch (\RuntimeException $e) {
                $_SESSION['_errors'] = [$e->getMessage()];
                $this->redirect('resources?tab=resume-review');
            }
            $fileUrl = $swappedFile ? upload_url($swappedFile) : upload_url($user['resume_file']);
        }

        $linkUrl = $includeLink ? $user['resume_url'] : null;

        $message = trim((string) $this->input('message', ''));
        ResumeReview::create((int) Auth::id(), $fileUrl, $linkUrl, $message);

        $this->flash('success', 'Your resume was submitted for review.');
        $this->redirect('resources?tab=resume-review');
    }

    public function claim(string $id): void
    {
        $this->requireCsrf();
        if (empty(Auth::user()['is_mentor'])) {
            http_response_code(403);
            (new ErrorController())->notFound();
            return;
        }

        if (ResumeReview::claim((int) $id, (int) Auth::id())) {
            $review = ResumeReview::find((int) $id);
            if ($review) {
                Notification::notify(
                    (int) $review['requester_id'],
                    'resume_review_claimed',
                    Auth::user()['name'] . ' is now reviewing your resume.',
                    'resources?tab=resume-review'
                );
            }
            $this->flash('success', 'Review claimed. Leave feedback whenever you\'re ready.');
        } else {
            $this->flash('error', 'That review was already claimed by someone else.');
        }

        $this->redirect('resources?tab=resume-review');
    }

    public function feedback(string $id): void
    {
        $this->requireCsrf();
        if (empty(Auth::user()['is_mentor'])) {
            http_response_code(403);
            (new ErrorController())->notFound();
            return;
        }

        $feedback = trim((string) $this->input('feedback', ''));
        if ($feedback === '') {
            $this->flash('error', 'Write some feedback before submitting.');
            $this->redirect('resources?tab=resume-review');
        }

        if (ResumeReview::submitFeedback((int) $id, (int) Auth::id(), $feedback)) {
            $review = ResumeReview::find((int) $id);
            if ($review) {
                Notification::notify(
                    (int) $review['requester_id'],
                    'resume_review_completed',
                    Auth::user()['name'] . ' completed your resume review.',
                    'resources?tab=resume-review'
                );
            }
            $this->flash('success', 'Feedback submitted.');
        } else {
            $this->flash('error', 'Could not submit feedback for that review.');
        }

        $this->redirect('resources?tab=resume-review');
    }
}
