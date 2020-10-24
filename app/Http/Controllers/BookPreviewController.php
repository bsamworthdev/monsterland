<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Repositories\DBMonsterRepository;
use App\Repositories\DBUserRepository;
use App\Repositories\DBBookRepository;

class BookPreviewController extends Controller
{

    protected $DBMonsterRepo;
    protected $DBUserRepo;
    protected $DBBookRepo;

    public function __construct(Request $request, 
    DBMonsterRepository $DBMonsterRepo,
    DBUserRepository $DBUserRepo,
    DBBookRepository $DBBookRepo)
    {
        $this->DBMonsterRepo = $DBMonsterRepo;
        $this->DBUserRepo = $DBUserRepo;
        $this->DBBookRepo = $DBBookRepo;
    }
    public function index($group_id)
    {
        if (Auth::check()){
            $user_id = Auth::User()->id;
            $user = $this->DBUserRepo->find($user_id,['groups']);

            $monsters = $this->DBMonsterRepo->getTopMonsters($user, $group_id);
            return view('bookPreview',[
                'group_id' => $group_id,
                'monsters' => $monsters
            ]);
        }
    }
}
