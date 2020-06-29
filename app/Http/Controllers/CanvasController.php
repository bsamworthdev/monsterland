<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MonsterSegment;
use App\Monster;
use Illuminate\Support\Facades\Log;

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
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        if (isset($request->monster_id)){
            $monster_id = $request->monster_id;
            $this->monster_id = $monster_id;
            
            $monster = Monster::find($monster_id);
            if ($monster->status == 'awaiting body'){
                $monster_segment_name = 'body';
            } elseif ($monster->status == 'awaiting legs'){
                $monster_segment_name = 'legs';
            } else {
                return back()->withError('Cannot load monster');
            }
        } else {
            $monster_segment_name = 'head';
        }
        
        return view('canvas', [
            'segment_name' => $monster_segment_name,
            'monster' => $monster ? : null
        ]);
    }

    public function save(Request $request)
    {
        if (isset($request->monster_id)){
            $monster_id = $request->monster_id;

            //Update existing monster
            $monster = Monster::find($monster_id); 
            if ($monster->status == 'awaiting body'){
                $status = 'awaiting legs';
                $segment = 'body';
            } elseif ($monster->status == 'awaiting legs'){
                $status = 'complete';
                $segment = 'legs';
            } else {
                return back()->withError('Cannot save monster');
            }
            $monster->status = $status;
            $monster->save();

        } else {
            //Create new monster
            $monster = new Monster;
            $monster->name = 'Default name';
            $monster->status = 'awaiting body';
            $monster->save();

            $segment = 'head';
            $monster_id = $monster->id;
        }

        $monster_segment = new MonsterSegment;
        $monster_segment->segment = $segment;
        $monster_segment->image = $request->imgBase64;
        $monster_segment->monster_id = $monster_id;
        $monster_segment->save();

        return 'saved';
    }
}
