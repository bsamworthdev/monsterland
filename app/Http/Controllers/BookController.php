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

    public function index($group_id, $book_id=NULL)
    {
        if (Auth::check()){
            $user_id = Auth::User()->id;
            $user = $this->DBUserRepo->find($user_id,['groups']);

            $monsters = $this->DBMonsterRepo->getTopMonsters($user, $group_id);
            if ($book_id) {
                $book = $this->DBBookRepo->getBook($user_id, $book_id);
            }
            return view('bookBuilder',[
                'group_id' => $group_id,
                'monsters' => $monsters,
                'book_monsters' => isset($book) ? $book->monsters->pluck('id') : $monsters->pluck('id')
            ]);
        }
    }

    public function save(Request $request){
        if (Auth::check()){
            $user_id = Auth::User()->id;
            $group_id = $request->groupId;
            $monsters = $request->monsters;

            $group = $this->DBGroupRepo->getGroupById($group_id, $user_id);
            $book_id = $this->DBBookRepo->createBook($user_id, $group, $monsters);
            return $book_id;
        }
    }
}
