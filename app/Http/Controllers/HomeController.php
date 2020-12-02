<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\InfoMessageClosed;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use App\Repositories\DBMonsterRepository;
use App\Repositories\DBUserRepository;
use App\Repositories\DBTrophyRepository;
use App\Repositories\DBTrophyTypeRepository;
use App\Repositories\DBInfoMessageRepository;
use App\Repositories\DBProfanityRepository;
use App\Repositories\DBStatsRepository;
use App\Repositories\DBAuditRepository;

use App\Services\TrophyService;

class HomeController extends Controller
{
    protected $DBMonsterRepo;
    protected $DBUserRepo;
    protected $DBTrophyRepo;
    protected $DBTrophyTypeRepo;
    protected $DBInfoMessageRepo;
    protected $DBProfanityRepo;
    protected $DBStatsRepo;
    protected $DBAuditRepo;
    protected $TrophyService;
    private $user;
    private $user_id;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, 
        DBMonsterRepository $DBMonsterRepo, 
        DBUserRepository $DBUserRepo,
        DBTrophyRepository $DBTrophyRepo,
        DBTrophyTypeRepository $DBTrophyTypeRepo,
        DBInfoMessageRepository $DBInfoMessageRepo,
        DBProfanityRepository $DBProfanityRepo,
        DBStatsRepository $DBStatsRepo,
        DBAuditRepository $DBAuditRepo,
        TrophyService $TrophyService)
    {
        $this->middleware(['auth','verified', function($request, $next) 
            use ($DBMonsterRepo,$DBUserRepo,$DBTrophyRepo,
            $DBTrophyTypeRepo,$DBInfoMessageRepo,$DBProfanityRepo,
            $DBStatsRepo, $DBAuditRepo, $TrophyService){

            $this->DBMonsterRepo = $DBMonsterRepo;
            $this->DBUserRepo = $DBUserRepo;
            $this->DBTrophyRepo = $DBTrophyRepo;
            $this->DBTrophyTypeRepo = $DBTrophyTypeRepo;
            $this->DBInfoMessageRepo = $DBInfoMessageRepo;
            $this->DBProfanityRepo = $DBProfanityRepo;
            $this->DBStatsRepo = $DBStatsRepo;
            $this->DBAuditRepo = $DBAuditRepo;
            $this->TrophyService = $TrophyService;
        
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
    public function index()
    {
        $flagged_monsters = $this->DBMonsterRepo->getFlaggedMonsters();
        $flagged_comment_monsters = $this->DBMonsterRepo->getFlaggedCommentMonsters();
        $monitored_monsters = $this->DBMonsterRepo->getMonitoredMonsters();
        $unfinished_monsters = $this->DBMonsterRepo->getUnfinishedMonsters($this->user);
        $info_messages = $this->DBInfoMessageRepo->getActiveMessages($this->user->id);
        $leader_board_stats = $this->DBStatsRepo->getLeaderBoardStats();
        $audit_actions = $this->DBAuditRepo->getActions($this->user);

        return view('home', [
            "unfinished_monsters" => $unfinished_monsters,
            "flagged_monsters" => $flagged_monsters,
            "flagged_comment_monsters" => $flagged_comment_monsters,
            "monitored_monsters" => $monitored_monsters,
            "audit_actions" => $audit_actions,
            "user_id" => $this->user->id,
            "info_messages" => $info_messages,
            "user_is_vip" => $this->user->vip,
            "leader_board_stats" => $leader_board_stats
        ]);
    }

    public function fetchMonsters(){
        $unfinished_monsters = $this->DBMonsterRepo->getUnfinishedMonsters($this->user);
        return $unfinished_monsters;
    }
    public function getNewUserChanges(){
        $newChanges = $this->DBAuditRepo->getActions($this->user, 10);
        return $newChanges;
    }

    public function create(Request $request)
    {
        $name = trim($request->name);
        if ($name == "" || strlen($name) > 20) die();

        $monster = $this->DBMonsterRepo->getInstance();
        $monster->name = $name;
        $monster->auth = $this->DBMonsterRepo->isAuth($request->level, $this->user);
        $monster->vip = $this->DBMonsterRepo->isVIP($request->level, ($this->user && $this->user->vip));
        $monster->nsfw = $this->DBProfanityRepo->isNSFW($name) ? 1 : ($request->nsfw ? 1 : 0);
        $monster->nsfl = $this->DBProfanityRepo->isNSFL($name);

        $monster->status = 'awaiting head';
        $monster->save();

        header('Location: /canvas/'. $monster->id);
        die();

        // return response()->json([
        //     'id' => $monster->id
        // ]);
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

            $users = $this->DBUserRepo->getAllActiveUsers(true,true,true,true);
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
           
        }
    } 
}