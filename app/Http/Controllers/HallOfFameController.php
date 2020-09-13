<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Monster;
use App\User;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class HallOfFameController extends Controller
{
    public function index(Request $request, $page = 0, $time_filter = 'week')
    {
        if (Auth::check()){
            $user_id = Auth::User()->id;
            $user = User::find($user_id);
            $group_id = 0;
        } else {
            $user = NULL;
            $session = $request->session();
            $group_id = $session->get('group_id') ? : 0;
        }

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
            ->where('status', 'complete')
            ->where('created_at','>=',$date)
            ->where('nsfl', '0')
            ->when(!$user || $user->allow_nsfw == 0, function($q) {
                $q->where('nsfw', '0');
            })
            ->where('group_id', $group_id)
            ->having('average_rating', '>', 0)
            ->having('ratings_count', '>', 0)
            ->orderBy('average_rating','desc')
            ->orderBy('ratings_count', 'desc')
            ->orderBy('name', 'asc')
            ->skip($page*8)
            ->take(8)
            ->get();

        // Model::where('types_id', $specialism_id)
        //     ->withCount(['requests as requests_1' => function ($query) {
        //         $query->where('type', 1);
        //     }, 'requests as requests_2' => function ($query) {
        //         $query->where('type', 2);
        //     }])

        return view('hallOfFame', [
            "top_monsters" => $top_monsters,
            "page" => $page,
            "time_filter" => $time_filter
            // "user_id" => $user_id
        ]);
    }
}
