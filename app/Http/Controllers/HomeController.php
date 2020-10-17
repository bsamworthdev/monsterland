<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Profanity;
use App\InfoMessage;
use App\InfoMessageClosed;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;

use App\http\Repositories\DBMonsterRepository;
use App\http\Repositories\DBUserRepository;
use App\http\Repositories\DBTrophyRepository;
use App\http\Repositories\DBTrophyTypeRepository;

class HomeController extends Controller
{
    protected $DBMonsterRepo;
    protected $DBUserRepo;
    protected $DBTrophyRepo;
    protected $DBTrophyTypeRepo;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, 
        DBMonsterRepository $DBMonsterRepo, 
        DBUserRepository $DBUserRepo,
        DBTrophyRepository $DBTrophyRepo,
        DBTrophyTypeRepository $DBTrophyTypeRepo)
    {
        $this->middleware(['auth','verified']);
        $this->DBMonsterRepo = $DBMonsterRepo;
        $this->DBUserRepo = $DBUserRepo;
        $this->DBTrophyRepo = $DBTrophyRepo;
        $this->DBTrophyTypeRepo = $DBTrophyTypeRepo;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user_id = Auth::User()->id;
        $user = $this->DBUserRepo->find($user_id);
        $unfinished_monsters = $this->DBMonsterRepo->getUnfinishedMonsters($user);
        
            $info_messages = InfoMessage::where('start_date', '<', DB::raw('now()'))
                ->where('end_date', '>' , DB::raw('now()'))
                ->where(function ($q) use($user_id){
                    $q->whereNull('user')
                    ->orWhere('user', $user_id);
                })
                ->whereDoesntHave('closed_info_messages', function($q) use($user_id){
                    $q->where('user_id', $user_id);
                })
                ->get();

        return view('home', [
            "unfinished_monsters" => $unfinished_monsters,
            "user_id" => $user_id,
            "info_messages" => $info_messages,
            "user_is_vip" => $user->vip
        ]);
    }

    public function fetchMonsters(){
        $user_id = Auth::User()->id;
        $user = $this->DBUserRepo->find($user_id);
        $unfinished_monsters = $this->DBMonsterRepo->getUnfinishedMonsters($user);

        return $unfinished_monsters;
    }

    public function create(Request $request)
    {
        $monster = $this->DBMonsterRepo->getInstance();
        $name = trim($request->name);

        if ($name == "" || strlen($name) > 20){
            die();
        } else {
            $monster->name = $name;
        }

        $user_id = Auth::User()->id;
        $user = $this->DBUserRepo->find($user_id);

        switch ($request->level){
            case 'basic':
                $monster->auth = 0;
                $monster->vip = 0;
                break;
            case 'standard':
                $monster->auth = 1;
                $monster->vip = 0;
                break;
            case 'pro':
                if (!$user->vip) return false;
                $monster->auth = 1;
                $monster->vip = 1;
                break;
        }
        $monster->nsfw = $request->nsfw ? 1 : 0;

        $profanity = Profanity::whereRaw('"'.$name.'" like CONCAT("%", word, "%")')
            ->orderBy('nsfl','desc')
            ->orderBy('nsfw','desc')
            ->get();

        if (count($profanity) > 0) {
            if ($profanity[0]->nsfw){
                $monster->nsfw = 1;
            }
            if ($profanity[0]->nsfl){
                $monster->nsfl = 1;
            }
        }

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
        $user_id = Auth::User()->id;

        if ($action == 'unblock'){
            if ($user_id != 1) die();

            $monsters = $this->DBMonsterRepo->cancelInactiveMonsters();
        } elseif ($action == 'createpngs'){
            if ($user_id != 1) die();

            $monsters = $this->DBMonsterRepo->createMissingMonsterImages();
        } elseif ($action == 'closeInfoMessage'){
            if ($user_id != 1) die();
            $message_id = $request->message_id;

            $infoMessageClosed = new InfoMessageClosed;
            $infoMessageClosed->user_id = $user_id;
            $infoMessageClosed->info_message_id = $message_id;
            $infoMessageClosed->save();
        } elseif ($action == 'awardtrophies'){
            if ($user_id != 1) die();

            $users = $this->DBUserRepo->getAllActiveUsers(true,true,true,true);
            $trophyTypes = $this->DBTrophyTypeRepo->getAll();

            foreach($trophyTypes as $trophyType){
                foreach($users as $user){
                    $hasTrophy=false;

                    $trophyConditionSatisfied = false;
                    switch ($trophyType->name){
                        case 'first_monster':
                            if (count($user->monsterSegments)>1) $trophyConditionSatisfied = true;
                            break;
                        case 'ten_monsters':
                            if (count($user->monsterSegments)>10) $trophyConditionSatisfied = true;
                            break;
                        case 'hundred_monsters':
                            if (count($user->monsterSegments)>100) $trophyConditionSatisfied = true;
                            break;
                        case 'first_rating':
                            if (count($user->ratings)>1) $trophyConditionSatisfied = true;
                            break;
                        case 'ten_ratings':
                            if (count($user->ratings)>10) $trophyConditionSatisfied = true;
                            break;
                        case 'hundred_ratings':
                            if (count($user->ratings)>100) $trophyConditionSatisfied = true;
                            break;
                        case 'first_comment':
                            if (count($user->comments)>1) $trophyConditionSatisfied = true;
                            break;
                        case 'ten_comments':
                            if (count($user->comments)>10) $trophyConditionSatisfied = true;
                            break;
                        case 'hundred_comments':
                            if (count($user->comments)>100) $trophyConditionSatisfied = true;
                            break;
                        case 'popular_comment':
                            $found = false;
                            foreach ($user->comments as $comment){
                                if ($comment->votes >= 5){
                                    $found = true;
                                break;
                                }
                            }
                            $trophyConditionSatisfied = $found;
                            break;
                        case 'two_day_streak':
                            // Log::debug('two day streak found'. $user->id.':'.$user->top_streak);
                            if ($user->streak && $user->streak->top_streak>1) $trophyConditionSatisfied = true;
                            break;
                        case 'four_day_streak':
                            if ($user->streak && $user->streak->top_streak>3) $trophyConditionSatisfied = true;
                            break;
                        case 'seven_day_streak':
                            if ($user->streak && $user->streak->top_streak>6) $trophyConditionSatisfied = true;
                            break;
                    }

                    $hasTrophy=false;
                    if ($trophyConditionSatisfied){
                        foreach($user->trophies as $trophy){
                            if ($trophy->type_id == $trophyType->id){
                                $hasTrophy=true;
                            break;
                            }
                        }
                        if (!$hasTrophy){
                            $this->DBTrophyRepo->awardTrophy($user, $trophyType);
                        }
                    }
                }
            }
           
        }
    } 
}