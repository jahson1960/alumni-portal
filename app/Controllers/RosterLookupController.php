<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\AlumniRoster;

/**
 * Backs the live matric-number check on the public and admin-assisted registration forms —
 * registration no longer asks for name/program/cohort/graduation year, so this lets the form
 * show what will be pulled from the roster before the alumnus commits to signing up.
 */
class RosterLookupController extends Controller
{
    public function lookup(): void
    {
        header('Content-Type: application/json');

        $matricNumber = trim((string) $this->input('matric_number', ''));
        $entry = $matricNumber !== '' ? AlumniRoster::findByMatric($matricNumber) : null;

        if (!$entry) {
            echo json_encode(['found' => false]);
            return;
        }

        if ($entry['claimed_by_user_id']) {
            echo json_encode(['found' => true, 'claimed' => true]);
            return;
        }

        echo json_encode([
            'found' => true,
            'claimed' => false,
            'name' => $entry['full_name'],
            'program' => $entry['program'],
            'cohort' => $entry['cohort'],
            'graduation_year' => $entry['graduation_year'],
        ]);
    }
}
