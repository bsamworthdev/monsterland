<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Repositories\DBMonsterRepository;
use App\Repositories\DBUserRepository;
use App\Services\TimeService;

class MyMonstersController extends Controller
{

    protected $DBMonsterRepo;
    protected $DBUserRepo;
    protected $TimeService;

    public function __construct(Request $request, 
    DBMonsterRepository $DBMonsterRepo, 
    DBUserRepository $DBUserRepo,
    TimeService $TimeService)
    {
        $this->middleware(['auth','verified']);
        $this->DBMonsterRepo = $DBMonsterRepo;
        $this->DBUserRepo = $DBUserRepo;
        $this->TimeService = $TimeService;
    }

    public function index($user_id = NULL, $page = 0, $time_filter = 'week', $search = '')
    {
        $current_user = Auth::check() ? Auth::User() : NULL;
        $selected_user=$this->DBUserRepo->find($user_id);
        $date = $this->TimeService->getDateFromTimeFilter($time_filter);
        $top_monsters =$this->DBMonsterRepo->getTopMonstersByUser($selected_user, $current_user, $date, $search, $page);

        return view('myMonsters', [
            "top_monsters" => $top_monsters,
            "page" => $page,
            "time_filter" => $time_filter,
            "is_my_page" => ($current_user && $user_id == $current_user->id),
            "user" => $selected_user,
            "search" => $search
        ]);
    }
}
