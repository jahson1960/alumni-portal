<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\Company;
use App\Models\Job;

class CompanyController extends Controller
{
    /** Old standalone URL — the listing now lives on the Careers page's "Companies Hiring" tab. */
    public function index(): void
    {
        $this->redirect('jobs?tab=companies');
    }

    public function show(string $id): void
    {
        $company = Company::find((int) $id);
        if (!$company) {
            http_response_code(404);
            (new ErrorController())->notFound();
            return;
        }

        $this->view('companies.show', [
            'title' => $company['name'],
            'activeNav' => 'jobs',
            'company' => $company,
            'openJobs' => Job::openByCompany((int) $id),
            'alumniHere' => Company::alumniAt($company['name']),
        ]);
    }
}
