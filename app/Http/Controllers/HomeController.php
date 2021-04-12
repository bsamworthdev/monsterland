<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InfoMessageClosed;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use App\Repositories\DBMonsterRepository;
use App\Repositories\DBMonsterSegmentRepository;
use App\Repositories\DBUserRepository;
use App\Repositories\DBTrophyRepository;
use App\Repositories\DBTrophyTypeRepository;
use App\Repositories\DBInfoMessageRepository;
use App\Repositories\DBProfanityRepository;
use App\Repositories\DBStatsRepository;
use App\Repositories\DBAuditRepository;
use App\Repositories\DBRandomWordsRepository;
use App\Services\TrophyService;
use App\Services\RedisService;

class HomeController extends Controller
{
    protected $DBMonsterRepo;
    protected $DBMonsterSegmentRepo;
    protected $DBUserRepo;
    protected $DBTrophyRepo;
    protected $DBTrophyTypeRepo;
    protected $DBInfoMessageRepo;
    protected $DBProfanityRepo;
    protected $DBStatsRepo;
    protected $DBAuditRepo;
    protected $DBRandomWordsRepo;
    protected $TrophyService;
    protected $RedisService;
    private $user;
    private $user_id;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, 
        DBMonsterRepository $DBMonsterRepo, 
        DBMonsterSegmentRepository $DBMonsterSegmentRepo,
        DBUserRepository $DBUserRepo,
        DBTrophyRepository $DBTrophyRepo,
        DBTrophyTypeRepository $DBTrophyTypeRepo,
        DBInfoMessageRepository $DBInfoMessageRepo,
        DBProfanityRepository $DBProfanityRepo,
        DBStatsRepository $DBStatsRepo,
        DBAuditRepository $DBAuditRepo,
        DBRandomWordsRepository $DBRandomWordsRepo,
        TrophyService $TrophyService,
        RedisService $RedisService)
    {
        $this->middleware(['auth','verified', function($request, $next) 
            use ($DBMonsterRepo,$DBMonsterSegmentRepo,$DBUserRepo,$DBTrophyRepo,
            $DBTrophyTypeRepo,$DBInfoMessageRepo,$DBProfanityRepo,
            $DBStatsRepo, $DBAuditRepo, $DBRandomWordsRepo, $TrophyService, $RedisService){

            $this->DBMonsterRepo = $DBMonsterRepo;
            $this->DBMonsterSegmentRepo = $DBMonsterSegmentRepo;
            $this->DBUserRepo = $DBUserRepo;
            $this->DBTrophyRepo = $DBTrophyRepo;
            $this->DBTrophyTypeRepo = $DBTrophyTypeRepo;
            $this->DBInfoMessageRepo = $DBInfoMessageRepo;
            $this->DBProfanityRepo = $DBProfanityRepo;
            $this->DBStatsRepo = $DBStatsRepo;
            $this->DBAuditRepo = $DBAuditRepo;
            $this->DBRandomWordsRepo = $DBRandomWordsRepo;
            $this->TrophyService = $TrophyService;
            $this->RedisService = $RedisService;
        
            $user_id = Auth::User()->id;
            $this->user = $this->DBUserRepo->find($user_id);    
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
        $flagged_monsters = $this->DBMonsterRepo->getFlaggedMonsters();
        $flagged_comment_monsters = $this->DBMonsterRepo->getFlaggedCommentMonsters();
        $monitored_monsters = $this->DBMonsterRepo->getMonitoredMonsters();
        $take_two_monsters = $this->DBMonsterRepo->getTakeTwoMonsters();
        $unfinished_monsters = $this->DBMonsterRepo->getUnfinishedMonsters($this->user);
        $info_messages = $this->DBInfoMessageRepo->getActiveMessages($this->user->id);
        $leader_board_stats = $this->DBStatsRepo->getLeaderBoardStats();
        $audit_actions = $this->DBAuditRepo->getActions($this->user);
        $random_monster = $this->DBMonsterRepo->getRandomMonster();
        $random_words = $this->DBRandomWordsRepo->getAll();
        $daily_action_count = $this->DBAuditRepo->getDailyActionCount();

        $session = $request->session();
        $session_id = $session->getId();
        // $request->session()->forget('gallery_title');
        // $request->session()->forget('gallery_monster_ids');
        // Redis::del('gallery_title');
        // Redis::del('gallery_monster_ids');
        $this->RedisService->delete($session_id.'_gallery_title');
        $this->RedisService->delete($session_id.'_gallery_monster_ids');

        return view('home', [
            "unfinished_monsters" => $unfinished_monsters,
            "flagged_monsters" => $flagged_monsters,
            "flagged_comment_monsters" => $flagged_comment_monsters,
            "monitored_monsters" => $monitored_monsters,
            "take_two_monsters" => $take_two_monsters,
            "audit_actions" => $audit_actions,
            "user_id" => $this->user->id,
            "info_messages" => $info_messages,
            "user_is_vip" => $this->user->vip,
            "user_allows_nsfw" => $this->user->allow_nsfw,
            "leader_board_stats" => $leader_board_stats,
            "random_monster" => $random_monster,
            "random_words" => $random_words,
            "daily_action_count" => $daily_action_count
        ]);
    }

    public function fetchMonsters(){
        $unfinished_monsters = $this->DBMonsterRepo->getUnfinishedMonsters($this->user);
        return $unfinished_monsters;
    }

    public function fetchRandomMonster(){
        $random_monster = $this->DBMonsterRepo->getRandomMonster();
        return $random_monster;
    }

    public function getDailyActionCount(){
        return $this->DBAuditRepo->getDailyActionCount();
    }

    public function getNewUserChanges(){
        $newChanges = $this->DBAuditRepo->getActions($this->user, 10);
        return $newChanges;
    }

    public function create(Request $request)
    {
        $name = trim($request->name);
        if ($name == "" || strlen($name) > 26) die();

        $monster = $this->DBMonsterRepo->getInstance();
        $monster->name = $name;
        $monster->auth = $this->DBMonsterRepo->isAuth($request->level, $this->user);
        $monster->vip = $this->DBMonsterRepo->isVIP($request->level, ($this->user && $this->user->vip));
        $monster->nsfw = $this->DBProfanityRepo->isNSFW($name) ? 1 : ($request->nsfw ? 1 : 0);
        $monster->nsfl = $this->DBProfanityRepo->isNSFL($name);
        $monster->prevent_peek = $request->prevent_peek ? 1 : 0;

        $monster->status = 'awaiting head';
        $monster->save();

        header('Location: /canvas/'. $monster->id);
        die();

        // return response()->json([
        //     'id' => $monster->id
        // ]);
    }
    public function awardWeeklyTrophies(Request $request){
        if ($this->user->id != 1) die();
        $monsterIds = [];
        $monsterIds['first'] = $request->firstPlace;
        $monsterIds['second'] = $request->secondPlace;
        $monsterIds['third'] = $request->thirdPlace;
        $this->DBTrophyRepo->awardWeeklyTrophies($monsterIds);
        $this->DBInfoMessageRepo->addWeeklyTrophiesMessage($monsterIds);

        header('Location: /home');
        die();
    }

    public function update(Request $request){
        $action = $request->action;

        if ($action == 'unblock'){
            if ($this->user->id != 1) die();

            $monsters = $this->DBMonsterRepo->cancelInactiveMonsters();
        } elseif ($action == 'createpngs'){
            if ($this->user->id != 1) die();

            $monsters = $this->DBMonsterRepo->createMissingMonsterImages();
        } elseif ($action == 'closeInfoMessage'){
            $message_id = $request->message_id;

            $infoMessageClosed = new InfoMessageClosed;
            $infoMessageClosed->user_id = $this->user->id;
            $infoMessageClosed->info_message_id = $message_id;
            $infoMessageClosed->save();
        } elseif ($action == 'awardtrophies'){
            if ($this->user->id != 1) die();

            $users = $this->DBUserRepo->getAllActiveUsers(true,true,true,true,true,2);
            $trophyTypes = $this->DBTrophyTypeRepo->getAll();

            foreach($trophyTypes as $trophyType){
                foreach($users as $user){
                    if ($this->TrophyService->trophyConditionSatisfied($trophyType, $user)){
                        if (!$this->DBUserRepo->hasTrophyOfType($user, $trophyType)){
                            $this->DBTrophyRepo->awardTrophy($user, $trophyType);
                        }
                    }
                }
            }
        } elseif ($action == 'removeOldB64Images'){
            if ($this->user->id != 1) die();

            $this->DBMonsterRepo->removeOldB64Images();
            
        } elseif ($action == 'convertB64Images'){
            if ($this->user->id != 1) die();

            $this->DBMonsterSegmentRepo->convertB64Images();
            
        } elseif ($action == 'setHasUsedApp'){
            $key = $request->key;
            $users = $this->DBUserRepo->setHasUsedApp($this->user->id, $key);
        } elseif ($action == 'createMissingThumbnails'){
            if ($this->user->id != 1) die();

            $monsters = $this->DBMonsterRepo->createMissingThumbnailImages();
        }
    } 
}