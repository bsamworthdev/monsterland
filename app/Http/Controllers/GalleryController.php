<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Route;
use App\http\Repositories\DBUserRepository;
use App\http\Repositories\DBMonsterRepository;
use App\http\Repositories\DBMonsterSegmentRepository;

class GalleryController extends Controller
{

    protected $DBMonsterRepo;
    protected $DBMonsterSegmentRepo;
    protected $DBUserRepo;

    public function __construct(DBMonsterRepository $DBMonsterRepo, 
        DBMonsterSegmentRepository $DBMonsterSegmentRepo,
        DBUserRepository $DBUserRepository)
    {
        $this->DBMonsterRepo = $DBMonsterRepo;
        $this->DBMonsterSegmentRepo = $DBMonsterSegmentRepo;
        $this->DBUserRepository = $DBUserRepository;
        // $this->middleware(['auth','verified']);
    }

    //
    public function index(Request $request, $monster_id = NULL){

        if (Auth::check()){
            $user_id = Auth::User()->id;
            $user = $this->DBUserRepository->find($user_id);
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

        $monster = $this->DBMonsterRepo->getMonsterById($monster_id, $user);
        
        if ($monster){

            $nextMonster = $this->DBMonsterRepo->getNextMonster($monster, $user, $group_id);
            $prevMonster = $this->DBMonsterRepo->getPrevMonster($monster, $user, $group_id);
                
            return view('gallery', [
                'monster' => $monster,
                'user' => $user,
                'prevMonster' => $prevMonster ? $prevMonster : $monster,
                'nextMonster' => $nextMonster ? $nextMonster : $monster,
                'groupMode' => $group_id > 0 ? 1 : 0
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
            if ($user_id != 1) return;

            $action = $request->action;
            $monster_id = $request->monster_id;

            //Log::debug('action = '.$action);
            if ($action == 'flag'){
                $severity = $request->severity;
                $this->DBMonsterRepo->flagMonster($monster_id, $severity);
            } elseif ($action == 'rollback'){
                $segments = $request->segments;

                if ($segments == 'legs'){
                    $this->DBMonsterSegmentRepo->deleteMonsterSegments($monster_id, ['legs']);
                    $this->DBMonsterRepo->rollbackMonster($monster_id, ['legs']);
                }
                elseif ($segments == 'body_legs'){
                    $this->DBMonsterSegmentRepo->deleteMonsterSegments($monster_id, ['body','legs']);
                    $this->DBMonsterRepo->rollbackMonster($monster_id, ['body','legs']);
                }
                header("Location: /home");
                die();

            } elseif ($action == 'abort'){
                $this->DBMonsterRepo->abortMonster($monster_id);
            }
        }
    }
}
