<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InfoMessage;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

use App\Repositories\DBMonsterRepository;
use App\Repositories\DBUserRepository;
use App\Repositories\DBInfoMessageRepository;
use App\Repositories\DBProfanityRepository;
use App\Repositories\DBAuditRepository;
use App\Repositories\DBStatsRepository;
use App\Services\RedisService;

class NonAuthHomeController extends Controller
{
    protected $DBMonsterRepo;
    protected $DBUserRepo;
    protected $DBInfoMessageRepo;
    protected $DBProfanityRepo;
    protected $DBAuditRepo;
    protected $DBStatsRepo;
    protected $RedisService;

    public function __construct(Request $request, 
        DBMonsterRepository $DBMonsterRepo, 
        DBUserRepository $DBUserRepo,
        DBInfoMessageRepository $DBInfoMessageRepo,
        DBProfanityRepository $DBProfanityRepo,
        DBAuditRepository $DBAuditRepo,
        DBStatsRepository $DBStatsRepo,
        RedisService $RedisService)
    {
        $this->middleware(['guest', function($request, $next) 
            use ($DBMonsterRepo,$DBUserRepo,$DBInfoMessageRepo,
            $DBProfanityRepo,$DBAuditRepo,$DBStatsRepo,$RedisService){

            $this->DBMonsterRepo = $DBMonsterRepo;
            $this->DBUserRepo = $DBUserRepo;
            $this->DBInfoMessageRepo = $DBInfoMessageRepo;
            $this->DBProfanityRepo = $DBProfanityRepo;
            $this->DBAuditRepo = $DBAuditRepo;
            $this->DBStatsRepo = $DBStatsRepo;
            $this->RedisService = $RedisService;
         
            return $next($request);
        }]); 
        
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {

        //Users
        $session = $request->session();
        $session_id = $session->getId();

        //Group variables
        $group_id = $session->get('group_id') ? : 0;
        $group_name = $session->get('group_name') ? : '';
        $group_username = $session->get('group_username') ? : '';

        $unfinished_monsters = $this->DBMonsterRepo->getUnfinishedMonsters(NULL, $group_id);
        $info_messages = $this->DBInfoMessageRepo->getActiveMessages();
        //$audit_actions = $this->DBAuditRepo->getActions();
        $daily_action_count = $this->DBAuditRepo->getDailyActionCount();

        //Get cached stats
        if ($this->RedisService->exists(date('Ymd').'_overallstats') && $this->RedisService->get('stats_need_updating') == false){
            $stats = $this->RedisService->get(date('Ymd').'_overallstats');
        } else {
            $stats =  $this->DBStatsRepo->getOverallStats();
            $this->RedisService->set(date('Ymd').'_overallstats', $stats);
            $this->RedisService->set('stats_need_updating', false);
        }

        // $request->session()->forget('gallery_title');
        // $request->session()->forget('gallery_monster_ids');
        // Redis::del('gallery_title');
        // Redis::del('gallery_monster_ids');
        // $this->RedisService->delete($session_id.'_gallery_title');
        // $this->RedisService->delete($session_id.'_gallery_monster_ids');
        $this->RedisService->delete($session_id, false);

        return view('homeNonAuth', [
            "unfinished_monsters" => $unfinished_monsters,
            "session_id" => $session_id,
            "info_messages" => $info_messages,
            //"audit_actions" => $audit_actions,
            "group_mode" => $group_id > 0 ? 1 : 0,
            "group_name" => $group_name,
            "group_username" => $group_username,
            "daily_action_count" => $daily_action_count,
            "overall_stats" => $stats
        ]);
    }

    public function fetchMonsters(Request $request){
        $session = $request->session();

        //Group variables
        $group_id = $session->get('group_id') ? : 0;
        $unfinished_monsters = $this->DBMonsterRepo->getUnfinishedMonsters(NULL, $group_id);

        return $unfinished_monsters;
    }

    public function getDailyActionCount(){
        return $this->DBAuditRepo->getDailyActionCount();
    }

    public function create(Request $request)
    {
        $name = $request->name;
        $session = $request->session();

        if ($name == "" || strlen($name) > 20) die();
       
        $monster = $this->DBMonsterRepo->getInstance();
        $monster->auth = 0;
        $monster->status = 'awaiting head';
        $monster->group_id = $session->get('group_id') ? : 0;;
        $monster->name = $name;
        $monster->nsfw = $this->DBProfanityRepo->isNSFW($name) ? 1 : ($request->nsfw ? 1 : 0);
        $monster->nsfl = $this->DBProfanityRepo->isNSFL($name);
        $monster->save();

        return response()->json([
            'id' => $monster->id
        ]);
    }
}
