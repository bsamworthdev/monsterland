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
                && $monster->updated_at > Carbon::now()->subHours(1)) {
                return back()->with('error', 'This monster is already being worked on');
            }

            $monster_segment_name = $this->DBMonsterSegmentRepo->getCurrentSegmentName($monster->status);
            if (!$monster_segment_name) return back()->with('error', 'Cannot load monster');

            $this->DBMonsterRepo->startMonster($monster_id, 0, $session_id);

            //Fetch version with images
            $monster = $this->DBMonsterRepo->find($monster_id, 'segmentsWithImages');
        } else {
            $monster_segment_name = 'head';
        }
        
        return view('canvas', [
            'segment_name' => $monster_segment_name,
            'monster' => is_null($monster_id) ? null : $monster,
            'logged_in' => Auth::check(),
            "group_mode" => $group_id > 0 ? 1 : 0
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
                $image = NULL;
                $completed_at = NULL;
            } elseif ($monster->status == 'awaiting body'){
                if ($monster->segments[0]->created_by_session_id !== $session_id){
                    $status = 'awaiting legs';
                    $background = $monster->background;
                    $segment = 'body';
                    $image = NULL;
                    $completed_at = NULL;
                } else {
                    return back()->withError('Cannot save monster');
                }
            } elseif ($monster->status == 'awaiting legs'){
                if ($monster->segments[1]->created_by_session_id !== $session_id){
                    $status = 'complete';
                    $background = $monster->background;
                    $segment = 'legs';
                    $image = NULL;
                    $completed_at = date('Y-m-d H:i:s');
                    $image = $monster->createImage($request->imgBase64);
                } else {
                    return back()->withError('Cannot save monster');
                }
            } else {
                return back()->withError('Cannot save monster');
            }
            $monster->status = $status;
            $monster->image = $image;
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
        $monster_segment->segment = $segment;
        $monster_segment->image = $request->imgBase64;
        $monster_segment->email_on_complete = $request->email_on_complete;
        $monster_segment->monster_id = $monster_id;
        $monster_segment->created_by = $user ? $user->id : 0;
        $monster_segment->created_by_session_id = $session_id;
        $monster_segment->created_by_group_username = $group_username;
        $monster_segment->save();

        //Update current_streak if account found
        if ($user){
            $streak = $this->DBStreakRepo->updateStreak($user->id);
        }

        //Send email(s)
        if ($monster->status == 'complete'){
            foreach($monster->segments as $segment){
                if ($segment->email_on_complete){
                    $segment_user_id = $segment->created_by;
                    if ($segment_user_id > 0){
                        $segment_user= $this->DBUserRepo->find($segment_user_id);
                        Mail::to($segment_user->email)
                            ->send(new CompletedMonsterMailable($segment_user, $monster));
                    }
                }
            }
            //Audit
            $this->DBAuditRepo->create(NULL, $monster_id, 'monster_completed', 'New monster created: ');
        } else {
            //Audit
            $this->DBAuditRepo->create(NULL, $monster_id, 'segment_completed', ' drew '.$segment.' for ');
        }

        return 'saved';
    }

    public function cancel(Request $request)
    {
        if (isset($request->monster_id)){
            $this->DBMonsterRepo->cancelMonster($request->monster_id);
        }

        return 'success';
    }
}
