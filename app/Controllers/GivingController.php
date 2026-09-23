<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Controller;
use App\Models\Campaign;
use App\Models\DonationConfirmation;
use App\Models\DonationMethod;
use App\Models\GivingCause;
use App\Models\Setting;

class GivingController extends Controller
{
    private const SORTS = ['newest', 'most_funded', 'least_funded'];

    public function index(): void
    {
        $this->requireVisibility('give');

        $q = trim((string) $this->input('q', ''));
        $sort = in_array($this->input('sort'), self::SORTS, true) ? $this->input('sort') : 'newest';
        $causes = GivingCause::allOrdered();
        $viewerId = Auth::id();

        $this->view('giving.index', [
            'title' => 'Give Back',
            'activeNav' => 'giving',
            'campaigns' => Campaign::search($q, $sort),
            'causes' => $causes,
            'contactEmail' => Setting::get('giving_contact_email', 'giving@rbsn.example.com'),
            'q' => $q,
            'sort' => $sort,
            'totalRaised' => Campaign::totalRaised(),
            'activeCount' => Campaign::activeCount(),
            'causesCount' => count($causes),
            'donationMethods' => DonationMethod::activeOrdered(),
            'confirmedCampaignIds' => $viewerId ? DonationConfirmation::confirmedCampaignIdsFor((int) $viewerId) : [],
        ]);
    }

    public function confirm(string $id): void
    {
        $this->requireCsrf();
        if (!Auth::check()) {
            $this->redirect('login');
        }

        $campaign = Campaign::find((int) $id);
        if ($campaign) {
            DonationConfirmation::create((int) Auth::id(), (int) $id);
            $this->flash('success', 'Thank you! Your donation to "' . $campaign['title'] . '" has been recorded.');
        }
        $this->redirectBack('give');
    }

    public function cause(string $slug): void
    {
        $this->requireVisibility('give');

        $cause = GivingCause::findBySlug($slug);
        if (!$cause) {
            http_response_code(404);
            (new ErrorController())->notFound();
            return;
        }

        $this->view('giving.cause', [
            'title' => $cause['title'] . ' - Give Back',
            'activeNav' => 'giving',
            'cause' => $cause,
            'contactEmail' => Setting::get('giving_contact_email', 'giving@rbsn.example.com'),
        ]);
    }
}
