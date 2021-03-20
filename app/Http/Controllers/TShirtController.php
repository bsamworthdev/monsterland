<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\DBMonsterRepository;
use App\Repositories\DBUserRepository;
use App\Models\TShirt;
use App\Repositories\DBSettingsRepository;

class TShirtController extends Controller
{
    protected $DBMonsterRepo;
    protected $DBUserRepo;
    protected $DBSettingsRepo;

    public function __construct(Request $request, 
        DBMonsterRepository $DBMonsterRepo,
        DBUserRepository $DBUserRepo,
        DBSettingsRepository $DBSettingsRepo)
    {
        $this->DBMonsterRepo = $DBMonsterRepo;
        $this->DBUserRepo = $DBUserRepo;
        $this->DBSettingsRepo = $DBSettingsRepo;
    }

    public function index($monsterId=NULL)
    {
        $monster = $this->DBMonsterRepo->find($monsterId);

        if (Auth::check()){
            $user_id = Auth::User()->id;
            $user = $this->DBUserRepo->find($user_id);

            // $monsters = $this->DBMonsterRepo->getTopMonsters($user, $group_id);
            // if ($book_id) {
            //     $book = $this->DBBookRepo->getBook($user_id, $book_id);
            // }
            return view('tshirt',[
                'canUseStore' => $user->canUseStore,
                'monster' => $monster,
                'user_id' => $user_id
            ]);
        } else {
            $canUserStore = $this->DBSettingsRepo->everyoneCanUseStore();
            return view('tshirt',[
                'canUseStore' => $canUserStore,
                'monster' => $monster,
                'user_id' => 0
            ]);
        }
    }

    public function save(Request $request){
        $tShirt = new TShirt;
        $tShirt->monster_id= $request->monsterId;
        $tShirt->color= $request->color;
        $tShirt->gender= $request->gender;
        $tShirt->size= $request->size;
        $tShirt->position= $request->position;
        $tShirt->show_name= $request->includeName;
        $tShirt->entered_name= $request->enteredName;
        $tShirt->show_border= $request->includeBorder;
        $tShirt->design_code= $request->designCode;
        $tShirt->save();

        return $tShirt->id;
    }
}
