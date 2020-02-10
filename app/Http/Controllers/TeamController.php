<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Team;

class TeamController extends Controller
{
    protected $filters = [
        'id' => 'ofId',
        'orderBy' => 'ofOrderBy',
        'not' => 'ofNot',
        'city' => 'ofCity',
        'division_id' => 'ofDivision',
        'short_name' => 'ofShortName',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'id' => 'exists:teams,id',
            'division_id' => 'exists:divisions,id',
            'short_name' => 'exists:teams,short_name',
        ]);

        $teams = $this->applyFilters($request, new Team);
        return $teams->get();
    }
}
