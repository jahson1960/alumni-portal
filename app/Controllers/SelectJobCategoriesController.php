<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Category;
use App\Models\User;

/**
 * Forced onboarding step for every admin-registered account (single or bulk, via
 * register_from_roster()) - an admin registering someone can't reasonably pick their job
 * interests for them, so it's deferred to here, right after the account's forced password
 * change. A self-registered account never reaches this page - it already picked categories on
 * the sign-up form itself. See onboarding_wall_redirect() in helpers.php for the enforcement.
 */
class SelectJobCategoriesController extends Controller
{
    public function __construct()
    {
        if (!Auth::check()) {
            $this->redirectToLogin();
        }
    }

    public function show(): void
    {
        $this->view('auth.select_job_categories', [
            'title' => 'Select Job Categories',
            'allCategories' => Category::forType('job'),
        ], 'auth');
    }

    public function update(): void
    {
        $this->requireCsrf();

        $jobCategoryIds = array_map('intval', (array) $this->input('job_categories', []));
        if (!$jobCategoryIds) {
            $_SESSION['_errors'] = ['Please select at least one job category.'];
            $this->redirect('select-job-categories');
        }

        $userId = (int) Auth::id();
        Category::syncUserJobCategories($userId, $jobCategoryIds);
        User::update($userId, ['must_select_job_categories' => 0]);

        $this->flash('success', 'Your interests have been saved. Let\'s finish setting up your profile.');
        $this->redirect('profile/edit');
    }
}
