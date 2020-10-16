<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MonsterSegment;
use App\Monster;
use App\Streak;
use App\User;
use App\Mail\CompletedMonsterMailable;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;

class CanvasController extends Controller
{

    private $monster_id;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request)
    {
        $this->middleware(['auth','verified']);
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
        $monsters = Monster::where('in_progress','1')
            ->where('id','<>', $monster_id)
            ->where('in_progress_with_session_id', $session_id)
            ->update(
                [
                'in_progress' => 0, 
                'in_progress_with' => 0, 
                'in_progress_with_session_id' => NULL
                ]
            );

        if (!is_null($monster_id)){
            $monster = Monster::with('segments')->find($monster_id);
            $user_id = Auth::User()->id;

            if ($monster->in_progress_with > 0 
                && $monster->in_progress_with != $user_id
                && $monster->updated_at > Carbon::now()->subHours(1)) {
                return back()->with('error', 'This monster is already being worked on');
            }

            if ($monster->status == 'awaiting head'){
                $monster_segment_name = 'head';
            } elseif ($monster->status == 'awaiting body'){
                $monster_segment_name = 'body';
            } elseif ($monster->status == 'awaiting legs'){
                $monster_segment_name = 'legs';
            } else {
                return back()->with('error', 'Cannot load monster');
            }

            $monster->in_progress = 1;
            $monster->in_progress_with = $user_id;
            $monster->in_progress_with_session_id = $session_id;
            $monster->save();

            //Fetch version with images
            $monster = Monster::with('segmentsWithImages')->find($monster_id);
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
            $monster = Monster::find($monster_id); 
            if ($monster->status == 'awaiting head'){
                $status = 'awaiting body';
                $segment = 'head';
                $image = NULL;
                $completed_at = NULL;
            } elseif ($monster->status == 'awaiting body'){
                if ($monster->segments[0]->created_by !== $user_id){
                    $status = 'awaiting legs';
                    $segment = 'body';
                    $image = NULL;
                    $completed_at = NULL;
                } else {
                    return back()->withError('Cannot save monster');
                }
            } elseif ($monster->status == 'awaiting legs'){
                if ($monster->segments[1]->created_by !== $user_id){
                    $status = 'complete';
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
            $monster->in_progress = 0;
            $monster->in_progress_with = 0;
            $monster->in_progress_with_session_id = NULL;
            $monster->completed_at = $completed_at;
            $monster->save();

        } else {
            return back()->with('error', 'Cannot save monster');
            //Create new monster
            // $monster = new Monster;
            // $monster->name = 'Default name';
            // $monster->status = 'awaiting body';
            // $monster->image = NULL;
            // $monster->in_progress = 0;
            // $monster->in_progress_with = 0;
            // $monster->in_progress_with_session_id = NULL;
            // $monster->save();

            // $segment = 'head';
            // $monster_id = $monster->id;
        }

        $monster_segment = new MonsterSegment;
        $monster_segment->segment = $segment;
        $monster_segment->image = $request->imgBase64;
        $monster_segment->email_on_complete = $request->email_on_complete;
        $monster_segment->monster_id = $monster_id;
        $monster_segment->created_by = $user_id;
        $monster_segment->created_by_session_id =$session_id;
        $monster_segment->save();

        //Update current_streak
        $streak = Streak::where('user_id', $user_id)
            ->firstOrNew();
            
        $streak->user_id = $user_id;
        if (date('Y-m-d', strtotime($streak->updated_at)) == date('Y-m-d',strtotime("-1 days"))){
            //Yesterday
            $streak->current_streak += 1;
        } else {
            if (date('Y-m-d', strtotime($streak->updated_at)) != date('Y-m-d')){
                //Not Today
                $streak->current_streak = 1;
            }
        }

        if ($streak->current_streak > $streak->top_streak) {
            //Broken top streak record
            $streak->top_streak = $streak->current_streak;
            $streak->top_streak_at = date('Y-m-d H:i:s');
        }
        $streak->save();

        //Send email(s)
        if ($status == 'complete'){
            foreach($monster->segments as $segment){
                if ($segment->email_on_complete){
                    $segment_user_id = $segment->created_by;
                    if ($segment_user_id > 0){
                        $segment_user= User::find($segment_user_id);
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
            //Update existing monster
            $monster = Monster::find($request->monster_id); 
            $monster->in_progress = 0;
            $monster->in_progress_with = 0;
            $monster->in_progress_with_session_id = NULL;
            $monster->save();
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
