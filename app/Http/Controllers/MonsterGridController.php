<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Repositories\DBMonsterRepository;
use App\Repositories\DBUserRepository;
use App\Services\TimeService;
use App\Services\RedisService;
use Illuminate\Support\Facades\Log;

class monsterGridController extends Controller
{
    protected $DBMonsterRepo;
    protected $DBUserRepo;
    protected $RedisService;

    public function __construct(Request $request, 
    DBMonsterRepository $DBMonsterRepo, 
    DBUserRepository $DBUserRepo,
    TimeService $TimeService,
    RedisService $RedisService)
    {
        $this->DBMonsterRepo = $DBMonsterRepo;
        $this->DBUserRepo = $DBUserRepo;
        $this->TimeService = $TimeService;
        $this->RedisService = $RedisService;
    }

    public function index(Request $request, $page_type = 'gallery', $selected_user_id = 0, $search = '')
    {
        $user_stats = NULL;
        $following = 0;
        $following_count = 0;
        $followers_count = 0;
        $my_page = false;
        $session = $request->session();
        $session_id = $session->getId();
        $filters = "";

        if ($search){
            //Forced search
            if (str_starts_with($search, 'tag:')) $search = '#'.explode(":",$search)[1];
            $filters = json_encode([
                'search' => $search,
                'time_filter' => 'ever'
            ]);
            $key = $session_id.'_'.$page_type.(($selected_user_id && !$my_page) ? '_'.$selected_user_id : '').'_gallery_filters';
            $this->RedisService->set($key, $filters);
            return redirect('/monstergrid');
        } 

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
        }
        
        $group_id = $session->get('group_id') ? : 0;
        $group_name = $session->get('group_name') ? : '';

        $key = $session_id.'_'.$page_type.(($selected_user_id && !$my_page) ? '_'.$selected_user_id : '').'_gallery_filters';
        $filters = $this->RedisService->get($key);

        return view('monsterGrid', [
            "user" => $selected_user,
            "group_id" => $group_id,
            "group_name" => $group_name,
            "page_type" => $page_type,
            "my_page" => $my_page ? 1 : 0,
            "stats" => $user_stats,
            "following" => $following ? 1 : 0,
            "following_count" => $following_count,
            "followers_count" => $followers_count,
            "filters" => $filters
        ]);
    }

    public function getData(Request $request){
        $session = $request->session();
        $session_id = $session->getId();
        
        $action = $request->action;
        $search = $request->search;
        $sort_by = $request->sortBy;
        $page_title = $request->pageTitle;
        $time_filter = $request->timeFilter;
        $favourites_only = $request->favouritesOnly;
        $followed_only = $request->followedOnly;
        $nsfw_only = $request->nsfwOnly;
        $unrated_only = $request->unratedOnly;
        $my_monsters_only = $request->myMonstersOnly;
        $user_monsters_only = $request->userMonstersOnly;
        $skip = $request->skip;
        $page_type = $request->pageType;
        $user_name = $request->userName;
        $group_id = $session->get('group_id') ? : 0;
        $group_name = $session->get('group_name') ? : '';
        
        $filters = [
            'search' => $search,
            'group_id' => $group_id,
            'group_name' => $group_name,
            'sort_by' => $sort_by,
            'time_filter' => $time_filter,
            'favourites_only' => $favourites_only,
            'followed_only' => $followed_only,
            'nsfw_only' => $nsfw_only,
            'unrated_only' => $unrated_only,
            'my_monsters_only' => $my_monsters_only,
            'user_monsters_only' => $user_monsters_only,
            'user_name' => $user_name
        ];

        if (Auth::check()){
            $user_id = Auth::User()->id;
            $user = $this->DBUserRepo->find($user_id);
        } else {
            $user = NULL;
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
                    // Redis::set('gallery_monster_ids', implode(',', $all_monster_ids));
                    if ($page_type == 'favourites'){
                        $title = "My Favourites";
                    }elseif ($page_type == 'halloffame'){
                        $title = "Hall Of Fame";
                    }elseif ($page_type == 'mymonsters'){
                        $title = "My Monsters";
                    }elseif ($page_type == 'usermonsters'){
                        $title = $user_name;
                    }else {
                        $title = "Gallery";
                    }

                    // $request->session()->put('gallery_title', $title);
                    // $request->session()->put('gallery_monster_ids', implode(',', $all_monster_ids));
                    // Redis::set('gallery_title', $title);
                    // Redis::set('gallery_monster_ids', implode(',', $all_monster_ids));
                    $this->RedisService->set($session_id.'_gallery_title', $title);
                    $this->RedisService->set($session_id.'_gallery_monster_ids', implode(',', $all_monster_ids));
                    $this->RedisService->set($session_id.'_'.$page_type.($user_monsters_only ? '_'.$user_monsters_only : '').'_gallery_filters', json_encode($filters));
                } else{
                    //Append new monster_ids to array 
                    // $cached_monster_ids = $request->session()->get('gallery_monster_ids');
                    // $cached_monster_ids = Redis::get('gallery_monster_ids');
                    $cached_monster_ids = $this->RedisService->get($session_id.'_gallery_monster_ids');
                    $cached_monster_ids = explode(',',$cached_monster_ids);
                    $all_monster_ids = array_merge($cached_monster_ids, $all_monster_ids);
                    // $request->session()->put('gallery_monster_ids', implode(',', $all_monster_ids));
                    // Redis::set('gallery_monster_ids', implode(',', $all_monster_ids));
                    $this->RedisService->set($session_id.'_gallery_monster_ids', implode(',', $all_monster_ids));
                }
            }
            return $monsters;
        }
    }
}
