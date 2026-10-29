<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Advisor;
use App\Models\AdvisorNote;
use App\Models\User;

/** An advisor's self-service area: the alumni assigned to them, their profile, and private advising notes. */
class AdvisorController extends Controller
{
    public function __construct()
    {
        if (!Auth::check() || !Auth::isAdvisor()) {
            $this->redirect('/');
        }
    }

    public function index(): void
    {
        $this->view('advisor.my_alumni.index', [
            'title' => 'My Assigned Alumni',
            'activeNav' => 'advisor_my_alumni',
            'alumni' => Advisor::alumniForAdvisor((int) Auth::id()),
        ]);
    }

    public function show(string $id): void
    {
        $advisorId = (int) Auth::id();
        $alumniId = (int) $id;

        if (!Advisor::isAssigned($advisorId, $alumniId)) {
            http_response_code(404);
            (new ErrorController())->notFound();
            return;
        }

        $alum = User::find($alumniId);

        $this->view('advisor.my_alumni.show', [
            'title' => $alum['name'],
            'activeNav' => 'advisor_my_alumni',
            'alum' => $alum,
            'notes' => AdvisorNote::forAdvisorAndAlumni($advisorId, $alumniId),
        ]);
    }

    public function addNote(string $id): void
    {
        $this->requireCsrf();
        $advisorId = (int) Auth::id();
        $alumniId = (int) $id;

        if (!Advisor::isAssigned($advisorId, $alumniId)) {
            http_response_code(404);
            (new ErrorController())->notFound();
            return;
        }

        $note = trim((string) $this->input('note', ''));
        if ($note !== '') {
            AdvisorNote::create($advisorId, $alumniId, $note);
            $this->flash('success', 'Note added.');
        }
        $this->redirect("advisor/my-alumni/{$alumniId}");
    }
}
