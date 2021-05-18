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
        //$this->middleware(['auth','verified']);
        $this->DBMonsterRepo = $DBMonsterRepo;
        $this->DBUserRepo = $DBUserRepo;
        $this->TimeService = $TimeService;
    }

    public function index($user_id = NULL, $page = 0, $time_filter = 'ever', $search = '')
    {
        $current_user = NULL;
        $following = false;
        if (Auth::check()){
            $current_user = $this->DBUserRepo->find(Auth::User()->id, ['followingUsers','followedByUsers']);
            $followed_user_ids = $current_user->followingUsers->pluck('followed_user_id')->toArray();
            $following = in_array($user_id, $followed_user_ids);
        } 
        $selected_user=$this->DBUserRepo->find($user_id);
        if (!$selected_user) return back()->with('error', 'User not found');

        $user_stats = $this->DBUserRepo->getStats($user_id);
        $date = $this->TimeService->getDateFromTimeFilter($time_filter);
        $top_monsters =$this->DBMonsterRepo->getTopMonstersByUser($selected_user, $current_user, $date, $search, $page);

        return view('myMonsters', [
            "top_monsters" => $top_monsters,
            "page" => $page,
            "time_filter" => $time_filter,
            "is_my_page" => ($current_user && $user_id == $current_user->id),
            "user" => $selected_user,
            "stats" => $user_stats,
            "search" => $search,
            "page_type" => 'myMonsters',
            "following" => $following ? 1 : 0,
            "following_count" => count($selected_user->followingUsers),
            "followers_count" => count($selected_user->followedByUsers),
        ]);
    }

    public function update(Request $request){
        $action = $request->action;
        if ($action == 'gildUser'){
            if (Auth::User()->id == 1){
                $user_id = $request->user_id;
                $this->DBUserRepo->gildUser($user_id);
            }
        } elseif ($action == 'ungildUser'){
            if (Auth::User()->id == 1){
                $user_id = $request->user_id;
                $this->DBUserRepo->ungildUser($user_id);
            }
        } elseif ($action == 'monitorUser'){
            if (Auth::User()->id == 1){
                $user_id = $request->user_id;
                $this->DBUserRepo->monitorUser($user_id);
            }
        } elseif ($action == 'unmonitorUser'){
            if (Auth::User()->id == 1){
                $user_id = $request->user_id;
                $this->DBUserRepo->unmonitorUser($user_id);
            }
        } elseif ($action == 'followUser'){
            if (Auth::check()){
                $follower_user_id = Auth::User()->id;
                $followed_user_id = $request->user_id;
                $this->DBUserRepo->followUser($follower_user_id, $followed_user_id);
            }
        } elseif ($action == 'unfollowUser'){
            if (Auth::check()){
                $follower_user_id = Auth::User()->id;
                $followed_user_id = $request->user_id;
                $this->DBUserRepo->unfollowUser($follower_user_id, $followed_user_id);
            }
        } 
    }
}
