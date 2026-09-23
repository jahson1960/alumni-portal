<?php

namespace App\Controllers;

use App\Core\Controller;
use App\Models\User;

class MapController extends Controller
{
    public function index(): void
    {
        $this->requireVisibility('map');

        $country = $this->input('country');
        $country = $country !== null && $country !== '' ? (string) $country : null;

        $this->view('map.index', [
            'title' => 'Our Alumni Around the World',
            'activeNav' => 'map',
            'countryCounts' => User::countByCountry(),
            'selectedCountry' => $country,
            'cityCounts' => $country ? User::countByCity($country) : [],
        ]);
    }
}
