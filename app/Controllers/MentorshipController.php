<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\MentorshipRequest;
use App\Models\Notification;
use App\Models\User;

class MentorshipController extends Controller
{
    private const CAREER_STAGES = ['early' => 'Early Career', 'mid' => 'Mid-Career', 'senior' => 'Senior / Executive'];
    private const AVAILABILITY = ['open' => 'Actively Available', 'limited' => 'Limited Availability', 'closed' => 'Not Available'];

    public function index(): void
    {
        $this->requireVisibility('mentorship');

        $filters = [];
        foreach (['q', 'industry', 'location'] as $key) {
            $value = trim((string) $this->input($key, ''));
            if ($value !== '') {
                $filters[$key] = $value;
            }
        }
        if (in_array($this->input('career_stage'), array_keys(self::CAREER_STAGES), true)) {
            $filters['career_stage'] = $this->input('career_stage');
        }
        if (in_array($this->input('availability'), array_keys(self::AVAILABILITY), true)) {
            $filters['availability'] = $this->input('availability');
        }
        // Kept as a distinct field name for backward-compat links (e.g. programme pages linking to a specific area).
        $area = trim((string) $this->input('area', ''));
        if ($area !== '') {
            $filters['area'] = $area;
        }

        $this->view('mentorship.index', [
            'title' => 'Find a Mentor',
            'activeNav' => 'mentorship',
            'mentors' => MentorshipRequest::mentors($filters),
            'totalMentors' => MentorshipRequest::countMentors($filters),
            'industries' => MentorshipRequest::distinctIndustries(),
            'locations' => MentorshipRequest::distinctLocations(),
            'careerStages' => self::CAREER_STAGES,
            'availabilityOptions' => self::AVAILABILITY,
            'filters' => $filters,
            'area' => $area,
        ]);
    }

    public function becomeForm(): void
    {
        $this->guard();

        $this->view('mentorship.become', [
            'title' => 'Become a Mentor',
            'activeNav' => 'mentorship',
            'user' => Auth::user(),
            'availabilityOptions' => self::AVAILABILITY,
            'mentorCount' => MentorshipRequest::countMentors([]),
            'sampleMentors' => array_slice(MentorshipRequest::mentors([]), 0, 3),
        ]);
    }

    public function becomeStore(): void
    {
        $this->requireCsrf();
        $this->guard();

        $isMentor = $this->input('is_mentor') ? 1 : 0;
        $mentorshipAreas = trim((string) $this->input('mentorship_areas', ''));
        $availability = in_array($this->input('mentor_availability'), array_keys(self::AVAILABILITY), true)
            ? $this->input('mentor_availability') : 'open';

        if ($isMentor && $mentorshipAreas === '') {
            $_SESSION['_errors'] = ['Please list at least one area you can mentor in.'];
            $this->redirect('mentorship/become');
        }

        User::update((int) Auth::id(), [
            'is_mentor' => $isMentor,
            'mentorship_areas' => $mentorshipAreas !== '' ? $mentorshipAreas : null,
            'mentor_availability' => $availability,
        ]);

        $this->flash('success', $isMentor ? 'You are now listed as a mentor. Thank you for giving back!' : 'Your mentor availability has been updated.');
        $this->redirect('mentorship');
    }

    public function requests(): void
    {
        $this->guard();

        $this->view('mentorship.requests', [
            'title' => 'My Mentorship Requests',
            'activeNav' => 'mentorship',
            'incoming' => MentorshipRequest::incomingFor((int) Auth::id()),
            'sent' => MentorshipRequest::sentBy((int) Auth::id()),
        ]);
    }

    public function request(string $mentorId): void
    {
        $this->requireCsrf();
        $this->guard();

        $area = trim((string) $this->input('area', ''));
        $message = trim((string) $this->input('message', ''));

        if ((int) $mentorId === (int) Auth::id()) {
            $this->flash('error', 'You cannot request mentorship from yourself.');
            $this->redirect('mentorship');
        }

        MentorshipRequest::create((int) $mentorId, (int) Auth::id(), $area, $message);
        Notification::notify(
            (int) $mentorId,
            'mentorship_request',
            Auth::user()['name'] . ' requested mentorship from you.',
            'mentorship/requests'
        );
        $this->flash('success', 'Mentorship request sent.');
        $this->redirect('mentorship');
    }

    public function accept(string $id): void
    {
        $this->requireCsrf();
        $this->guard();
        $row = MentorshipRequest::find((int) $id);
        if ($row && MentorshipRequest::respond((int) $id, (int) Auth::id(), 'accepted')) {
            Notification::notify(
                (int) $row['requester_id'],
                'mentorship_accepted',
                Auth::user()['name'] . ' accepted your mentorship request.',
                'mentorship/requests'
            );
        }
        $this->flash('success', 'Mentorship request accepted.');
        $this->redirect('mentorship/requests');
    }

    public function decline(string $id): void
    {
        $this->requireCsrf();
        $this->guard();
        MentorshipRequest::respond((int) $id, (int) Auth::id(), 'declined');
        $this->flash('success', 'Mentorship request declined.');
        $this->redirect('mentorship/requests');
    }

    private function guard(): void
    {
        if (!Auth::check() || !Auth::user()) {
            Auth::logout();
            header('Location: ' . url('login'));
            exit;
        }
    }
}
