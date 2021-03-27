<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Repositories\DBMonsterRepository;
use App\Repositories\DBUserRepository;
use App\Services\TimeService;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redis;

class monsterGridController extends Controller
{
    protected $DBMonsterRepo;
    protected $DBUserRepo;

    public function __construct(Request $request, 
    DBMonsterRepository $DBMonsterRepo, 
    DBUserRepository $DBUserRepo,
    TimeService $TimeService)
    {
        $this->DBMonsterRepo = $DBMonsterRepo;
        $this->DBUserRepo = $DBUserRepo;
        $this->TimeService = $TimeService;
    }

    public function index(Request $request, $page_type = 'gallery', $selected_user_id = NULL)
    {

        $user_stats = NULL;
        $following = 0;
        $following_count = 0;
        $followers_count = 0;
        $my_page = false;
        if ($selected_user_id  || Auth::check()){

            $group_id = 0;
            if (!$selected_user_id) {
                $selected_user_id = Auth::User()->id;
                $my_page = true;
            }
            $selected_user = $this->DBUserRepo->find($selected_user_id);

            $following = false;
            if (Auth::check()){
                $current_user_id = Auth::User()->id;
                $current_user = $this->DBUserRepo->find($current_user_id);
                $followed_user_ids = $current_user->followingUsers->pluck('followed_user_id')->toArray();
                $following = in_array($selected_user->id, $followed_user_ids);
            }
            $following_count = count($selected_user->followingUsers);
            $followers_count = count($selected_user->followedByUsers);
            
            if ($page_type == 'mymonsters' || $page_type == 'usermonsters'){
                $user_stats = $this->DBUserRepo->getStats($selected_user_id);
            }

        } else {
            $selected_user = NULL;
            $session = $request->session();
            $group_id = $session->get('group_id') ? : 0;
        }

        return view('monsterGrid', [
            "user" => $selected_user,
            "group_id" => $group_id,
            "page_type" => $page_type,
            "my_page" => $my_page ? 1 : 0,
            "stats" => $user_stats,
            "following" => $following ? 1 : 0,
            "following_count" => $following_count,
            "followers_count" => $followers_count,
        ]);
    }

    public function getData(Request $request){
        $action = $request->action;
        $search = $request->search;
        $sort_by = $request->sortBy;
        $time_filter = $request->timeFilter;
        $favourites_only = $request->favouritesOnly;
        $followed_only = $request->followedOnly;
        $nsfw_only = $request->nsfwOnly;
        $unrated_only = $request->unratedOnly;
        $my_monsters_only = $request->myMonstersOnly;
        $user_monsters_only = $request->userMonstersOnly;
        $skip = $request->skip;
        if (Auth::check()){
            $user_id = Auth::User()->id;
            $user = $this->DBUserRepo->find($user_id);
            $group_id = 0;
        } else {
            $user = NULL;
            $session = $request->session();
            $group_id = $session->get('group_id') ? : 0;
        }
        if ( $action == 'getGalleryMonsters'){
            $date = $this->TimeService->getDateFromTimeFilter($time_filter);
            $monsters = $this->DBMonsterRepo->getMonsters($user, $group_id, $search, $favourites_only, 
                $followed_only, $nsfw_only, $unrated_only, $my_monsters_only, $user_monsters_only,
                $sort_by, $date, $skip);

            if ($skip % 80 == 0){
                $all_monster_ids = $this->DBMonsterRepo->getMonsters($user, $group_id, $search, $favourites_only, 
                    $followed_only, $nsfw_only, $unrated_only, $my_monsters_only, $user_monsters_only, 
                    $sort_by, $date, $skip, true);

                if ($skip == 0){
                    if ($user && $user->id == 1){
                        Redis::set('gallery_monster_ids', implode(',', $all_monster_ids));
                    }
                } else{
                    //Append new monster_ids to array 
                    $cached_monster_ids = Redis::get('gallery_monster_ids');
                    $cached_monster_ids = explode(',',$cached_monster_ids);
                    $all_monster_ids = array_merge($cached_monster_ids, $all_monster_ids);
                    if ($user && $user->id == 1){
                        Redis::set('gallery_monster_ids', implode(',', $all_monster_ids));
                    }
                }
            }
            return $monsters;
        }
    }
}