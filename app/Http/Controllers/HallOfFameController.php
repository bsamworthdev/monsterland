<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\http\Repositories\DBMonsterRepository;
use App\http\Repositories\DBUserRepository;

class HallOfFameController extends Controller
{
    protected $DBMonsterRepo;
    protected $DBUserRepo;

    public function __construct(Request $request, 
    DBMonsterRepository $DBMonsterRepo, 
    DBUserRepository $DBUserRepo)
    {
        $this->middleware(['auth','verified']);
        $this->DBMonsterRepo = $DBMonsterRepo;
        $this->DBUserRepo = $DBUserRepo;
    }

    public function index(Request $request, $page = 0, $time_filter = 'week', $search = '')
    {
        if (Auth::check()){
            $user_id = Auth::User()->id;
            $user = $this->DBUserRepo->find($user_id);
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
        
        $top_monsters = $this->DBMonsterRepo->getTopMonsters($user, $date, $group_id, $search, $page);

        return view('hallOfFame', [
            "user" => $user,
            "top_monsters" => $top_monsters,
            "page" => $page,
            "time_filter" => $time_filter,
            "search" => $search
        ]);
    }
}
