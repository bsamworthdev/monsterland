<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Monster;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class MyMonstersController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth','verified']);
    }

    public function index($page = 0, $time_filter = 'week')
    {
        $user_id = Auth::User()->id;

        switch ($time_filter){
            case 'day':
                $date = \Carbon\Carbon::today()->subHours(24);
            break;
            case 'week':
                $date = \Carbon\Carbon::today()->subDays(7);
            break;
            case 'month':
                $date = \Carbon\Carbon::today()->subWeeks(4);
            break;
            case 'year':
                $date = \Carbon\Carbon::today()->subWeeks(52);
            break;
            case 'ever':
                $date = \Carbon\Carbon::today()->subYears(10);
            break;
        }
        
        $top_monsters = Monster::withCount([
        'ratings as average_rating' => function($query) {
            $query->select(DB::raw('coalesce(avg(rating),0)'));
        }, 
        'ratings as ratings_count'])
        ->join('monster_segments', 'monster_segments.monster_id', '=', 'monsters.id')
        ->where('monsters.status', 'complete')
        ->where('monsters.created_at','>=',$date)
        ->where('monster_segments.created_by',$user_id)
        ->groupBy('monsters.id')
        ->orderBy('average_rating','desc')
        ->orderBy('ratings_count', 'desc')
        ->orderBy('monsters.name', 'asc')
        ->skip($page*8)
        ->take(8)
        ->get();

        // Model::where('types_id', $specialism_id)
        //     ->withCount(['requests as requests_1' => function ($query) {
        //         $query->where('type', 1);
        //     }, 'requests as requests_2' => function ($query) {
        //         $query->where('type', 2);
        //     }])

        return view('myMonsters', [
            "top_monsters" => $top_monsters,
            "page" => $page,
            "time_filter" => $time_filter
            // "user_id" => $user_id
        ]);
    }
}
