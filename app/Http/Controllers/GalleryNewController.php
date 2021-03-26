<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Repositories\DBMonsterRepository;
use App\Repositories\DBUserRepository;
use App\Services\TimeService;
use Illuminate\Support\Facades\Log;

class GalleryNewController extends Controller
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

    public function index(Request $request, $page_type = 'gallery')
    {

        $user_stats = NULL;
        $following = 0;
        $following_count = 0;
        $followers_count = 0;
        if (Auth::check()){
            $user_id = Auth::User()->id;
            $user = $this->DBUserRepo->find($user_id);
            $group_id = 0;

            if (!$user) return back()->with('error', 'User not found');

            $followed_user_ids = $user->followingUsers->pluck('followed_user_id')->toArray();
            $following = in_array($user_id, $followed_user_ids);
            $following_count = count($user->followingUsers);
            $followers_count = count($user->followedByUsers);
            
            if ($page_type == 'mymonsters'){
                $user_stats = $this->DBUserRepo->getStats($user_id);
            }

        } else {
            $user = NULL;
            $session = $request->session();
            $group_id = $session->get('group_id') ? : 0;
        }

        return view('galleryNew', [
            "user" => $user,
            "group_id" => $group_id,
            "page_type" => $page_type,
            "stats" => $user_stats,
            "following" => $following,
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
            return $this->DBMonsterRepo->getMonsters($user, $group_id, $search, $favourites_only, 
                $followed_only, $nsfw_only, $unrated_only, $my_monsters_only, $sort_by, $date, $skip);
        }
    }
}
