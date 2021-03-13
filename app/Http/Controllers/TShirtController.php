<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\DBMonsterRepository;
use App\Repositories\DBUserRepository;
use App\Models\TShirt;

class TShirtController extends Controller
{
    protected $DBMonsterRepo;
    protected $DBUserRepo;

    public function __construct(Request $request, 
        DBMonsterRepository $DBMonsterRepo,
        DBUserRepository $DBUserRepo)
    {
        $this->DBMonsterRepo = $DBMonsterRepo;
        $this->DBUserRepo = $DBUserRepo;
    }

    public function index($monsterId=NULL)
    {
        if (Auth::check()){
            $user_id = Auth::User()->id;
            $user = $this->DBUserRepo->find($user_id);

            // $monsters = $this->DBMonsterRepo->getTopMonsters($user, $group_id);
            // if ($book_id) {
            //     $book = $this->DBBookRepo->getBook($user_id, $book_id);
            // }
            $monster = $this->DBMonsterRepo->find($monsterId);
            return view('tshirt',[
                'canUseStore' => $user->canUseStore,
                'monster' => $monster,
                'user_id' => $user_id
            ]);
        }
    }

    public function save(Request $request){
        $tShirt = new TShirt;
        $tShirt->monster_id= $request->monsterId;
        $tShirt->color= $request->color;
        $tShirt->gender= $request->gender;
        $tShirt->size= $request->size;
        $tShirt->show_name= $request->includeName;
        $tShirt->show_border= $request->includeBorder;
        $tShirt->design_code= $request->designCode;
        $tShirt->save();

        return $tShirt->id;
    }
}
