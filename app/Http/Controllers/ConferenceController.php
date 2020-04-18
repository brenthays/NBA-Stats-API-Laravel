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

        $cacheKey = $this->getCacheKey('allConferences', $request);
        $conferences = Cache::remember($cacheKey, now()->addMinutes(15), function() use ($request) {
            return $this->applyFilters($request, new Conference)->get();
        });

        return $conferences;
    }
}
