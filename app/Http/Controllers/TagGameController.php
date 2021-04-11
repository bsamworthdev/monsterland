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
        $this->DBMonsterRepo = $DBMonsterRepo;
        $this->DBUserRepo = $DBUserRepo;
        $this->DBTagRepo = $DBTagRepo;
        
    }

    public function index()
    {
        if (Auth::check()){
            $user_id = Auth::User()->id;
            $user = $this->DBUserRepo->find($user_id);

            $hasSubmissionOnly = !in_array($user_id, [1,17,89]);
            $monsters = $this->DBMonsterRepo->getMonstersToTag($user, $hasSubmissionOnly);
        } else {
            $user = NULL;
            $monsters = $this->DBMonsterRepo->getMonstersToTag($user);
        }

        return view('tagGame', [
            'monsters' => json_encode($monsters)
        ]);
    }

    public function update(Request $request){
        $action = $request->action;

        $user_id = NULL;
        if (Auth::check()){
            $user_id = Auth::User()->id;
        }

        $session = $request->session();
        $session_id = $session->getId();

        if ($action=='savesubmission'){
            $monster_id = $request->monster_id;
            $name = $request->name;
            $this->DBTagRepo->saveSubmission($user_id, $session_id, $monster_id, $name);
            if (count($this->DBTagRepo->getTagSubmissions($monster_id, $name)) == 2){
                $this->DBTagRepo->saveTag($monster_id, $name);
            }
        }
    }
}
