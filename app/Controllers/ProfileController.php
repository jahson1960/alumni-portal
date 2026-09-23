<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Core\Upload;
use App\Models\User;
use App\Models\UserEducation;
use App\Models\UserExperience;

class ProfileController extends Controller
{
    private const VISIBILITY_OPTIONS = ['public', 'alumni', 'private'];

    public function __construct()
    {
        if (!Auth::check() || !Auth::user()) {
            Auth::logout();
            header('Location: ' . url('login'));
            exit;
        }
    }

    public function edit(): void
    {
        $userId = (int) Auth::id();

        $this->view('profile.edit', [
            'title' => 'Edit Profile',
            'user' => Auth::user(),
            'education' => UserEducation::forUser($userId),
            'experience' => UserExperience::forUser($userId),
            'needsRichEditor' => true,
        ]);
    }

    public function update(): void
    {
        $this->requireCsrf();
        $user = Auth::user();

        $name = trim((string) $this->input('name', ''));
        $preferredName = trim((string) $this->input('preferred_name', ''));
        $headline = trim((string) $this->input('headline', ''));
        $company = trim((string) $this->input('company', ''));
        $companySize = trim((string) $this->input('company_size', ''));
        $yearsInRole = trim((string) $this->input('years_in_role', ''));
        $industry = trim((string) $this->input('industry', ''));
        $city = trim((string) $this->input('city', ''));
        $country = trim((string) $this->input('country', ''));
        $graduationYear = trim((string) $this->input('graduation_year', ''));
        $program = trim((string) $this->input('program', ''));
        $bio = trim((string) $this->input('bio', ''));
        $skills = trim((string) $this->input('skills', ''));
        $expertiseAreas = trim((string) $this->input('expertise_areas', ''));
        $businessInterests = trim((string) $this->input('business_interests', ''));
        $personalInterests = trim((string) $this->input('personal_interests', ''));
        $linkedin = trim((string) $this->input('linkedin_url', ''));
        $personalWebsite = trim((string) $this->input('personal_website', ''));
        $twitter = trim((string) $this->input('twitter_url', ''));
        $phone = trim((string) $this->input('phone', ''));
        $resumeUrl = trim((string) $this->input('resume_url', ''));
        $visibility = in_array($this->input('profile_visibility'), self::VISIBILITY_OPTIONS, true)
            ? $this->input('profile_visibility') : 'public';
        $showEmail = $this->input('show_email') ? 1 : 0;
        $showPhone = $this->input('show_phone') ? 1 : 0;
        $isMentor = $this->input('is_mentor') ? 1 : 0;
        $mentorshipAreas = trim((string) $this->input('mentorship_areas', ''));

        $errors = [];
        if ($name === '') {
            $errors[] = 'Full name is required.';
        }

        $bio = \App\Core\Sanitizer::html($bio);

        $avatarPath = $user['avatar'] ?? null;
        try {
            $uploaded = Upload::image($this->file('avatar'), 'avatars');
            if ($uploaded) {
                $avatarPath = $uploaded;
            }
        } catch (\RuntimeException $e) {
            $errors[] = $e->getMessage();
        }

        $resumeFilePath = $user['resume_file'] ?? null;
        try {
            $uploadedResume = Upload::document($this->file('resume_file'), 'resumes');
            if ($uploadedResume) {
                $resumeFilePath = $uploadedResume;
            }
        } catch (\RuntimeException $e) {
            $errors[] = $e->getMessage();
        }

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->redirect('profile/edit');
        }

        User::update((int) $user['id'], [
            'name' => $name,
            'preferred_name' => $preferredName !== '' ? $preferredName : null,
            'headline' => $headline !== '' ? $headline : null,
            'company' => $company !== '' ? $company : null,
            'company_size' => $companySize !== '' ? $companySize : null,
            'years_in_role' => $yearsInRole !== '' ? $yearsInRole : null,
            'industry' => $industry !== '' ? $industry : null,
            'city' => $city !== '' ? $city : null,
            'country' => $country !== '' ? $country : null,
            'graduation_year' => $graduationYear !== '' ? (int) $graduationYear : null,
            'program' => $program !== '' ? $program : null,
            'bio' => $bio !== '' ? $bio : null,
            'skills' => $skills !== '' ? $skills : null,
            'expertise_areas' => $expertiseAreas !== '' ? $expertiseAreas : null,
            'business_interests' => $businessInterests !== '' ? $businessInterests : null,
            'personal_interests' => $personalInterests !== '' ? $personalInterests : null,
            'linkedin_url' => $linkedin !== '' ? $linkedin : null,
            'personal_website' => $personalWebsite !== '' ? $personalWebsite : null,
            'twitter_url' => $twitter !== '' ? $twitter : null,
            'phone' => $phone !== '' ? $phone : null,
            'resume_url' => $resumeUrl !== '' ? $resumeUrl : null,
            'resume_file' => $resumeFilePath,
            'profile_visibility' => $visibility,
            'show_email' => $showEmail,
            'show_phone' => $showPhone,
            'is_mentor' => $isMentor,
            'mentorship_areas' => $mentorshipAreas !== '' ? $mentorshipAreas : null,
            'avatar' => $avatarPath,
        ]);

