<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Game;

class GameController extends Controller
{
    protected $filters = [
        'id' => 'ofId',
        'orderBy' => 'ofOrderBy',
        'not' => 'ofNot',
        'season_id' => 'ofSeason',
        'date' => 'ofDate',
        'team_id' => 'ofTeam',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'id' => 'exists:games,id',
            'season_id' => 'exists:seasons,id',
            'team_id' => 'exists:teams,id',
        ]);

        $games = $this->applyFilters($request, new Game);
        return $games->get();
    }
}
