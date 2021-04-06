<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\CompletedMonsterMailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use App\Repositories\DBMonsterRepository;
use App\Repositories\DBMonsterSegmentRepository;
use App\Repositories\DBUserRepository;
use App\Repositories\DBStreakRepository;
use App\Repositories\DBAuditRepository;
use App\Repositories\DBPeekRepository;
use App\Events\MonsterCompleted;
use App\Models\SalvagedSegment;

class CanvasController extends Controller
{

    private $monster_id;
    protected $DBMonsterRepo;
    protected $DBMonsterSegmentRepo;
    protected $DBUserRepo;
    protected $DBStreakRepo;
    protected $DBAuditRepo;
    protected $DBPeekRepo;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, 
        DBMonsterRepository $DBMonsterRepo, 
        DBMonsterSegmentRepository $DBMonsterSegmentRepo,
        DBUserRepository $DBUserRepo,
        DBStreakRepository $DBStreakRepo,
        DBAuditRepository $DBAuditRepo,
        DBPeekRepository $DBPeekRepo)
    {
        $this->middleware(['auth','verified']);
        $this->DBMonsterRepo = $DBMonsterRepo;
        $this->DBMonsterSegmentRepo = $DBMonsterSegmentRepo;
        $this->DBUserRepo = $DBUserRepo;
        $this->DBStreakRepo = $DBStreakRepo;
        $this->DBAuditRepo = $DBAuditRepo;
        $this->DBPeekRepo = $DBPeekRepo;
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
            $user = $this->DBUserRepo->find($user_id);

            if ($monster->in_progress_with > 0 
                && ($monster->in_progress_with == 0 || $monster->in_progress_with != $user_id)
                && $monster->in_progress_with_session_id != $session_id
                && $monster->updated_at > Carbon::now()->subMinutes(10)) {
                return back()->with('error', 'This monster is already being worked on');
            }

            $monster_segment_name = $this->DBMonsterSegmentRepo->getCurrentSegmentName($monster->status);
            if (!$monster_segment_name) return back()->with('error', 'Cannot load monster');

            if (
                ($monster_segment_name == 'body' && $this->DBMonsterSegmentRepo->findSegmentCreator($monster_id, 'head') == $user_id)
                || 
                ($monster_segment_name == 'legs' && $this->DBMonsterSegmentRepo->findSegmentCreator($monster_id, 'body') == $user_id)
                ) {
                return back()->with('error', 'ERROR: You cannot edit a monster which you drew the previous section for!');
            }

            $this->DBMonsterRepo->startMonster($monster_id, $user_id, $session_id);

            $this->DBAuditRepo->create($user_id, $monster_id, 'misc', 'started working on: ');

            //Fetch version with images
            $monster = $this->DBMonsterRepo->find($monster_id, 'segmentsWithImages');
        } else {
            $monster_segment_name = 'head';
        }
        
        return view('canvas', [
            'segment_name' => $monster_segment_name,
            'monster' => is_null($monster_id) ? null : $monster,
            'logged_in' => 1,
            'user' => $user
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
            $user = $this->DBUserRepo->find($user_id);
            if ($monster->status == 'awaiting head'){
                $status = 'awaiting body';
                $background = $request->background;
                $needs_validating = $user->needs_monitoring;
                $segment = 'head';
                $completed_at = NULL;
                $name = $monster->name;
            } elseif ($monster->status == 'awaiting body'){
                if ($monster->segments[0]->created_by !== $user_id){
                    $status = 'awaiting legs';
                    $background = $monster->background;
                    $needs_validating = $user->needs_monitoring;
                    $segment = 'body';
                    $completed_at = NULL;
                    $name = $monster->name;
                } else {
                    return back()->withError('Cannot save monster');
                }
            } elseif ($monster->status == 'awaiting legs'){
                if ($monster->segments[1]->created_by !== $user_id){
                    $status = 'complete';
                    $background = $monster->background;
                    $needs_validating = 0;
                    $segment = 'legs';
                    //$image = NULL;
                    $completed_at = date('Y-m-d H:i:s');
                    $name = $monster->name;
                    if (date('m-d') == '04-01'){
                        //April Fool
                        $name .= ' (+ cat)';
                    }
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
            $monster->needs_validating = $needs_validating;
            $monster->in_progress_with_session_id = NULL;
            $monster->completed_at = $completed_at;
            $monster->name = $name;
            $monster->save();

        } else {
            return back()->with('error', 'Cannot save monster');
        }

        $monster_segment = $this->DBMonsterSegmentRepo->createInstance();
        $segmentImagePath = $monster_segment->createImage($monster_id, $request->imgBase64, $segment);

        $monster_segment->segment = $segment;
        $monster_segment->image = ''; //$request->imgBase64;
        $monster_segment->image_path = $segmentImagePath;
        $monster_segment->colors_used = json_encode($request->colorsUsed);
        $monster_segment->fineliner_used = $request->finelinerUsed;
        $monster_segment->email_on_complete = $request->email_on_complete;
        $monster_segment->monster_id = $monster_id;
        $monster_segment->created_by = $user_id;
        $monster_segment->created_by_session_id =$session_id;
        $monster_segment->save();

        //Monster completed, so save images
        if ($monster->completed_at != NULL){
            $monster->image = $monster->createImage();
            $monster->thumbnail_image = $monster->createThumbnailImage();
            $monster->save();
        }

        //Update current_streak
        $streak = $this->DBStreakRepo->updateStreak($user_id);

        if ($status == 'complete'){
            //Emit MonsterCompleted event
            event(new MonsterCompleted($monster));
            //Audit
            $this->DBAuditRepo->create($user_id, $monster_id, 'monster_completed', 'New monster created: ');
        } else {
            //Audit
            $this->DBAuditRepo->create($user_id, $monster_id, 'segment_completed', ' drew the '.$segment.' for ');
        }

        return 'saved';
    }

    public function salvage(Request $request){
        $user_id = Auth::User()->id;
        $session = $request->session();
        $session_id = $session->getId();
        $monster_id = $request->monster_id;

        $salvaged_segment = new SalvagedSegment;
        $salvaged_segment->segment = $request->segment;
        $salvaged_segment->image = $request->imgBase64;
        $salvaged_segment->colors_used = json_encode($request->colorsUsed);
        $salvaged_segment->fineliner_used = $request->finelinerUsed;
        $salvaged_segment->monster_id = $monster_id;
        $salvaged_segment->created_by = $user_id;
        $salvaged_segment->created_by_session_id =$session_id;
        $salvaged_segment->save();

        //Update current_streak
        $streak = $this->DBStreakRepo->updateStreak($user_id);

        return 'saved';
    }

    public function cancel(Request $request)
    { 
        if (isset($request->monster_id)){
            $user_id = Auth::User()->id;
            $monster_id = $request->monster_id;
            $this->DBMonsterRepo->cancelMonster($monster_id);
            $this->DBAuditRepo->create($user_id, $monster_id, 'misc', 'stopped working on ');
        }

        return 'success';
    }

    public function update(Request $request){
        
        if (!Auth::check()) return 'unauthorised';

        $action = $request->action;

        $monster_id = $request->monster_id;
        $user_id = Auth::User()->id;
        $session = $request->session();
        $session_id = $session->getId();
        
        if ($action == 'updateName'){
            $monster_name = $request->monster_name;
            
            $this->DBMonsterRepo->updateMonsterName($user_id, $monster_id, $monster_name);
        } elseif ($action == 'updateLevel'){
            $monster_level = $request->monster_level;
            $user = $this->DBUserRepo->find($user_id);
            
            if ($monster_level == 'basic' || $monster_level == 'standard' || $user->vip){
                $this->DBMonsterRepo->updateMonsterLevel($user_id, $monster_id, $monster_level);
            }
        } elseif ($action == 'peekActivated'){
            $monster_segment = $request->monster_segment;
            $this->DBPeekRepo->create($user_id, $monster_id, $monster_segment);
            $this->DBUserRepo->decrementPeekCount($user_id);
        } elseif ($action == 'updateIdleTimer'){
            $this->DBMonsterRepo->updateLastUpdated($monster_id);
        } elseif ($action == 'reviveImage'){
            $segment_name = $request->segment_name;
            return $this->DBMonsterRepo->reviveImage($monster_id, $segment_name, $user_id, $session_id);
        } elseif ($action == 'updateIsNSFW'){
            $is_nsfw = $request->nsfw;
            $user = $this->DBUserRepo->find($user_id);
            $this->DBMonsterRepo->updateMonsterNSFW($user_id, $monster_id, $is_nsfw);
        } elseif ($action == 'sendBirthAnnouncement'){
            $monster = $this->DBMonsterRepo->find($monster_id);
            if ($session_id == $monster->segments[2]->created_by_session_id){
                $payload = [
                    'username' => "New Monster bot",
                    'content' =>  "[".$monster->name."](https://monsterland.net/gallery/".$monster_id.") has just been born!",
                    'embeds' =>  [
                        [
                            'image' => [
                                'author' => '',
                                'title' => '',
                                'description' => '',
                                'url'  =>  "https://monsterland.net/storage/".$monster_id.".png"
                            ]
                        ]
                    ],
                ];
                $url = 'https://discord.com/api/webhooks/828349688247484476/yh_yD6f9efWiYQ8fbBHc3vfPTtow5zPQrohSdJ6xwmOdLvHUyPZlNGF3GwBcZi6Jmp_1';
                $response = Http::post($url, $payload);
            }
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
