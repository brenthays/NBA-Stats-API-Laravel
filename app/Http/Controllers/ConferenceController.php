<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Conference;
use Illuminate\Support\Facades\Cache;

class ConferenceController extends Controller
{
    protected $filters = [
        'id' => 'ofId',
        'orderBy' => 'ofOrderBy',
        'not' => 'ofNot',
    ];

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $this->validate($request, [
            'id' => 'exists:conferences,id',
        ]);

        $cacheKey = 'allConferences' . implode($request->all(), '&');
        $conferences = Cache::rememberForever($cacheKey, function() use ($request) {
            return $this->applyFilters($request, new Conference)->get();
        });

        return $conferences;
    }
}
