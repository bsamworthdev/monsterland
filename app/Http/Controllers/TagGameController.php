<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\DBMonsterRepository;
use App\Repositories\DBUserRepository;
use App\Repositories\DBTagRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\RedisService;

class TagGameController extends Controller
{
    protected $DBMonsterRepo;
    protected $DBUserRepo;
    protected $DBTagRepo;
    protected $RedisService;
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(Request $request, 
        DBMonsterRepository $DBMonsterRepo,
        DBUserRepository $DBUserRepo,
        DBTagRepository $DBTagRepo,
        RedisService $RedisService)
    {
        $this->DBMonsterRepo = $DBMonsterRepo;
        $this->DBUserRepo = $DBUserRepo;
        $this->DBTagRepo = $DBTagRepo;
        $this->RedisService = $RedisService;
        
    }

    public function index()
    {
        $top_scores = [
            'everyone_today'=>$this->DBTagRepo->getTopScore('today'),
            'everyone_ever'=>$this->DBTagRepo->getTopScore('ever')
        ];

        if (Auth::check()){
            $user_id = Auth::User()->id;
            $user = $this->DBUserRepo->find($user_id);

            $hasSubmissionOnly = !in_array($user_id, [1,17,89]);
            $top_scores = array_merge($top_scores,
                [
                    'user_ever'=>$this->DBTagRepo->getTopScore('ever', $user_id)
                ]
            );
            $monsters = $this->DBMonsterRepo->getMonstersToTag($user, $hasSubmissionOnly);
        } else {
            $user = NULL;
            $monsters = $this->DBMonsterRepo->getMonstersToTag($user);
        }

        return view('tagGame', [
            'user_name' => ( $user == NULL ? '' : $user->name ),
            'monsters' => json_encode($monsters),
            'top_scores' => json_encode($top_scores)
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
                $this->RedisService->set('stats_need_updating', true);
            }
        } elseif ($action=='savescore'){
            if (!$user_id) return false;

            $score = $request->score;
            if ($this->DBTagRepo->validateScore($user_id, $score)){
                $this->DBTagRepo->saveTagScore($user_id, $score);
            }
        }
    }
}
