<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Monster;
use Illuminate\Support\Facades\DB;

class HallOfFameController extends Controller
{
    public function index()
    {
        // $user_id = Auth::User()->id;

        $top_monsters = Monster::withCount([
        'ratings as average_rating' => function($query) {
            $query->select(DB::raw('coalesce(avg(rating),0)'));
        }, 
        'ratings as ratings_count'])
        ->where('status', 'complete')
        ->having('average_rating', '>', 0)
        ->having('ratings_count', '>', 2)
        ->orderBy('average_rating','desc')
        ->take(6)
        ->get();

        // Model::where('types_id', $specialism_id)
        //     ->withCount(['requests as requests_1' => function ($query) {
        //         $query->where('type', 1);
        //     }, 'requests as requests_2' => function ($query) {
        //         $query->where('type', 2);
        //     }])

        return view('hallOfFame', [
            "top_monsters" => $top_monsters,
            // "user_id" => $user_id
        ]);
    }
}
