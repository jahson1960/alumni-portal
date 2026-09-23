<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class CohortController extends Controller
{
    public function index(): void
    {
        $this->requireVisibility('cohorts');

        $this->view('cohorts.index', [
            'title' => 'Class & Cohort Communities',
            'activeNav' => 'cohorts',
            'cohorts' => User::cohorts(),
        ]);
    }

    public function show(string $program, string $year): void
    {
        $members = User::cohortMembers($program, (int) $year);

        if (empty($members)) {
            http_response_code(404);
            (new ErrorController())->notFound();
            return;
        }

        $this->view('cohorts.show', [
            'title' => $program . ' ' . $year,
            'activeNav' => 'cohorts',
            'program' => $program,
            'year' => $year,
            'members' => $members,
            'stats' => User::cohortStats($program, (int) $year),
        ]);
    }
}
