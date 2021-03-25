<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\CompletedMonsterMailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Carbon\Carbon;
use App\Repositories\DBMonsterRepository;
use App\Repositories\DBMonsterSegmentRepository;
use App\Repositories\DBUserRepository;
use App\Repositories\DBStreakRepository;
use App\Repositories\DBAuditRepository;
use App\Events\MonsterCompleted;
use App\Models\SalvagedSegment;

class NonAuthCanvasController extends Controller
{

    private $monster_id;
    protected $DBMonsterRepo;
    protected $DBMonsterSegmentRepo;
    protected $DBUserRepo;
    protected $DBStreakRepo;
    protected $DBAuditRepo;

    public function __construct(Request $request, 
        DBMonsterRepository $DBMonsterRepo, 
        DBMonsterSegmentRepository $DBMonsterSegmentRepo,
        DBUserRepository $DBUserRepo,
        DBStreakRepository $DBStreakRepo,
        DBAuditRepository $DBAuditRepo)
    {
        // $this->middleware(['auth','verified']); //Added temporarily to block non-logged-in users
        $this->DBMonsterRepo = $DBMonsterRepo;
        $this->DBMonsterSegmentRepo = $DBMonsterSegmentRepo;
        $this->DBUserRepo = $DBUserRepo;
        $this->DBStreakRepo = $DBStreakRepo;
        $this->DBAuditRepo = $DBAuditRepo;
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request, $monster_id = NULL)
    {
        $session = $request->session();
        $session_id = $session->getId();

        $user_id =0;
        if (Auth::check()){
            $user_id = Auth::user()->id;
        }

        //Reset other monsters in progress with this session
        $this->DBMonsterRepo->resetUserMonsters($monster_id, $session_id);

        if (!is_null($monster_id)){
            $monster = $this->DBMonsterRepo->find($monster_id, 'segments');
            $group_id = $session->get('group_id') ? : 0;

            // if ($monster->in_progress_with > 0 && $monster->in_progress_with != $user_id) {
            //     return back()->with('error', 'This monster is already being worked on');
            // }

            if ($monster->in_progress_with_session_id <> NULL 
                && $monster->in_progress_with_session_id != $session_id
                && $monster->updated_at > Carbon::now()->subMinutes(10)) {
                return back()->with('error', 'This monster is already being worked on');
            }

            $monster_segment_name = $this->DBMonsterSegmentRepo->getCurrentSegmentName($monster->status);
            if (!$monster_segment_name) return back()->with('error', 'Cannot load monster');

            $this->DBMonsterRepo->startMonster($monster_id, $user_id, $session_id);

            $this->DBAuditRepo->create(($user_id ? : NULL), $monster_id, 'misc', 'started working on');

            //Fetch version with images
            $monster = $this->DBMonsterRepo->find($monster_id, 'segmentsWithImages');
        } else {
            $monster_segment_name = 'head';
        }
        
        return view('canvas', [
            'segment_name' => $monster_segment_name,
            'monster' => is_null($monster_id) ? null : $monster,
            'logged_in' => Auth::check() ? 1 : 0,
            "group_mode" => $group_id > 0 ? 1 : 0,
            'user' => Auth::user() ? : null
        ]);
    }

