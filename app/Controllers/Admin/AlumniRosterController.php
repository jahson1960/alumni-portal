<?php

namespace App\Controllers\Admin;

use App\Core\SpreadsheetReader;
use App\Models\AlumniRoster;
use App\Models\Program;

class AlumniRosterController extends AdminController
{
    private const MAX_IMPORT_SIZE = 5 * 1024 * 1024;

    /** @var array<string,string> normalized spreadsheet header => alumni_roster column */
    private const HEADER_ALIASES = [
        'matricnumber' => 'matric_number',
        'matricno' => 'matric_number',
        'matric' => 'matric_number',
        'fullname' => 'full_name',
        'name' => 'full_name',
        'studentname' => 'full_name',
        'email' => 'email',
        'emailaddress' => 'email',
        'graduationyear' => 'graduation_year',
        'gradyear' => 'graduation_year',
        'year' => 'graduation_year',
        'cohort' => 'cohort',
        'program' => 'program',
        'programme' => 'program',
        'course' => 'program',
    ];

    public function index(): void
    {
        $filters = [
            'cohort' => trim((string) $this->input('cohort', '')),
            'program' => trim((string) $this->input('program', '')),
            'graduation_year' => trim((string) $this->input('graduation_year', '')),
        ];
        $activeFilters = array_filter($filters);

        $p = $this->paginate(AlumniRoster::countFiltered($filters), 30);
        $this->view('admin.alumni_roster.index', [
            'title' => term_plural() . ' Roster',
            'activeNav' => 'alumni_roster',
            'rows' => AlumniRoster::filtered($filters, $p['perPage'], $p['offset']),
            'filters' => $filters,
            'hasActiveFilters' => (bool) $activeFilters,
            'matchingUnclaimedCount' => $activeFilters ? count(AlumniRoster::unclaimedFiltered($filters)) : 0,
            'cohorts' => AlumniRoster::distinctCohorts(),
            'programs' => AlumniRoster::distinctPrograms(),
            'graduationYears' => AlumniRoster::distinctGraduationYears(),
            ...$p,
        ]);
    }

    public function create(): void
    {
        $this->view('admin.alumni_roster.form', [
            'title' => 'New Roster Entry',
            'activeNav' => 'alumni_roster',
            'row' => null,
            'programs' => Program::all('name ASC'),
        ]);
    }

    public function store(): void
    {
        $this->save(null);
    }

    public function edit(string $id): void
    {
        $row = AlumniRoster::find((int) $id);
        if (!$row) {
            $this->flash('error', 'Roster entry not found.');
            $this->redirect('admin/alumni-roster');
        }

        $this->view('admin.alumni_roster.form', [
            'title' => 'Edit Roster Entry',
            'activeNav' => 'alumni_roster',
            'row' => $row,
            'programs' => Program::all('name ASC'),
        ]);
    }

    public function update(string $id): void
    {
        $this->save((int) $id);
    }

    public function destroy(string $id): void
    {
        $this->requireCsrf();
        AlumniRoster::delete((int) $id);
        $this->flash('success', 'Roster entry deleted.');
        $this->redirect('admin/alumni-roster');
    }

    private function save(?int $id): void
    {
        $this->requireCsrf();

        $matricNumber = trim((string) $this->input('matric_number', ''));
        $fullName = trim((string) $this->input('full_name', ''));
        $email = trim((string) $this->input('email', ''));
        $graduationYear = trim((string) $this->input('graduation_year', ''));
        $cohort = trim((string) $this->input('cohort', ''));
        $program = trim((string) $this->input('program', ''));

        if ($program !== '' && !in_array($program, array_column(Program::all(), 'name'), true)) {
            $program = '';
        }

        $errors = [];
        if ($matricNumber === '') {
            $errors[] = 'A matric number is required.';
        }
        if ($fullName === '') {
            $errors[] = 'Full name is required — it is used as-is to create the ' . term_singular_lc() . "'s account.";
        }
        if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'That email address does not look valid.';
        }
        if ($graduationYear === '' || !ctype_digit($graduationYear)) {
            $errors[] = 'A valid graduation year is required.';
        }
        if ($cohort === '') {
            $errors[] = 'A cohort is required.';
        }

