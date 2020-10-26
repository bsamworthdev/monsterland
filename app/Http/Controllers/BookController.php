<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Repositories\DBMonsterRepository;
use App\Repositories\DBUserRepository;
use App\Repositories\DBBookRepository;
use App\Repositories\DBGroupRepository;

class BookController extends Controller
{

    protected $DBMonsterRepo;
    protected $DBUserRepo;
    protected $DBBookRepo;
    protected $DBGroupRepo;

    public function __construct(Request $request, 
    DBMonsterRepository $DBMonsterRepo,
    DBUserRepository $DBUserRepo,
    DBBookRepository $DBBookRepo,
    DBGroupRepository $DBGroupRepo)
    {
        $this->DBMonsterRepo = $DBMonsterRepo;
        $this->DBUserRepo = $DBUserRepo;
        $this->DBBookRepo = $DBBookRepo;
        $this->DBGroupRepo = $DBGroupRepo;
    }

    public function index($group_id)
    {
        if (Auth::check()){
            $user_id = Auth::User()->id;
            $user = $this->DBUserRepo->find($user_id,['groups']);

            $monsters = $this->DBMonsterRepo->getTopMonsters($user, $group_id);
            return view('bookBuilder',[
                'group_id' => $group_id,
                'monsters' => $monsters
            ]);
        }
    }

    public function save(Request $request){
        if (Auth::check()){
            $user_id = Auth::User()->id;
            $group_id = $request->group_id;
            $monsters = $request->monsters;

            $group = $this->DBGroupRepo->getGroupById($group_id, $user_id);
            $group_name = $group->name;
            $book_id = $this->DBBookRepo->createBook($user_id, $group_name, $monsters);
            return $book_id;
        }
    }
}
