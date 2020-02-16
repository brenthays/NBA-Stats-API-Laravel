<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Player;

class PlayerController extends Controller
{
    protected $filters = [
        'id' => 'ofId',
        'orderBy' => 'ofOrderBy',
        'not' => 'ofNot',
        'team_id' => 'ofTeam',
        'name' => 'ofName',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'id' => 'exists:players,id',
            'season_id' => 'exists:seasons,id',
            'game_id' => 'exists:seasons,id',
            'team_id' => 'exists:teams,id',
        ]);

        $players = $this->applyFilters($request, new Player);

        if($request->has('with_stats') && $request->with_stats && ($request->has('season_id') || $request->has('game_id'))) {
            $players = $players
                ->statsBase($request->only(['game_id', 'season_id']))
                ->statsMinutes()
                ->statsScoring()
                ->statsShooting()
                ->statsFreeThrows()
                ->statsAssists()
                ->statsRebounding()
                ->statsOffensiveVsDefensive();
        }

        return $players->get();
    }
}
