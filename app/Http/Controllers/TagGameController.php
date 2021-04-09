<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\DBMonsterRepository;
use App\Repositories\DBUserRepository;
use App\Repositories\DBTagRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class TagGameController extends Controller
{
    protected $DBMonsterRepo;
    protected $DBUserRepo;
    protected $DBTagRepo;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, 
        DBMonsterRepository $DBMonsterRepo,
        DBUserRepository $DBUserRepo,
        DBTagRepository $DBTagRepo)
    {
        $this->middleware(['auth','verified', function($request, $next) 
            use ($DBMonsterRepo, $DBUserRepo, $DBTagRepo){
            $this->DBMonsterRepo = $DBMonsterRepo;
            $this->DBUserRepo = $DBUserRepo;
            $this->DBTagRepo = $DBTagRepo;
            return $next($request);
        }]); 
        
    }

    public function index()
    {
        $user_id = Auth::User()->id;
        $user = $this->DBUserRepo->find($user_id);
        $hasSubmissionOnly = !in_array($user_id, [1,17,89]);
        $monsters = $this->DBMonsterRepo->getMonstersToTag($user, $hasSubmissionOnly);
        return view('tagGame', [
            'monsters' => json_encode($monsters)
        ]);
    }

    public function update(Request $request){
        $action = $request->action;
        $user_id = Auth::User()->id;

        if ($action=='savesubmission'){
            $monster_id = $request->monster_id;
            $name = $request->name;
            $this->DBTagRepo->saveSubmission($user_id, $monster_id, $name);
            if (count($this->DBTagRepo->getTagSubmissions($monster_id, $name)) == 2){
                $this->DBTagRepo->saveTag($monster_id, $name);
            }
        }
    }
}
