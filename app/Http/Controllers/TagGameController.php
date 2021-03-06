<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Repositories\DBMonsterRepository;
use App\Repositories\DBUserRepository;
use App\Repositories\DBTagRepository;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Services\RedisService;
use App\Repositories\DBSettingsRepository;

class TagGameController extends Controller
{
    protected $DBMonsterRepo;
    protected $DBUserRepo;
    protected $DBTagRepo;
    protected $DBSettingsRepo;
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
        DBSettingsRepository $DBSettingsRepo,
        RedisService $RedisService)
    {
        $this->DBMonsterRepo = $DBMonsterRepo;
        $this->DBUserRepo = $DBUserRepo;
        $this->DBTagRepo = $DBTagRepo;
        $this->DBSettingsRepo = $DBSettingsRepo;
        $this->RedisService = $RedisService;
        
    }

    public function index()
    {
        $masterTaggers = $this->DBSettingsRepo->getMasterTaggers();

        $top_scores = [
            'everyone_today'=>$this->DBTagRepo->getTopScore('today', $masterTaggers),
            'everyone_ever'=>$this->DBTagRepo->getTopScore('ever', $masterTaggers)
        ];

        if (Auth::check()){
            $user_id = Auth::User()->id;
            $user = $this->DBUserRepo->find($user_id);

            $hasSubmissionOnly = !in_array($user_id, $masterTaggers);
            $top_scores = array_merge($top_scores,
                [
                    'user_ever'=>$this->DBTagRepo->getTopScore('ever', $masterTaggers, $user_id)
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
            'top_scores' => json_encode($top_scores),
            'logged_in' => ( $user == NULL ? 0 : 1 ),
            'is_patron' => ( $user == NULL ? 0 : $user->is_patron ),
            'has_used_app' => ( $user == NULL ? 0 : $user->has_used_app ),
        ]);
    }

    public function update(Request $request){
        $action = $request->action;

        $user_id = NULL;
        if (Auth::check()){
            $user_id = Auth::User()->id;
            $user = $this->DBUserRepo->find($user_id);
        }

        $session = $request->session();
        $session_id = $session->getId();

        if ($action=='savesubmission'){
            $monster_id = $request->monster_id;
            $name = $request->name;
            $this->DBTagRepo->saveSubmission($user_id, $session_id, $monster_id, $name);
            if (count($this->DBTagRepo->getTagSubmissions($monster_id, $name)) == 2){
                $this->DBTagRepo->saveTag($monster_id, $name);
                // $this->RedisService->set('stats_need_updating', true);
            }
        } elseif ($action=='savescore'){
            if (!$user_id) return false;

            $score = $request->score;
            if ($this->DBTagRepo->validateScore($user_id, $score)){
                $this->DBTagRepo->saveTagScore($user_id, $score);
                if (!$user->is_patron && !$user->has_used_app){
                    $this->DBTagRepo->awardTagPrize($user, $score);
                }
            }
        } elseif ($action=='saveskip'){

            $monster_id = $request->monster_id;
            $this->DBTagRepo->saveSkip($user_id, $monster_id);
        }
    }
}
