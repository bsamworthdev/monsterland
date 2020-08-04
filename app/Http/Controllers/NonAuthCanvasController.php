<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MonsterSegment;
use App\Monster;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class NonAuthCanvasController extends Controller
{

    private $monster_id;

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request, $monster_id = NULL)
    {
        if (!is_null($monster_id)){
            $monster = Monster::with('segments')->find($monster_id);
            $session = $request->session();
            $session_id = $session->getId();

            // if ($monster->in_progress_with > 0 && $monster->in_progress_with != $user_id) {
            //     return back()->with('error', 'This monster is already being worked on');
            // }

            if ($monster->in_progress_with_session_id <> NULL && $monster->in_progress_with_session_id != $session_id) {
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
            $monster->in_progress_with = 0;
            $monster->in_progress_with_session_id = $session_id;
            $monster->save();
        } else {
            $monster_segment_name = 'head';
        }
        
        return view('canvas', [
            'segment_name' => $monster_segment_name,
            'monster' => is_null($monster_id) ? null : $monster,
            'logged_in' => Auth::check()
        ]);
    }

    public function save(Request $request)
    {
        $session = $request->session();
        $session_id = $session->getId();

        if (isset($request->monster_id)){
            $monster_id = $request->monster_id;
            //Update existing monster
            $monster = Monster::find($monster_id); 
            if ($monster->status == 'awaiting head'){
                $status = 'awaiting body';
                $segment = 'head';
            } elseif ($monster->status == 'awaiting body'){
                $status = 'awaiting legs';
                $segment = 'body';
            } elseif ($monster->status == 'awaiting legs'){
                $status = 'complete';
                $segment = 'legs';
            } else {
                return back()->withError('Cannot save monster');
            }
            $monster->status = $status;
            $monster->in_progress = 0;
            $monster->in_progress_with = 0;
            $monster->in_progress_with_session_id = NULL;
            $monster->save();

        } else {
            //Create new monster
            $monster = new Monster;
            $monster->name = 'Default name';
            $monster->status = 'awaiting body';
            $monster->in_progress = 0;
            $monster->in_progress_with = 0;
            $monster->in_progress_with_session_id = NULL;
            $monster->save();

            $segment = 'head';
            $monster_id = $monster->id;
        }
        $user = Auth::User();
        $monster_segment = new MonsterSegment;
        $monster_segment->segment = $segment;
        $monster_segment->image = $request->imgBase64;
        $monster_segment->monster_id = $monster_id;
        $monster_segment->created_by = $user ? $user->id : 0;
        $monster_segment->created_by_session_id = $session_id;
        $monster_segment->save();

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
}
