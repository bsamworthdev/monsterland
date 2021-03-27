<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\Repositories\DBUserRepository;
use App\Repositories\DBMonsterRepository;
use App\Repositories\DBMonsterSegmentRepository;
use App\Repositories\DBTakeTwoRepository;
use App\Repositories\DBSettingsRepository;
use App\Repositories\DBAuditRepository;
use Illuminate\Support\Facades\Redis;

class GalleryController extends Controller
{

    protected $DBMonsterRepo;
    protected $DBMonsterSegmentRepo;
    protected $DBUserRepo;
    protected $DBTakeTwoRepo;
    protected $DBSettingsRepo;
    protected $DBAuditRepo;

    public function __construct(DBMonsterRepository $DBMonsterRepo, 
        DBMonsterSegmentRepository $DBMonsterSegmentRepo,
        DBUserRepository $DBUserRepo,
        DBTakeTwoRepository $DBTakeTwoRepo,
        DBSettingsRepository $DBSettingsRepo,
        DBAuditRepository $DBAuditRepo)
    {
        $this->DBMonsterRepo = $DBMonsterRepo;
        $this->DBMonsterSegmentRepo = $DBMonsterSegmentRepo;
        $this->DBUserRepo = $DBUserRepo;
        $this->DBTakeTwoRepo = $DBTakeTwoRepo;
        $this->DBSettingsRepo = $DBSettingsRepo;
        $this->DBAuditRepo = $DBAuditRepo;
        // $this->middleware(['auth','verified']);
    }

    //
    public function index(Request $request, $monster_id = NULL){

        if (Auth::check()){
            $user_id = Auth::User()->id;
            $user = $this->DBUserRepo->find($user_id);
            if (!isset($user->email_verified_at)){
                $user = NULL;
            }
            $group_id = 0;
        } else {
            $user = NULL;
            $session = $request->session();
            $group_id = $session->get('group_id') ? : 0;
        }
        
        if (!isset($monster_id)) {
            $monster = $this->DBMonsterRepo->getLatestCompletedMonster($user, $group_id);
            if ($monster){
                header("Location: /gallery/$monster->id");
            } else {
                // header("Location: /nonauth/home/$group_id");
                return back()->with('error', 'No completed monsters in gallery');
            }
            die();
        }

        $monster = $this->DBMonsterRepo->getMonsterById($monster_id, $user, $group_id);
        
        if ($monster){

            // $nextMonster = $this->DBMonsterRepo->getNextMonster($monster, $user, $group_id);
            // $prevMonster = $this->DBMonsterRepo->getPrevMonster($monster, $user, $group_id);

            // $gallery_monster_ids = Redis::get('gallery_monster_ids');
            $request->session()->get('gallery_monster_ids');
            if ($gallery_monster_ids){
                //Get prev and next monsters based on most recent filter in gallery grid
                $gallery_monster_ids = explode(',',$gallery_monster_ids);
                $index = array_search($monster_id, $gallery_monster_ids);

                $next_monster_id = NULL;
                $prev_monster_id = NULL;
                if ($index < count($gallery_monster_ids) - 1) $next_monster_id = $gallery_monster_ids[$index+1];
                if ($index > 0) $prev_monster_id = $gallery_monster_ids[$index-1];;
                $nextMonster = $this->DBMonsterRepo->getMonsterById($next_monster_id, $user, $group_id);
                $prevMonster = $this->DBMonsterRepo->getMonsterById($prev_monster_id, $user, $group_id);
            } else {
                $nextMonster = $this->DBMonsterRepo->getNextMonster($monster, $user, $group_id);
                $prevMonster = $this->DBMonsterRepo->getPrevMonster($monster, $user, $group_id);
            }
            $everyoneCanUseStore = $this->DBSettingsRepo->everyOneCanUseStore();

            return view('gallery', [
                'monster' => $monster,
                'user' => $user,
                'everyoneCanUseStore' => $everyoneCanUseStore ? 1 : 0,
                'prevMonster' => $prevMonster ? $prevMonster : $monster,
                'nextMonster' => $nextMonster ? $nextMonster : $monster,
                'groupMode' => ($group_id > 0 || $monster->group_id > 0) ? 1 : 0
            ]);
        } else {
            return view('error', [
                'error_message' => 'No monster found'
            ]);
        }
        
    }

