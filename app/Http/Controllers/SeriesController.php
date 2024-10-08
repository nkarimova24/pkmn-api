<?php

namespace App\Http\Controllers;

use App\Models\Series;
use Illuminate\Http\Request;

class SeriesController extends Controller
{

    //all series    , sorted from new to old
    public function index() 
{

    $series = Series::with(['sets' => function($query) {
        $query->orderBy('release_date', 'desc');
    }])->get();

    $sortedSeries = $series->sortByDesc(function ($s) {
        return $s->sets->isEmpty() ? null : $s->sets->first()->release_date;
    });

    return response()->json($sortedSeries->values()); 
}

    //series from sets
    public function setsFromSeries($seriesId)
    {
        $sets = Series::find($seriesId)->sets;
        return response()->json($sets);
    }
}