        $this->flash('success', 'Your profile has been updated.');
        $this->redirect('profile/edit');
    }

    public function educationStore(): void
    {
        $this->saveEducation(null);
    }

    public function educationUpdate(string $id): void
    {
        $this->saveEducation((int) $id);
    }

    public function educationDestroy(string $id): void
    {
        $this->requireCsrf();
        UserEducation::deleteAsOwner((int) $id, (int) Auth::id());
        $this->flash('success', 'Education entry removed.');
        $this->redirect('profile/edit#education');
    }

    public function experienceStore(): void
    {
        $this->saveExperience(null);
    }

    public function experienceUpdate(string $id): void
    {
        $this->saveExperience((int) $id);
    }

    public function experienceDestroy(string $id): void
    {
        $this->requireCsrf();
        UserExperience::deleteAsOwner((int) $id, (int) Auth::id());
        $this->flash('success', 'Experience entry removed.');
        $this->redirect('profile/edit#experience');
    }

    private function saveEducation(?int $id): void
    {
        $this->requireCsrf();
        $userId = (int) Auth::id();

        if ($id !== null && !UserEducation::findAsOwner($id, $userId)) {
            $this->flash('error', 'Education entry not found.');
            $this->redirect('profile/edit#education');
        }

        $school = trim((string) $this->input('school', ''));
        if ($school === '') {
            $this->flash('error', 'School / institution is required.');
            $this->redirect('profile/edit#education');
        }

        $data = [
            'user_id' => $userId,
            'school' => $school,
            'degree' => trim((string) $this->input('degree', '')) ?: null,
            'field_of_study' => trim((string) $this->input('field_of_study', '')) ?: null,
            'start_year' => $this->input('start_year') !== '' ? (int) $this->input('start_year') : null,
            'end_year' => $this->input('end_year') !== '' ? (int) $this->input('end_year') : null,
        ];

        if ($id === null) {
            $data['sort_order'] = 0;
            UserEducation::create($data);
            $this->flash('success', 'Education added.');
        } else {
            unset($data['user_id']);
            UserEducation::update($id, $data);
            $this->flash('success', 'Education updated.');
        }

        $this->redirect('profile/edit#education');
    }

    private function saveExperience(?int $id): void
    {
        $this->requireCsrf();
        $userId = (int) Auth::id();

        if ($id !== null && !UserExperience::findAsOwner($id, $userId)) {
            $this->flash('error', 'Experience entry not found.');
            $this->redirect('profile/edit#experience');
        }

        $title = trim((string) $this->input('title', ''));
        $company = trim((string) $this->input('company', ''));
        if ($title === '' || $company === '') {
            $this->flash('error', 'Title and company are required.');
            $this->redirect('profile/edit#experience');
        }

        $isCurrent = $this->input('is_current') ? 1 : 0;

        $data = [
            'user_id' => $userId,
            'title' => $title,
            'company' => $company,
            'location' => trim((string) $this->input('location', '')) ?: null,
            'start_date' => $this->input('start_date') !== '' ? $this->input('start_date') : null,
            'end_date' => !$isCurrent && $this->input('end_date') !== '' ? $this->input('end_date') : null,
            'is_current' => $isCurrent,
            'description' => trim((string) $this->input('description', '')) ?: null,
        ];

        if ($id === null) {
            $data['sort_order'] = 0;
            UserExperience::create($data);
            $this->flash('success', 'Experience added.');
        } else {
            unset($data['user_id']);
            UserExperience::update($id, $data);
            $this->flash('success', 'Experience updated.');
        }

        $this->redirect('profile/edit#experience');
    }
}