        if (!$errors) {
            $existing = AlumniRoster::findByMatric($matricNumber);
            if ($existing && (int) $existing['id'] !== $id) {
                $errors[] = 'That matric number is already on the roster.';
            }
        }

        if ($errors) {
            $_SESSION['_errors'] = $errors;
            $this->redirect($id ? "admin/alumni-roster/{$id}/edit" : 'admin/alumni-roster/create');
        }

        $data = [
            'matric_number' => $matricNumber,
            'full_name' => $fullName,
            'email' => $email !== '' ? $email : null,
            'graduation_year' => (int) $graduationYear,
            'cohort' => $cohort,
            'program' => $program !== '' ? $program : null,
        ];

        if ($id === null) {
            AlumniRoster::create($data);
            $this->flash('success', 'Roster entry added.');
        } else {
            AlumniRoster::update($id, $data);
            $this->flash('success', 'Roster entry updated.');
        }

        $this->redirect('admin/alumni-roster');
    }

    public function import(): void
    {
        $this->view('admin.alumni_roster.import', [
            'title' => 'Import ' . term_plural() . ' Roster',
            'activeNav' => 'alumni_roster',
        ]);
    }

    public function importSample(): void
    {
        header('Content-Type: text/csv');
        header('Content-Disposition: attachment; filename="roster-template.csv"');
        $out = fopen('php://output', 'w');
        fputcsv($out, ['Matric Number', 'Full Name', 'Email', 'Graduation Year', 'Cohort', 'Program']);
        fputcsv($out, ['RBS/2019/001', 'Jane Doe', 'jane.doe@example.com', '2019', 'MBA 2019', 'MBA']);
        fclose($out);
        exit;
    }

    public function importStore(): void
    {
        $this->requireCsrf();

        $file = $this->file('roster_file');
        if (!$file || $file['error'] === UPLOAD_ERR_NO_FILE) {
            $this->flash('error', 'Please choose a .csv or .xlsx file to upload.');
            $this->redirect('admin/alumni-roster/import');
        }
        if ($file['error'] !== UPLOAD_ERR_OK) {
            $this->flash('error', 'That file could not be uploaded. Please try again.');
            $this->redirect('admin/alumni-roster/import');
        }
        if ($file['size'] > self::MAX_IMPORT_SIZE) {
            $this->flash('error', 'That file is too large — please keep it under 5MB.');
            $this->redirect('admin/alumni-roster/import');
        }

        try {
            $rows = SpreadsheetReader::read($file['tmp_name'], $file['name']);
        } catch (\RuntimeException $e) {
            $this->flash('error', $e->getMessage());
            $this->redirect('admin/alumni-roster/import');
        }

        $validRows = [];
        $skippedInvalid = 0;

        foreach ($rows as $row) {
            $mapped = $this->mapImportRow($row);

            $matricNumber = trim((string) ($mapped['matric_number'] ?? ''));
            $fullName = trim((string) ($mapped['full_name'] ?? ''));
            $graduationYear = trim((string) ($mapped['graduation_year'] ?? ''));
            $cohort = trim((string) ($mapped['cohort'] ?? ''));
            $email = trim((string) ($mapped['email'] ?? ''));
            $program = trim((string) ($mapped['program'] ?? ''));

            if ($matricNumber === '' || $fullName === '' || $cohort === '' || !ctype_digit($graduationYear)) {
                $skippedInvalid++;
                continue;
            }
            if ($email !== '' && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $email = '';
            }

            $validRows[] = [
                'matric_number' => $matricNumber,
                'full_name' => $fullName,
                'email' => $email !== '' ? $email : null,
                'graduation_year' => (int) $graduationYear,
                'cohort' => $cohort,
                'program' => $program !== '' ? $program : null,
            ];
        }

        try {
            $result = AlumniRoster::importRows($validRows);
        } catch (\Throwable $e) {
            $this->flash('error', 'The import failed partway through, so no changes were made. Please check the file and try again.');
            $this->redirect('admin/alumni-roster/import');
        }

        $summary = "{$result['added']} added, {$result['updated']} updated";
        if ($result['skipped_claimed'] > 0) {
            $summary .= ", {$result['skipped_claimed']} skipped (already claimed)";
        }
        if ($skippedInvalid > 0) {
            $summary .= ", {$skippedInvalid} skipped (missing required fields)";
        }
        $this->flash('success', $summary . '.');
        $this->redirect('admin/alumni-roster');
    }

    /** Translates a SpreadsheetReader row (normalized headers) into alumni_roster column names via HEADER_ALIASES. */
    private function mapImportRow(array $row): array
    {
        $mapped = [];
        foreach ($row as $header => $value) {
            $column = self::HEADER_ALIASES[$header] ?? null;
            if ($column !== null) {
                $mapped[$column] = $value;
            }
        }
        return $mapped;
    }

    /** Registers a single roster entry's account - the row must be unclaimed and have an email on file. */
    public function register(string $id): void
    {
        $this->requireCsrf();

        $row = AlumniRoster::find((int) $id);
        if (!$row) {
            $this->flash('error', 'Roster entry not found.');
            $this->redirect('admin/alumni-roster');
        }
        if ($row['claimed_by_user_id']) {
            $this->flash('error', 'This roster entry has already been claimed.');
            $this->redirect('admin/alumni-roster');
        }
        $email = trim((string) ($row['email'] ?? ''));
        if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->flash('error', 'Add a valid email to this roster entry before registering it.');
            $this->redirect('admin/alumni-roster');
        }
        if (!email_domain_has_mail_server($email)) {
            $this->flash('error', "We couldn't find a mail server for that email's domain.");
            $this->redirect('admin/alumni-roster');
        }
        if (\App\Models\User::emailExists($email)) {
            $this->flash('error', 'An account with that email already exists.');
            $this->redirect('admin/alumni-roster');
        }

        register_from_roster($row, $email);
        $this->flash('success', trim((string) $row['full_name']) . ' has been registered and emailed their login details.');
        $this->redirect('admin/alumni-roster');
    }

    /** Registers every unclaimed, emailed roster row matching the submitted filters. */
    public function registerFiltered(): void
    {
        $this->requireCsrf();

        $filters = [
            'cohort' => trim((string) $this->input('cohort', '')),
            'program' => trim((string) $this->input('program', '')),
            'graduation_year' => trim((string) $this->input('graduation_year', '')),
        ];

        if (!array_filter($filters)) {
            $this->flash('error', 'Choose at least one filter before registering in bulk.');
            $this->redirect('admin/alumni-roster');
        }

        $rows = AlumniRoster::unclaimedFiltered($filters);
        $registered = 0;
        $skippedTaken = 0;
        $skippedDns = 0;

        foreach ($rows as $row) {
            $email = trim((string) $row['email']);
            if (\App\Models\User::emailExists($email)) {
                $skippedTaken++;
                continue;
            }
            if (!email_domain_has_mail_server($email)) {
                $skippedDns++;
                continue;
            }
            register_from_roster($row, $email);
            $registered++;
        }

        $summary = "Registered {$registered} " . term_plural_lc() . '.';
        if ($skippedTaken > 0) {
            $summary .= " {$skippedTaken} skipped — email already in use.";
        }
        if ($skippedDns > 0) {
            $summary .= " {$skippedDns} skipped — email domain not reachable.";
        }
        $this->flash('success', $summary);
        $this->redirect('admin/alumni-roster');
    }
}