    public function save(Request $request)
    {
        $session = $request->session();
        $session_id = $session->getId();
        
        $group_username = $session->get('group_username') ? : NULL;

        if (isset($request->monster_id)){
            $monster_id = $request->monster_id;
            //Update existing monster
            $monster = $this->DBMonsterRepo->find($monster_id); 
            if ($monster->status == 'awaiting head'){
                $status = 'awaiting body';
                $background = $request->background;
                $segment = 'head';
                $completed_at = NULL;
            } elseif ($monster->status == 'awaiting body'){
                if ($monster->segments[0]->created_by_session_id !== $session_id){
                    $status = 'awaiting legs';
                    $background = $monster->background;
                    $segment = 'body';
                    $completed_at = NULL;
                } else {
                    return back()->withError('Cannot save monster');
                }
            } elseif ($monster->status == 'awaiting legs'){
                if ($monster->segments[1]->created_by_session_id !== $session_id){
                    $status = 'complete';
                    $background = $monster->background;
                    $segment = 'legs';
                    $completed_at = date('Y-m-d H:i:s');
                } else {
                    return back()->withError('Cannot save monster');
                }
            } else {
                return back()->withError('Cannot save monster');
            }
            $monster->status = $status;
            $monster->background = $background;
            $monster->in_progress = 0;
            $monster->in_progress_with = 0;
            $monster->in_progress_with_session_id = NULL;
            $monster->completed_at = $completed_at;
            $monster->save();

        } else {
            return back()->with('error', 'Cannot save monster');
        }
        $user = Auth::User();
        $monster_segment = $this->DBMonsterSegmentRepo->createInstance();
        $segmentImagePath = $monster_segment->createImage($monster_id, $request->imgBase64, $segment);

        $monster_segment->segment = $segment;
        $monster_segment->image = ''; //$request->imgBase64;
        $monster_segment->image_path = $segmentImagePath;
        $monster_segment->colors_used = json_encode($request->colorsUsed);
        $monster_segment->fineliner_used = $request->finelinerUsed;
        $monster_segment->email_on_complete = $request->email_on_complete;
        $monster_segment->monster_id = $monster_id;
        $monster_segment->created_by = $user ? $user->id : 0;
        $monster_segment->created_by_session_id = $session_id;
        $monster_segment->created_by_group_username = $group_username;
        $monster_segment->save();

        //Monster completed, so save images
        if ($monster->completed_at != NULL){
            $monster->image = $monster->createImage();
            $monster->thumbnail_image = $monster->createThumbnailImage();;
            $monster->save();
        }

        //Update current_streak if account found
        if ($user){
            $streak = $this->DBStreakRepo->updateStreak($user->id);
        }

        //Send email(s)
        if ($monster->status == 'complete'){
            //Emit MonsterCompleted event
            event(new MonsterCompleted($monster));
            //Audit
            $this->DBAuditRepo->create(($user ? $user->id : NULL), $monster_id, 'monster_completed', 'New monster created: ');
        } else {
            //Audit
            $this->DBAuditRepo->create(($user ? $user->id : NULL), $monster_id, 'segment_completed', ' drew '.$segment.' for ');
        }

        return 'saved';
    }

    public function salvage(Request $request){
        $session = $request->session();
        $session_id = $session->getId();
        $monster_id = $request->monster_id;

        $user = Auth::User();
        $salvaged_segment = new SalvagedSegment;
        $salvaged_segment->segment = $request->segment;
        $salvaged_segment->image = $request->imgBase64;
        $salvaged_segment->colors_used = json_encode($request->colorsUsed);
        $salvaged_segment->fineliner_used = $request->finelinerUsed;
        $salvaged_segment->monster_id = $monster_id;
        $salvaged_segment->created_by = $user ? $user->id : 0;;
        $salvaged_segment->created_by_session_id =$session_id;
        $salvaged_segment->save();

        return 'saved';
    }

    public function cancel(Request $request)
    {
        
        if (isset($request->monster_id)){
            $user = Auth::User();
            $user_id = $user ? $user->id : NULL;
            $monster_id = $request->monster_id;
            $this->DBMonsterRepo->cancelMonster($monster_id);
            $this->DBAuditRepo->create($user_id, $monster_id, 'misc', 'stopped working on: ');
        }

        return 'success';
    }

    public function update(Request $request){

        $action = $request->action;

        $monster_id = $request->monster_id;
        if ($action == 'updateIdleTimer'){
            $this->DBMonsterRepo->updateLastUpdated($monster_id);
        }
        

        return 'success';
    }
}