    public function update(Request $request){

        if (Auth::check()){
            $user_id = Auth::User()->id;

            $action = $request->action;
            $monster_id = $request->monster_id;

            if ($action == 'flag'){
                if ($user_id != 1) return;

                $severity = $request->severity;
                $this->DBMonsterRepo->flagMonster($monster_id, $severity);
            } elseif ($action == 'rollback'){
                if ($user_id != 1) return;

                $segments = $request->segments;
                if ($segments == 'legs'){
                    $this->DBMonsterSegmentRepo->deleteMonsterSegments($monster_id, ['legs']);
                    $this->DBMonsterRepo->rollbackMonster($monster_id, ['legs']);
                    $this->DBAuditRepo->create($user_id, $monster_id, 'misc', 'rolled back legs on ');
                }
                elseif ($segments == 'body_legs'){
                    $this->DBMonsterSegmentRepo->deleteMonsterSegments($monster_id, ['body','legs']);
                    $this->DBMonsterRepo->rollbackMonster($monster_id, ['body','legs']);
                    $this->DBAuditRepo->create($user_id, $monster_id, 'misc', 'rolled back body and legs on ');
                }
                $this->DBMonsterRepo->validateMonster($monster_id);
                
                header("Location: /home");
                die();

            } elseif ($action == 'abort'){
                if ($user_id != 1) return;

                $this->DBMonsterRepo->abortMonster($monster_id);
            } elseif ($action == 'suggestrollback'){
                $user = $this->DBUserRepo->find($user_id);
                if ($user->moderator != 1) return;
                $this->DBMonsterRepo->suggestMonsterRollback($user_id, $monster_id);
            } elseif ($action == 'validate'){
                if ($user_id != 1) return;
                $this->DBMonsterRepo->validateMonster($monster_id);
            } elseif ($action == 'takeTwo'){
                $user = $this->DBUserRepo->find($user_id);
                if (!$user->has_used_app && !$user->is_patron && $user->take_two_count == 0) return;
                $segment_name = $request->segment;
                $this->DBTakeTwoRepo->create($user_id,$monster_id,$segment_name);
                $this->DBMonsterRepo->takeTwoOnMonster($monster_id, $segment_name); 
                $this->DBAuditRepo->create($user_id, $monster_id, 'misc', 'redraw from '.$segment_name);

                if (!$user->has_used_app && !$user->is_patron) $this->DBUserRepo->decrementTakeTwoCount($user_id);
            } elseif ($action == 'rejectTakeTwo'){
                if ($user_id != 1) return;
                $this->DBMonsterRepo->rejectTakeTwoOnMonster($monster_id);
            } elseif ($action == 'requestTakeTwo'){
                $user = $this->DBUserRepo->find($user_id);
                if ($user->moderator != 1) return;
                $segment_name = $request->segment;
                $this->DBMonsterRepo->requestTakeTwoOnMonster($user_id, $monster_id, $segment_name);
                return back()->with('success', 'New monster requested. This will appear if an admin approves it.');
            } elseif ($action == 'updateAuthLevel'){
                if ($user_id != 1) return;
                $level = $request->level;
                $this->DBMonsterRepo->updateAuthLevel($monster_id, $level);
            } elseif ($action == 'addFavourite'){
                $monster_id = $request->monster_id;
                $this->DBMonsterRepo->addFavourite($user_id, $monster_id);
            } elseif ($action == 'removeFavourite'){
                $monster_id = $request->monster_id;
                $this->DBMonsterRepo->removeFavourite($user_id, $monster_id);
            }

        }
    }

    public function findUserByName(Request $request, $search = NULL){
        $user = $this->DBUserRepo->findUserByName($search);
        if ($user && $user->id){
            header("Location: /monsters/".$user->id);
            die();
        } else {
            return back()->with('error', 'User not found');
        }
    }
}
