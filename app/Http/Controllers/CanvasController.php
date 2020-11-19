<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\CompletedMonsterMailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\Repositories\DBMonsterRepository;
use App\Repositories\DBMonsterSegmentRepository;
use App\Repositories\DBUserRepository;
use App\Repositories\DBStreakRepository;

class CanvasController extends Controller
{

    private $monster_id;
    protected $DBMonsterRepo;
    protected $DBMonsterSegmentRepo;
    protected $DBUserRepo;
    protected $DBStreakRepo;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, 
        DBMonsterRepository $DBMonsterRepo, 
        DBMonsterSegmentRepository $DBMonsterSegmentRepo,
        DBUserRepository $DBUserRepo,
        DBStreakRepository $DBStreakRepo)
    {
        $this->middleware(['auth','verified']);
        $this->DBMonsterRepo = $DBMonsterRepo;
        $this->DBMonsterSegmentRepo = $DBMonsterSegmentRepo;
        $this->DBUserRepo = $DBUserRepo;
        $this->DBStreakRepo = $DBStreakRepo;
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
            $user_id = Auth::User()->id;

            if ($monster->in_progress_with > 0 
                && $monster->in_progress_with != $user_id
                && $monster->updated_at > Carbon::now()->subHours(1)) {
                return back()->with('error', 'This monster is already being worked on');
            }

            $monster_segment_name = $this->DBMonsterSegmentRepo->getCurrentSegmentName($monster->status);
            if (!$monster_segment_name) return back()->with('error', 'Cannot load monster');

            $this->DBMonsterRepo->startMonster($monster_id, $user_id, $session_id);

            //Fetch version with images
            $monster = $this->DBMonsterRepo->find($monster_id, 'segmentsWithImages');
        } else {
            $monster_segment_name = 'head';
        }
        
        return view('canvas', [
            'segment_name' => $monster_segment_name,
            'monster' => is_null($monster_id) ? null : $monster,
            'logged_in' => 1
        ]);
    }

    public function save(Request $request)
    {
        $user_id = Auth::User()->id;
        $session = $request->session();
        $session_id = $session->getId();

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
                if ($monster->segments[0]->created_by !== $user_id){
                    $status = 'awaiting legs';
                    $background = $monster->background;
                    $segment = 'body';
                    $image = NULL;
                    $completed_at = NULL;
                } else {
                    return back()->withError('Cannot save monster');
                }
            } elseif ($monster->status == 'awaiting legs'){
                if ($monster->segments[1]->created_by !== $user_id){
                    $status = 'complete';
                    $background = $monster->background;
                    $segment = 'legs';
                    //$image = NULL;
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

        $monster_segment = $this->DBMonsterSegmentRepo->createInstance();
        $monster_segment->segment = $segment;
        $monster_segment->image = $request->imgBase64;
        $monster_segment->email_on_complete = $request->email_on_complete;
        $monster_segment->monster_id = $monster_id;
        $monster_segment->created_by = $user_id;
        $monster_segment->created_by_session_id =$session_id;
        $monster_segment->save();

        //Update current_streak
        $streak = $this->DBStreakRepo->updateStreak($user_id);

        //Send email(s)
        if ($status == 'complete'){
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

    // public function createMonsterImage($monster, $legs_image = NULL) {
    //     $output_image = imagecreatetruecolor(800, 800);

    //     if (!$legs_image) $legs_image = $monster->segments[2]->image;

    //     $head_image = base64_decode(str_replace('data:image/png;base64,','', $monster->segments[0]->image));
    //     $body_image = base64_decode(str_replace('data:image/png;base64,','', $monster->segments[1]->image));
    //     $legs_image = base64_decode(str_replace('data:image/png;base64,','', $legs_image));
    //     $image_1 = imagecreatefromstring($head_image);
    //     $image_2 = imagecreatefromstring($body_image);
    //     $image_3 = imagecreatefromstring($legs_image);

    //     $white = imagecolorallocate($output_image, 255, 255, 255);
    //     $image_path = storage_path('app/public/'.$monster->id.'.png');

    //     imagefill($output_image, 0, 0, $white);
    //     imagecopy($output_image, $image_1, 0, 0, 0, 0, 800, 266);
    //     imagecopy($output_image, $image_2, 0, 246, 0, 0, 800, 266);
    //     imagecopy($output_image, $image_3, 0, 512, 0, 0, 800, 266);
    //     imagepng($output_image, $image_path);

    //     // Storage::disk('public')->put('test2', $image_1);
    //     return '/storage/'.$monster->id.'.png';
    // }
}
