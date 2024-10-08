<?php

namespace App\Http\Controllers;
use App\Models\Series;
use Illuminate\Http\Request;

class SetController extends Controller
{
    //sets from series
    public function setsFromSeries($seriesId)
    {
        $sets = Series::find($seriesId)->sets;
        return response()->json($sets);
    }
}
