<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Repositories\DBMonsterRepository;
use App\Repositories\DBUserRepository;
use App\Services\TimeService;

class HallOfFameController extends Controller
{
    protected $DBMonsterRepo;
    protected $DBUserRepo;

    public function __construct(Request $request, 
    DBMonsterRepository $DBMonsterRepo, 
    DBUserRepository $DBUserRepo,
    TimeService $TimeService)
    {
        $this->middleware(['guest']);
        $this->DBMonsterRepo = $DBMonsterRepo;
        $this->DBUserRepo = $DBUserRepo;
        $this->TimeService = $TimeService;
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

        $date = $this->TimeService->getDateFromTimeFilter($time_filter);
        
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
